import { Argument } from "./models.js";
import { CaseConverter } from "./case_convert.js";
import { state } from "./state.js";

export function fillQuerySelector(options) {
    $("#query-selector").empty();
    for (let i = 0; i < options.length; i++) {
        let option = options[i];
        $("#query-selector").append(`<option value="${option.value}" ${options[i].toDataHTMLTag()}>${option.text}</option>`);
    }
}

export function getArgumentsOfSelectedOption() {
    let htmlDataArgs = $(state.selectedOption).data('args');
    if (htmlDataArgs === undefined) {
        return [];
    }
    let args = htmlDataArgs === "" ? [] : htmlDataArgs.split(" ").map((arg) => Argument.fromPropCompatString(arg));
    return args;
}

export function createForm(data) {
    let form = $("<form></form>");
    form.attr("id", "abc-form-data");
    form.addClass("form-control");
    $.each(data, function (index, item) {
        if (item.Extra == "auto_increment") {
            return;       
        }
        let label = $("<label></label>");
        label.text(item.Field);
        label.addClass("label");

        let input = $("<input></input>");
        input.attr("name", item.Field);

        if (item.Type === "date") {
            input.attr("type", "date");
        } else {
            if (item.Type.startsWith("int")) {
                input.attr("type", "number");

                if (item.Type.includes("unsigned")) {
                    input.attr("min", "0");
                }
            } else if (item.Type.startsWith("varchar")) {
                input.attr("type", "text");
                let length = item.Type.match(/\d+/)[0];
                input.attr("maxlength", length);
            }

            if (item.Null == "NO") {
                input.attr("required", "true");
            }

            if (item.Field == "Password") {
                input.attr("type", "password");
            }

            input.addClass("input");
            input.addClass("input-bordered");
            input.addClass("w-full");
            input.addClass("max-w-xs");
        }


        form.append(label);
        form.append(input);
        form.append("<br>");
    });
    return form;
}

function convertToCamelCase(inputString) {
    return inputString.charAt(0).toLowerCase() + inputString.slice(1);
}

export function getFormValuesAsJSON(formId) {
    const values = {};
    $("#" + formId).serializeArray().forEach((field) => {
        values[convertToCamelCase(field.name)] = field.value;
    });
    return JSON.stringify(values);
}