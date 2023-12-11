import $ from 'jquery';
const axios = require('axios').default;

$(document).ready(function () {
    let products = new Array<any>();

    function updateButtonStatus() {
        const selectedCount = $('.checkbox:checked').length;
        console.log("Hello!");

        if (selectedCount === 0) {
            $("#edit-button").prop("disabled", true);
            $("#delete-button").prop("disabled", true);
            $("#edit-button").addClass("btn-disabled");
            $("#delete-button").addClass("btn-disabled");
        } else if (selectedCount === 1) {
            $("#edit-button").prop("disabled", false);
            $("#delete-button").prop("disabled", false);
            $("#edit-button").removeClass("btn-disabled");
            $("#delete-button").removeClass("btn-disabled");
        } else {
            $("#edit-button").prop("disabled", true);
            $("#delete-button").prop("disabled", false);
            $("#edit-button").addClass("btn-disabled");
            $("#delete-button").removeClass("btn-disabled");
        }
    }

    $('#check-all').change(function () {
        if ($(this).is(':checked')) {
            $('.checkbox').prop('checked', true);
        } else {
            $('.checkbox').prop('checked', false);
        }
        updateButtonStatus();
    });

    $('.checkbox').change(function () {
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $('#check-all').prop('checked', true);
        } else {
            $('#check-all').prop('checked', false);
        }

        if ($(this).is(':checked')) {
            $(this).closest('tr').addClass('selected');
            products.push($(this).val());
        } else {
            $(this).closest('tr').removeClass('selected');
            products.splice(products.indexOf($(this).val()), 1);
        }
        updateButtonStatus();
    });

    $('#add-button').click(function () {
        window.location.href = '/admin/products/add';
    });

    $('#edit-button').click(function () {
        window.location.href = '/admin/products/edit/' + products[0];
    });

    $('#delete-button').click(function () {
        let deleteModal = $('#delete-modal')[0] as HTMLDialogElement;
        deleteModal.showModal();
        axios.get('/api/products/' + products[0])
            .then(function (response) {
                let product = response.data.producto;
                $('#delete-modal #product-name').text(product.Nombre);
                $('#delete-modal #product-image').attr('src', product.Imagenes[0]);
            })
            .catch(function (error) {
                console.log(error);
            });
        $('#delete-modal #delete-cancel').click(function () {
            deleteModal.close();
        });
        $('#delete-modal #delete-confirm').click(function () {
            axios.delete('/api/products/' + products[0])
                .then(function (response) {
                    window.location.href = '/admin/products';
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
    });
});