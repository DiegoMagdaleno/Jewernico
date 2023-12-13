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

    console.log(subtotal);

    updateTax();

    console.log(subtotal);

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
                let subtotal = parseFloat($('#subtotal-display').text().split(' ')[1]);
                let address = `${$('#calle').val()} ${$('#numeroExterior').val()} ${$('#numeroInterior').val()} ${$('#codigoPostal').val()} ${$('#estado-select').find(':selected').text()}, ${$('#pais-select').find(':selected').text()}`
                let details = $("#checkout-details")[0] as HTMLDialogElement;
                $('#checkout-details #subtotal-summary-display').text(`$ ${subtotal.toFixed(2)} MXN`);
                $('#checkout-details #shipping-summary-display').text(`$ ${shippingCost.toFixed(2)} MXN`);
                $('#checkout-details #tax-summary-display').text(`$ ${parseFloat($('#tax-display').text().split(' ')[1]).toFixed(2)} MXN`);
                $('#checkout-details #total-summary-display').text(`$ ${total.toFixed(2)} MXN`);
                $('#checkout-details #discount-summary-display').text(`$ ${parseFloat($('#discount-display').text().split(' ')[1]).toFixed(2)} MXN`);
                $('#checkout-details #address-summary-display').text(address);
                details.showModal();

                let paymentMethod = "Tarjeta de crédito";
                let tax = parseFloat($('#tax-display').text().split(' ')[1]);
                let discount = parseFloat($('#discount-display').text().split(' ')[1]);
                let products = [];
                $(".producto-info").each(function () {
                    let name = $(this).find(".nombre-producto").data('nombre');
                    let quantity = $(this).find(".cantidad-producto").data('cantidad');
                    let price = $(this).find(".subtotal-producto").data('price');
                    let product = { Nombre: name, Cantidad: quantity, Precio: price };
                    products.push(product);
                    console.log(product);
                });

                if (discount === undefined) {
                    discount = 0;
                }

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
                        $('#checkout-details #okay').removeAttr('disabled');
                        $('#checkout-details #okay').html('Aceptar');
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
                        axios.post('/api/receipt/pdf',data, {
                            responseType: 'blob',
                        }).then(function (response) {
                            let pdfBlob = new Blob([response.data], { type: 'application/pdf' });
                            let pdfUrl = window.URL.createObjectURL(pdfBlob);
                        
                            let toast = $('<div class="toast toast-end">' +
                                '<div class="alert alert-success">' +
                                '<span>Se ha generado el recibo. <a href="' + pdfUrl + '" download="receipt.pdf">Descargar aquí</a>.</span>' +
                                '</div>' +
                                '</div>');
                            
                            $('body').append(toast);
                            
                            ($("#checkout-details")[0] as HTMLDialogElement).close();

                            setTimeout(function () {
                                toast.fadeOut('slow', function () {
                                    $(this).remove();
                                    window.location.href = '/products';
                                });
                            }, 25000);
                        
                        }).catch(function (error) {
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
                    }).catch(function (error) {
                        $('#checkout-details #okay').removeAttr('disabled');
                        $('#checkout-details #okay').html('Aceptar');
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
