import $ from 'jquery';
const axios = require('axios').default;

$(document).ready(function () {
    const MEXICO = "https://gist.githubusercontent.com/DiegoMagdaleno/f799e65ca72a271e5b79a24364c12a45/raw/b751c0ddb461c3a333c6410ffe98bf18ad07a874/mexico.json";
    const USA = "https://gist.githubusercontent.com/DiegoMagdaleno/63ff6fadeb49346c138f4c636b8fb8b3/raw/bd341c0b60570a4ed7b407bcf589efb2d8dc6c0b/united_states.json";

    function fetchStates(url) {
        $('#estado-select').attr('disabled', 'disabled');
        $('#estado-select').html('<option>Cargando...</option>');
        axios.get(url).then(function (response) {
            $('#estado-select').removeAttr('disabled');
            $('#estado-select').html('<option value="">Selecciona un estado</option>');
            response.data.forEach(function (estado) {
                $('#estado-select').append(`<option value="${estado.code}">${estado.name}</option>`);
            });
        }).catch(function (error) {
            console.log(error);
        });
    }

    function updateTax() {
        let subtotal = 0;
        $('.total-price-item').each(function () {
            subtotal += $(this).data('price');
        });
        let tax = subtotal * $('#pais-select').find(':selected').data('tax');
        $('#tax-display').text(`$ ${tax.toFixed(2)} MXN (Impuesto de: ${$('#pais-select').find(':selected').data('tax') * 100}%)`);
    }

    function updateStates() {
        let pais = $('#pais-select').val();
        let url = pais === 'MX' ? MEXICO : USA;
        fetchStates(url);
    }

    $('#pais-select').on('change', function () {
        updateStates();
        updateTax();
    });

    updateStates();

    let shippingCost = 89.00;

    let subtotal = 0;
    $('.total-price-item').each(function () {
        subtotal += $(this).data('price');
    });
    $('#subtotal-display').text(`$ ${subtotal.toFixed(2)}`);

    if (subtotal > 299) {
        $('#shipping-display').text('Gratis');
        $('#shipping-display').addClass('text-success');
        shippingCost = 0;
    } else {
        $('#shipping-display').text(`$ ${shippingCost.toFixed(2)} MXN`);
    }

    updateTax();

    let total = subtotal + parseFloat($('#tax-display').text().split(' ')[1]) + shippingCost;
    $('#total-display').text(`$ ${total.toFixed(2)}`);

    $('#pay-button').on('click', function () {
        $('#pay-button').attr('disabled', 'disabled');
        $('#pay-button').html("<span class='loading loading-infinity loading-lg'></span>");
        axios.post('/api/checkout', {
        }).then(function (response) {
            if (response.data.success) {
                $('#pay-button').removeAttr('disabled');
                $('#pay-button').html('Pagar');
                let toast = $('<div class="toast toast-end">' +
                    '<div class="alert alert-success">' +
                    '<span>Se ha realizado el pago</span>' +
                    '</div>' +
                    '</div>');
                $('body').append(toast);
                setTimeout(function () {
                    toast.fadeOut('slow', function () {
                        $(this).remove();
                    });
                }, 5000);
            }
        }).catch(function (error) {
            $('#pay-button').removeAttr('disabled');
            $('#pay-button').html('Pagar');
            let toast = $('<div class="toast toast-end">' +
                '<div class="alert alert-error">' +
                '<span>Ha ocurrido un error</span>' +
                '</div>' +
                '</div>');
            $('body').append(toast);
            setTimeout(function () {
                toast.fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 5000);
        });
    });

    $('#coupon-submit').on('click', function () {
        let coupon = $('#coupon').val();
        console.log(coupon);
        $('#coupon-submit').attr('disabled', 'disabled');
        $('#coupon-submit').html("<span class='loading loading-infinity loading-lg'></span>");
        axios.get(`/api/coupon/${coupon}`).then(function (response) {
            if (response.data.success) {
                $('#coupon-submit').html('<i class="fas fa-arrow-right text-white"></i>');
                $('#coupon').val();
                let discount = subtotal * response.data.data.Total;
                $('#discount-display').text(`- $ ${discount.toFixed(2)}`);
                let total = subtotal + parseFloat($('#tax-display').text().split(' ')[1]) + shippingCost - discount;
                $('#total-display').text(`$ ${total.toFixed(2)}`);
            }
        }).catch(function (error) {
            $('#coupon-submit').removeAttr('disabled');
            $('#coupon-submit').html('<i class="fas fa-arrow-right text-white"></i>');
            let toast = $('<div class="toast toast-end">' +
                '<div class="alert alert-error">' +
                '<span>El cupón no es válido</span>' +
                '</div>' +
                '</div>');
            $('body').append(toast);
            setTimeout(function () {
                toast.fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 5000);
        });
    });
});
