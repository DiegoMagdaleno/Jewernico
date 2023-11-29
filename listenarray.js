export class ArrayEmitter extends Array {
    constructor(...args) {
        super(...args);
    }

    push(...args) {
        const result = super.push(...args);
        this.emitChange();
        return result;
    }

    pop() {
        const result = super.pop();
        this.emitChange();
        return result;
    }

    splice(...args) {
        const result = super.splice(...args);
        this.emitChange();
        return result;
    }

    clear() {
        this.splice(0, this.length);
    }

    emitChange() {
        if (this.onArrayChange) {
            this.onArrayChange(Array.from(this));
        }
    }

    onArrayChangeEvent(callback) {
        this.onArrayChange = callback;
    }
}