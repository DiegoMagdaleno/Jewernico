import $ from 'jquery';
const axios = require('axios').default;

$(document).ready(function () {
    $("#categories").change(function () {
        let category = $(this).val();
        console.log(category);
        $(".product").each(function () {
            if ($(this).data("category") == category || category == "all") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    })

    $('.shopping-cart').on('click', function () {
        const productId = $(this).closest('.product').data('pid');
        $(this).attr('disabled', 'disabled');
        $(this).html("<span class='loading loading-infinity loading-lg'></span>");
        axios.post('/api/cart/add', {
            idProducto: productId,
            cantidad: 1
        }).then(function (response) {
            if (response.data.success) {
                $('.shopping-cart').removeAttr('disabled');
                $('.shopping-cart').html('<i class="fa-solid fa-cart-shopping"></i>');
                let currentCount = parseInt($('#cart-indicator').text());
                $('#cart-indicator').text(currentCount + 1);
            }
        }).catch(function (error) {
            console.log(error);
        });
    });
});