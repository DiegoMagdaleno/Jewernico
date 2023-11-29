export class Argument {
    constructor(text, name, type) {
        this.text = text;
        this.name = name;
        this.type = type;
    }

    toPropCompatString() {
        return `${this.text}:${this.name}:${this.type}`;
    }

    static fromPropCompatString(string) {
        let [text, name, type] = string.split(":");
        return new Argument(text, name, type);
    }
}

export class Option {
    constructor(text, value, args) {
        this.text = text;
        this.value = value;
        this.args = args;
    }

    toDataHTMLTag() {
        return this.args === "undefined" ? `data-args=""` : `data-args="${this.args.map((arg) => arg.toPropCompatString()).join(" ")}"`;
    }
}
