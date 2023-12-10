import $ from 'jquery';
const axios = require('axios').default;

$(document).ready(function () {
    $("#newsteller-email-submit").click(function (event) {
        event.preventDefault();
        let email = $("#newsteller-email").val();
        axios.post('/api/first_coupon', {
            solicitante: email
        }).then(function (response) {
            console.log(response);
            $("#newsteller-email").val("");
        }).catch(function (error) {
            console.log(error);
            $("#newsteller-email").val("");
        });
    });
});