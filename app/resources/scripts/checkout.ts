import $ from 'jquery';
const axios = require('axios').default;

const MEXICO_URL = "https://gist.githubusercontent.com/DiegoMagdaleno/f799e65ca72a271e5b79a24364c12a45/raw/b751c0ddb461c3a333c6410ffe98bf18ad07a874/mexico.json";
const USA_URL = "https://gist.githubusercontent.com/DiegoMagdaleno/63ff6fadeb49346c138f4c636b8fb8b3/raw/bd341c0b60570a4ed7b407bcf589efb2d8dc6c0b/united_states.json";

$(document).ready(function () {
    const $estadoSelect = $('#estado-select');
    const $paisSelect = $('#pais-select');

    function fetchStates(url) {
        $estadoSelect.attr('disabled', 'disabled').html('<option>Cargando...</option>');
        axios.get(url).then(function (response) {
            $estadoSelect.prop('disabled', false).html('<option value="">Selecciona un estado</option>');
            response.data.forEach(function (estado) {
                $('#estado-select').append(`<option value="${estado.code}">${estado.name}</option>`);
            });
        }).catch(function (error) {
            console.log(error);
        });
    }

    function getTax() {
        return $('#pais-select').find(':selected').data('tax');
    }

    function calculateTax() {
        return calculateSubtotal() * getTax();
    }

    function calculateSubtotal() {
        let subtotal = 0;
        $('.total-price-item').each(function () {
            subtotal += $(this).data('price');
        });
        return subtotal;
    }

    function updateSummaryDisplay(cupon, shippingCost, tax) {
        const subtotal = calculateSubtotal();
        const totalCoupon = subtotal * cupon;
        const taxAmount = subtotal * tax;
        const total = subtotal + taxAmount + shippingCost - cupon;
        $('#shipping-display').text(subtotal > 299 ? 'Gratis' : `$ ${shippingCost.toFixed(2)} MXN`).toggleClass('text-success', subtotal > 299);
        $('#tax-display').text(`$ ${taxAmount.toFixed(2)} MXN (Impuesto de: ${tax * 100} %)`);
        $('#total-display').text(`$ ${total.toFixed(2)} MXN`);
        $('#discount-display').text(`$ -${totalCoupon.toFixed(2)} MXN`);
    }

    function showMessageToast(message, type) {
        const toast = $(`<div class="toast toast-end"><div class="alert alert-${type}"><span>${message}</span></div></div>`);
        $('body').append(toast);
        setTimeout(() => toast.fadeOut('slow', () => toast.remove()), 5000);
    }

    function updateStates() {
        let pais = $('#pais-select').val();
        let url = pais === 'MX' ? MEXICO_URL : USA_URL;
        fetchStates(url);
    }

    function disablePayButton() {
        $('#pay-button').attr('disabled', 'disabled').html("<span class='loading loading-infinity loading-lg'></span>");
    }

    function showDetailsModal() {
        const details = $("#checkout-details")[0] as HTMLDialogElement;
        details.showModal();
    }

    function buildAddress() {
        return `${$('#calle').val()} No. ${$('#numeroExterior').val()} Int. ${$('#numeroInterior').val()}, ${$('#codigoPostal').val()}, ${$('#estado-select').find(':selected').text()}, ${$('#pais-select').find(':selected').text()}`
    }

    function getProductList() {
        let products = [];
        $(".producto-info").each(function () {
            let name = $(this).find(".nombre-producto").data('nombre');
            let quantity = $(this).find(".cantidad-producto").data('cantidad');
            let price = $(this).find(".subtotal-producto").data('price');
            let product = { Nombre: name, Cantidad: quantity, Precio: price };
            products.push(product);
        });
        return products;
    }

    function getDiscount() {
        let discount = parseFloat($('#discount-display').text().split(' ')[1]);
        if (discount === undefined || isNaN(discount)) {
            return 0;
        } else {
            return discount;
        }
    }

    function getPaymentMethod() {
        const url = window.location.href;
        if (url.includes('oxxo')) {
            return "OXXO";
        } else {
            return "Tarjeta de crédito";
        }
    }

    function isFormValid() {
        let formValid = true;
        $("input[required]").each(function () {
            if (!($(this).val() as string).trim()) {
                $(this).addClass('input-error');
                $('label[for="' + $(this).attr('id') + '"]').addClass('text-red-500');
                formValid = false;
            } else {
                $(this).removeClass('input-error');
                $('label[for="' + $(this).attr('id') + '"]').removeClass('text-red-500');
            }
        });

        if ($('#estado-select').val() === null || $('#estado-select').val() === '') {
            $('#estado-select').addClass('input-error');
            formValid = false;
        } else {
            $('#estado-select').removeClass('input-error');
        }
        if ($('#pais-select').val() === null || $('#pais-select').val() === '') {
            $('#pais-select').addClass('input-error');
            formValid = false;
        } else {
            $('#pais-select').removeClass('input-error');
        }

        return formValid;
    }

    $paisSelect.on('change', updateStates);

    updateStates();

    let shippingCost = 89.00;

    updateSummaryDisplay(0, shippingCost, getTax());

    const $payButton = $('#pay-button');

    $payButton.on('click', function () {
        if (!isFormValid()) {
            showMessageToast('Completa todos los campos', 'error');
            return;
        }
        disablePayButton();
        axios.post('/api/checkout', {
        }).then(function (response) {
            if (response.data.success) {
                $payButton.html('Pagar');
                showMessageToast('Pago realizado', 'success');
                let subtotal = calculateSubtotal();
                let address = buildAddress();
                let tax = calculateTax();
                let discount = getDiscount();
                let paymentMethod = getPaymentMethod();
                let products = getProductList();
                let total = subtotal + tax + shippingCost - discount;

                $('#checkout-details #subtotal-summary-display').text(`$ ${subtotal.toFixed(2)} MXN`);
                $('#checkout-details #shipping-summary-display').text(`$ ${shippingCost.toFixed(2)} MXN`);
                $('#checkout-details #tax-summary-display').text(`$ ${tax.toFixed(2)} MXN`);
                $('#checkout-details #total-summary-display').text(`$ ${total.toFixed(2)} MXN`);
                $('#checkout-details #discount-summary-display').text(`$ ${discount.toFixed(2)} MXN`);
                $('#checkout-details #address-summary-display').text(address);
                showDetailsModal();

                $('#checkout-details #okay').on('click', function () {
                    $('#checkout-details #okay').attr('disabled', 'disabled');
                    $('#checkout-details #okay').html("<span class='loading loading-infinity loading-lg'></span>");
                    let data = {
                        metodoPago: paymentMethod,
                        impuesto: tax,
                        cupon: discount,
                        subtotal: subtotal,
                        total: total,
                        direccion: address,
                        productos: products,
                        envio: shippingCost,
                    }
                    axios.post('/api/receipt', data).then(function (response) {
                        showMessageToast('Se ha generado el recibo', 'success');
                        axios.post('/api/receipt/pdf', data, {
                            responseType: 'blob',
                        }).then(function (response) {
                            let pdfBlob = new Blob([response.data], { type: 'application/pdf' });
                            let pdfUrl = window.URL.createObjectURL(pdfBlob);

                            let link = document.createElement('a');
                            link.href = pdfUrl;
                            link.setAttribute('download', 'recibo.pdf');
                            document.body.appendChild(link);
                            link.click();
                            window.location.href = '/';
                        }).catch(function (error) {
                            $('#checkout-details #okay').removeAttr('disabled');
                            $('#checkout-details #okay').html('Aceptar');
                            showMessageToast('Ha ocurrido un error', 'error');
                        });
                    }).catch(function (error) {
                        $('#checkout-details #okay').removeAttr('disabled');
                        $('#checkout-details #okay').html('Aceptar');
                        showMessageToast('Ha ocurrido un error', 'error');
                    });
                });
            }
        }).catch(function (error) {
            $payButton.removeAttr('disabled');
            $payButton.html('Pagar');
            showMessageToast('Ha ocurrido un error', 'error');
        });
    });

    const $couponSubmit = $('#coupon-submit');
    const $coupon = $('#coupon');

    $couponSubmit.on('click', function () {
        $couponSubmit.attr('disabled', 'disabled').html("<span class='loading loading-infinity loading-lg'></span>");
        axios.get(`/api/coupon/${$coupon.val()}`).then(function (response) {
            if (response.data.success) {
                $couponSubmit.html('<i class="fas fa-arrow-right text-white"></i>');
                updateSummaryDisplay(response.data.data.Total, shippingCost, getTax());
            }
        }).catch(function (error) {
            $('#coupon-submit').removeAttr('disabled');
            $('#coupon-submit').html('<i class="fas fa-arrow-right text-white"></i>');
            $('#coupon').val();
            showMessageToast('Cupón inválido', 'error');
        });
    });
});
