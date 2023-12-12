import $ from 'jquery'
const axios = require('axios').default;

$(document).ready(function () {

    function clearForm(form: HTMLFormElement) {
        $(form).find('input').each(function () {
            $(this).val('');
        });
    }

    $('#contact-form').submit(function (event) {
        let email = $("#email").val();
        let name = $("#name").val();
        event.preventDefault();
        $('#submit').attr('disabled', 'disabled');
        $('#submit').addClass('btn-disabled');
        $('#submit').html('Enviando...');
        axios.post('/api/contact', {
            correoElectronico: email,
            nombre: name,
        }).then(function (response) {
            $('#submit').removeAttr('disabled');
            $('#submit').removeClass('btn-disabled');
            $('#submit').html('Enviar');
            let toast = $('<div class="toast toast-end">' +
                '<div class="alert alert-success">' +
                '<span>Se ha enviado el mensaje</span>' +
                '</div>' +
                '</div>');
            $('body').append(toast);
            clearForm($('#contact-form')[0] as HTMLFormElement);

            setTimeout(function () {
                toast.fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 5000);
        }).catch(function (error) {
            $('#submit').removeAttr('disabled');
            $('#submit').removeClass('btn-disabled');
            $('#submit').html('Enviar');
            let toast = $('<div class="toast toast-end">' +
                '<div class="alert alert-error">' +
                '<span>Ha ocurrido un error</span>' +
                '</div>' +
                '</div>');
            $('body').append(toast);
            clearForm($('#contact-form')[0] as HTMLFormElement);

            setTimeout(function () {
                toast.fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 5000);
        });
    });
});
