const axios = require('axios').default;
import $ from 'jquery';

$(document).ready(function () {
    $('#login-form').submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this as HTMLFormElement);

        axios.post('/api/login', {
            correoElectronico: formData.get('correoElectronico'),
            password: formData.get('password'),
            remember: formData.get('remember'),
            captcha: formData.get('captcha')
        })
            .then(function (response) {
                return axios.post('/api/propagate', response.data);
            }).then(function (response) {
                window.location.href = '/';
            })
            .catch(function (error) {
                let errorMsg = error.response.data.error;
                let toast = $('<div class="toast toast-end">' +
                    '<div class="alert alert-error">' +
                    '<span>' + errorMsg + '</span>' +
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