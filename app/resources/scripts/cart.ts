import $ from 'jquery';
const axios = require('axios').default;

$(document).ready(function () {
    let subtotal = 0;

    $("#checkout-button").on("click", function () {
        let modal = $("#checkout-modal")[0] as HTMLDialogElement;
        modal.showModal();
    });

    function updateSubtotal() {
        subtotal = 0;
        $(".product").each(function () {
            const $product = $(this);
            const precioProducto = parseFloat($product.data("price"));
            const $quantityInput = $product.find("input[type='number']");
            let quantity = parseInt($quantityInput.val() as string);

            let totalPrice = precioProducto * quantity;
            const $priceTotalItem = $product.find(".price-total-item");
            $priceTotalItem.text(`$${totalPrice.toFixed(2)} MXN`);
            subtotal += totalPrice;
        });
        $("#price-subtotal").text(`$${subtotal.toFixed(2)} MXN`);
    }

    $(".product").each(function () {
        const $product = $(this);
        const stock = parseInt($product.find("input[type='number']").attr('max'));

        function updateTotalPrice() {
            let quantity = parseInt($product.find("input[type='number']").val() as string);
            let precioProducto = parseFloat($product.data("price"));
            let $quantityInput = $product.find("input[type='number']");
            let $plusButton = $product.find(".plus");
            let $minusButton = $product.find(".minus");

            if (quantity <= 0 || isNaN(quantity)) {
                quantity = 1;
                $quantityInput.val(quantity);
            } else if (quantity > stock) {
                quantity = stock;
                $quantityInput.val(quantity);
            }

            let totalPrice = precioProducto * quantity;
            const $priceTotalItem = $product.find(".price-total-item");
            $priceTotalItem.text(`$${totalPrice.toFixed(2)} MXN`);
            updateSubtotal();

            console.log(quantity);
            
            if (quantity >= stock) {
                $plusButton.attr("disabled", "disabled");
            } else {
                $plusButton.removeAttr("disabled");
            }

            if (quantity <= 1) {
                $minusButton.attr("disabled", "disabled");
            } else {
                $minusButton.removeAttr("disabled");
            }
        }

        updateTotalPrice();

        function updateQuantityServerSide() {
            const productId = $product.data("pid");
            const quantity = parseInt($product.find("input[type='number']").val() as string);
            axios.post('/api/cart/update', {
                idProducto: productId,
                cantidad: quantity
            }).then(function (response) {
                if (response.data.success) {
                    $('#cart-indicator').text(response.data.cartCount);
                    let toast = $('<div class="toast toast-end">' +
                        '<div class="alert alert-success">' +
                        '<span>Se ha actualizado la cantidad</span>' +
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
                console.log(error);
            });
        }

        $product.find("input[type='number']").on("change", function () {
            updateTotalPrice();
            updateQuantityServerSide();
        });

        $product.find(".plus").on("click", function () {
            let quantity = parseInt($product.find("input[type='number']").val() as string);
            quantity++;
            $product.find("input[type='number']").val(quantity);
            updateTotalPrice();
            updateQuantityServerSide();
        });

        $product.find(".minus").on("click", function () {
            let quantity = parseInt($product.find("input[type='number']").val() as string);
            if (quantity > 1) {
                quantity--;
                $product.find("input[type='number']").val(quantity);
                updateTotalPrice();
                updateQuantityServerSide();
            }
        });
        $product.find(".remove").on("click", function () {
            const productId = $product.data("pid");
            $(this).attr('disabled', 'disabled');
            $(this).html("<span class='loading loading-infinity'></span>");
            console.log(productId);
            axios.post('/api/cart/remove', {
                idProducto: productId
            }).then(function (response) {
                if (response.data.success) {
                    $product.next().remove();
                    $product.remove();
                    $('#cart-indicator').text(response.data.cartCount);
                    updateSubtotal();
                }
            }).catch(function (error) {
                console.log(error);
            });
        });
    });
    updateSubtotal();
});
