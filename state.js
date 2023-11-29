import { OPTIONS } from "./const.js";
import { ArrayEmitter } from "./listenarray.js";

export let state = {
    activeTab: 0,
    selectedOption: OPTIONS[0][0],
    mode: "Consultas",
    checkedBoxes: new ArrayEmitter(),
};