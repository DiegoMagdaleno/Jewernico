import $ from 'jquery';
const axios = require('axios').default;

$(document).ready(function () {
    $("#submit-button").click(function (event) {
        let formValid = true;
        $("#register-form input[required]").each(function () {
            if (!($(this).val() as string).trim()) {
                $(this).addClass('input-error');
                $('label[for="' + $(this).attr('id') + '"]').addClass('text-red-500');
                formValid = false;
            } else {
                $(this).removeClass('input-error');
                $('label[for="' + $(this).attr('id') + '"]').removeClass('text-red-500');
            }
        });

        if ($('#contraseña').val() !== $('#confirmar-contraseña').val()) {
            let toast = $('<div class="toast toast-end">' +
                '<div class="alert alert-error">' +
                '<span>Las contraseñas no coinciden</span>' +
                '</div>' +
                '</div>');

            $('body').append(toast);

            setTimeout(function () {
                toast.fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 5000);

            formValid = false;
        }

        if (formValid) {
            let formData = new FormData($("#register-form")[0] as HTMLFormElement);
            const questionModal = $("#question-modal")[0] as HTMLDialogElement;
            questionModal.addEventListener('close', function () {
                event.preventDefault();
            });
            questionModal.showModal();
            $("#question-modal #question-submit").click(function () {
                const questionData = new FormData($("#question-modal #question-form")[0] as HTMLFormElement);
                formData.append('idPregunta', questionData.get('id-pregunta') as string);
                formData.append('respuesta', questionData.get('respuesta') as string);
                axios.post('/api/register', formData)
                    .then(function (response) {
                        window.location.href = '/login';
                    })
                    .catch(function (error) {
                        questionModal.close();
                        $("#register-form input").each(function () {
                            $(this).val("");
                        });
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
            })
        }
    });
});