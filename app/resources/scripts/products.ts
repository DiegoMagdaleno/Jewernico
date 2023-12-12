import $ from 'jquery';

$(document).ready(function () {
    $("#categories").change(function () {
        let category = $(this).val();
        console.log(category);
        $(".product").each(function () {
            if ($(this).data("category") == category || category == "all") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    })
});