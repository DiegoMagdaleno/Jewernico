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

    function updateStates() {
        let pais = $('#pais-select').val();
        let url = pais === 'MX' ? MEXICO : USA;
        fetchStates(url);
    }

    $('#pais-select').on('change', function () {
        updateStates();
    });

    updateStates();
});
