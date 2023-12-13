import $ from 'jquery';
const axios = require('axios').default;

function handleClick() {
    let email = $('#correo').val();

    if (!validateEmail(email as string)) {
        alert('Por favor, ingrese un correo electrónico válido');
        return;
    }

    $('#enviar').prop('disabled', true).val('Cargando...');

    axios.post('/api/recover/verify_email', { correoElectronico: email })
        .then(response => {
            axios.post('/api/recover/get_question', { correoElectronico: email })
                .then(response => {
                    let question = response.data.question;
                    $('#enviar').before(`
            <div class="w-full pr-2 pb-3">
              <label for="respuesta" class="block text-lg text-gray-600 mb-2">${question}</label>
              <input type="text" id="respuesta" name="respuesta" class="input input-bordered w-full max-w-full mb-2" placeholder="Ingrese su respuesta" required>
            </div>
          `);
                    $('#correo').prop('disabled', true);
                    $('#enviar').prop('disabled', false).val('Enviar');
                    $('#enviar').val('Verificar').off('click').on('click', () => {
                        let answer = $('#respuesta').val();

                        if (!answer) {
                            showErrorToast('Por favor, ingrese su respuesta');
                            return;
                        }

                        $('#enviar').prop('disabled', true).val('Cargando...');

                        axios.post('/api/recover/verify_question', { correoElectronico: email, respuesta: answer })
                            .then(response => {
                                const passwordModal = $('#password-modal')[0] as HTMLDialogElement;
                                passwordModal.showModal();
                                $('#password-modal #password-submit').off('click').on('click', () => {
                                    let password = $('#password-modal #password').val();
                                    let confirmPassword = $('#password-modal #confirm-password').val();

                                    if (password !== confirmPassword) {
                                        showErrorToast('Las contraseñas no coinciden');
                                        return;
                                    }

                                    $('#password-modal #password-submit').prop('disabled', true).val('Cargando...');

                                    axios.post('/api/recover/update_password', { correoElectronico: email, password: password })
                                        .then(response => {
                                            passwordModal.close();
                                            window.location.href = '/login';
                                        })
                                        .catch(error => {
                                            showErrorToast(error.response.data.error);
                                            $('#password-modal #password-submit').prop('disabled', false).val('Cambiar');
                                        });
                                });
                            })
                            .catch(error => {
                                showErrorToast(error.response.data.error);
                                $('#enviar').prop('disabled', false).val('Verificar');
                            });
                    });
                })
                .catch(error => {
                    showErrorToast(error.response.data.error);
                    $('#enviar').prop('disabled', false).val('Enviar');
                });
        })
        .catch(error => {
            showErrorToast(error.response.data.error);
            $('#enviar').prop('disabled', false).val('Enviar');
        });
}

function validateEmail(email: string) {
    let regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return regex.test(email);
}

function showErrorToast(error: string) {
    let toast = $('<div class="toast toast-end">' +
        '<div class="alert alert-error">' +
        '<span>' + error + '</span>' +
        '</div>' +
        '</div>');
    $('body').append(toast);

    setTimeout(function () {
        toast.fadeOut('slow', function () {
            $(this).remove();
        });
    }, 5000);
}


$('#enviar').on('click', handleClick);