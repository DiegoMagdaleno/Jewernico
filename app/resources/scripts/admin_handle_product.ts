import Dropzone from 'dropzone';
import $ from 'jquery';
const axios = require('axios').default;


$(document).ready(function () {
    let pathsArray = [];

    const myDropzone = new Dropzone('div#file-upload', {
        url: '/api/upload/files',
        maxFiles: 3,
        acceptedFiles: 'image/*',
        init: function () {
            this.on('success', function (file, response) {
                pathsArray.push(response['path']);
            });

            this.on('addedfile', function (file) {
                file.previewElement.addEventListener('click', function () {
                    myDropzone.removeFile(file);
                    file.previewElement.remove()
                    pathsArray.splice(pathsArray.indexOf(file.name), 1);
                });
            });
        }
    });

    let productId = $("main").data('product-id');
    let endpoint = '/api/products';
    if (productId) {
        axios.get('/api/products/' + productId).then(function (response) {
            let images = response.data.producto.Imagenes;
            if (!Array.isArray(images)) {
                images = [images];
            }
            if (images.length > 0) {
                images.forEach(image => {
                    let nameArray = image.split('/');
                    let name = nameArray[nameArray.length - 1];
                    let mockFile = { name: name, size: 0 };
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.emit("thumbnail", mockFile, image);
                    myDropzone.emit("complete", mockFile);
                    pathsArray.push(image);
                });
            }
        });
        endpoint += '/' + productId;
    }

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
            axios.post(endpoint, formData)
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

