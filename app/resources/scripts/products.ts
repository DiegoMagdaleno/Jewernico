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
                $('.product').each(function () {
                    if ($(this).data('stock') == 0) {
                        $(this).find('.shopping-cart').attr('disabled', 'disabled');
                    }
                });
                let currentCount = parseInt($('#cart-indicator').text());
                if (isNaN(currentCount)) {
                    currentCount = 0;
                }
                $('#cart-indicator').text(currentCount + 1);
            }
        }).catch(function (error) {
            console.log(error);
        });
    });

    $('#search-price').on('click', function () {
        let minPrice = $('#min-price').val();
        let maxPrice = $('#max-price').val();
        $(".product").each(function () {
            let price = $(this).data("price");
            if (price >= minPrice && price <= maxPrice) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});