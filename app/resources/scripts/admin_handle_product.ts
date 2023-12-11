import Dropzone from 'dropzone';
import $ from 'jquery';
const axios = require('axios').default;


$(document).ready(function () {
    let pathsArray = [];

    const myDropzone = new Dropzone('div#file-upload', {
        url: '/api/upload/files',
        init: function () {
            this.on('success', function (file, response) {
                pathsArray.push(response['path']);
            });

            this.on('addedfile', function (file) {
                file.previewElement.addEventListener('click', function () {
                    myDropzone.removeFile(file);
                    file.previewElement.remove();
                    pathsArray = pathsArray.filter(path => path !== file.name);
                });
            });
        }
    });

    $("#product-form").submit(function (event) {
        event.preventDefault();
    });

    $("#save-button").click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        let formValid = true;
        $("#product-form input[required]").each(function () {
            if (!($(this).val() as string).trim()) {
                $(this).addClass('input-error');
                $('label[for="' + $(this).attr('id') + '"]').addClass('text-red-500');
                formValid = false;
            } else {
                $(this).removeClass('input-error');
                $('label[for="' + $(this).attr('id') + '"]').removeClass('text-red-500');
            }
        });

        if (formValid) {
            let formData = new FormData($("#product-form")[0] as HTMLFormElement);
            formData.append('imagenes', JSON.stringify(pathsArray));
            console.log(formData);
            axios.post('/api/products', formData)
                .then(function (response) {
                    window.location.href = '/admin/products';
                })
                .catch(function (error) {
                    $("#product-form input").each(function () {
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
        }
    });
});

