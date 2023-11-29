import { fillQuerySelector, getArgumentsOfSelectedOption, createForm, getFormValuesAsJSON } from "./util.js";
import { OPTIONS } from "./const.js";
import { state } from "./state.js";
import { CaseConverter } from "./case_convert.js";

function renderUI() {
    $("#query-result").empty();
    if (state.mode === "abc") {
        $("#select-tabs").hide();
        $("#about-current-view-title").text("Altas, Bajas y Cambios");
        $("#about-current-view-instructions").text("Selecciona la tabla en la que deseas realizar una operación");
        $("#query-result").empty();
        $("#abc-controls").show();
        fillQuerySelector(OPTIONS[0]);
    } else {
        $("#about-current-view-title").text("Consultas");
        $("#about-current-view-instructions").text("Selecciona una consulta por realizar");
        $("#select-tabs").show();
        $("#abc-controls").hide();
        fillQuerySelector(OPTIONS[state.activeTab]);
    }
}

function updateResultView(data) {
    $("#query-result").empty();
    $("#query-result").html(data);

    $("#query-result table").addClass("table table-xs");

    if (state.mode === "abc") {
        $("#query-result table thead tr").each(function () {
            $(this).prepend($('<th>'));
        });

        $("#query-result table tbody tr").each(function () {
            let checkbox = $('<input>', {
                type: 'checkbox',
                class: 'checkbox',

            });

            let label = $('<label>').prepend(checkbox);

            $(this).prepend($('<td>').prepend(label));

            checkbox.on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').addClass('selected');
                    state.checkedBoxes.push($(this).closest('tr').find('td:nth-child(2)').text());
                } else {
                    $(this).closest('tr').removeClass('selected');
                    state.checkedBoxes.splice(state.checkedBoxes.indexOf($(this).closest('tr').find('td:nth-child(2)').text()), 1);
                    console.log(state.checkedBoxes);
                }
            });
        });
    }
}

function getDataOfTable(query) {
    let url = `http://localhost/php_course/BaseDeDatos/php/${query}`;
    let args = getArgumentsOfSelectedOption();
    $("#query-args input").each(function (index, element) {
        let arg = args[index];
        url += "/" + $(element).val();
    });
    $.ajax({
        url: url,
        type: "GET",
        success: function (data) {
            updateResultView(data);
        },
    })
}


$("#query-selector").on("change", function () {
    let selectedOption = $(this).find(":selected");
    state.selectedOption = selectedOption;
    let args = getArgumentsOfSelectedOption();
    $("#query-args").empty();
    $("#query-result").empty();
    state.checkedBoxes.clear();
    if (args.length === 0) {
        return;
    }
    for (let i = 0; i < args.length; i++) {
        let arg = args[i];
        $("#query-args").append(`
            <div class="form-group">
                <label for="${arg.name}">${arg.text}</label>
                <input type="${arg.type}" class="form-control" id="${arg.name}" placeholder="${arg.text}" required>
            </div>
        `);
    }
});

$(".tab").on("click", function () {
    $(".tab").removeClass("tab-active");
    $(this).addClass("tab-active");
    state.activeTab = $(this).data('num');
    fillQuerySelector(OPTIONS[state.activeTab]);
    $("#query-result").empty();
    $("#query-selector").trigger("change");
});


$(".join-item").on("click", function () {
    $(".join-item").removeClass("btn-primary");
    $(this).addClass("btn-primary");
    state.mode = $(this).attr("name");
    renderUI();
});

state.checkedBoxes.onArrayChangeEvent(function (newState) {
    if (newState.length === 0) {
        $("#abc-update").addClass("btn-disabled");
        $("#abc-delete").addClass("btn-disabled");
    } else if (newState.length === 1) {
        $("#abc-update").removeClass("btn-disabled");
        $("#abc-delete").removeClass("btn-disabled");
    } else {
        $("#abc-update").addClass("btn-disabled");
        $("#abc-delete").removeClass("btn-disabled");

    }
});

$("#abc-add").on("click", function () {
    let modal = $("#abc-dialog")[0].showModal();
    $("#abc-dialog-title").text("Agregar");
    let query = $("#query-selector").val();
    let url = `http://localhost/php_course/BaseDeDatos/php/schema/${query}`;
    $.ajax({
        url: url,
        type: "GET",
        success: function (data) {
            $("#abc-form").empty();
            $("#abc-form").append(createForm(data));
            let sendButton = $("<button></button>");
            sendButton.text("Agregar");
            sendButton.addClass("btn");
            sendButton.addClass("btn-primary");
            sendButton.attr("id", "abc-send");
            sendButton.attr("type", "button");
            sendButton.on("click", function () {
                let requiredFields = $("#abc-form-data input[required]");
                let isFormValid = true;

                requiredFields.each(function(){
                    if ($(this).val() === "") {
                        isFormValid = false;
                        $(this).addClass("input-error");
                    } else {
                        $(this).removeClass("input-error");
                    }
                })

                if (!isFormValid) {
                    console.log("Form is not valid");
                } else {
                    let formValues = getFormValuesAsJSON("abc-form-data");
                    let url = `http://localhost/php_course/BaseDeDatos/php/${query}`;
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formValues,
                        contentType: "application/json",
                        success: function (data) {
                            getDataOfTable(query);
                            $("#close-modal-button").trigger("click");
                        },
                    })
                }
            });
            
            $("#abc-form").append(sendButton);            
        },
    })
});


$("#abc-delete").on("click", function () {
    let query = $("#query-selector").val();
    for (let i = 0; i < state.checkedBoxes.length; i++) {
        let idToDelete = state.checkedBoxes[i];
        let url = `http://localhost/php_course/BaseDeDatos/php/${query}/${idToDelete}`;
        $.ajax({
            url: url,
            type: "DELETE",
            success: function (data) {
                getDataOfTable(query);
                updateResultView(data);
            },
        })
    }
});

$("#abc-update").on("click", function () {
    let modal = $("#abc-dialog")[0].showModal();
    $("#abc-dialog-title").text("Actualizar");
    let query = $("#query-selector").val();
    let url = `http://localhost/php_course/BaseDeDatos/php/schema/${query}`;
    $.ajax({
        url: url,
        type: "GET",
        success: function (data) {
            $("#abc-form").empty();
            $("#abc-form").append(createForm(data));
            let sendButton = $("<button></button>");
            sendButton.text("Actualizar");
            sendButton.addClass("btn");
            sendButton.addClass("btn-primary");
            sendButton.attr("id", "abc-send");
            sendButton.attr("type", "button");
            $("#abc-form").append(sendButton);
            let id = state.checkedBoxes[0];
            let url = `http://localhost/php_course/BaseDeDatos/php/${query}/${id}`;
            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {
                    $.each(data[0], function (key, value) {
                        $(`#abc-form-data input[name="${key}"]`).val(value);
                    });
                },
            });
            sendButton.on("click", function () {
                console.log("Clicked");
                let requiredFields = $("#abc-form-data input[required]");
                let isFormValid = true;

                requiredFields.each(function(){
                    if ($(this).val() === "") {
                        isFormValid = false;
                        $(this).addClass("input-error");
                    } else {
                        $(this).removeClass("input-error");
                    }
                })

                if (!isFormValid) {
                    console.log("Form is not valid");
                } else {
                    let formValues = getFormValuesAsJSON("abc-form-data");
                    let url = `http://localhost/php_course/BaseDeDatos/php/${query}/${id}`;
                    $.ajax({
                        url: url,
                        type: "PUT",
                        data: formValues,
                        contentType: "application/json",
                        success: function (data) {
                            getDataOfTable(query);
                            state.checkedBoxes.clear();
                            $("#close-modal-button").trigger("click");
                        },
                    })
                }
            });
        }
    })
});


$("#make-query").on("click", function () {
    getDataOfTable($("#query-selector").val());
});

fillQuerySelector(OPTIONS[0]);
state.selectedOption = OPTIONS[0][0];
state.activeTab = 0;
state.mode = "Consultas";

renderUI();