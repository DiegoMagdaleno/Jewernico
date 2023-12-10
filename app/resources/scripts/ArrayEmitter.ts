export class ArrayEmitter<T> extends Array<T> {
    onArrayChange: (array: T[]) => void; // change the name of the property to match the method
    constructor(...args: T[]) {
        super(...args);
    }

    public push(...args: T[]): number {
        const result = super.push(...args);
        this.emitChange();
        return result;
    }

    public pop(): T | undefined {
        const result = super.pop();
        return result;
    }

    public splice(start?: number, deleteCount?: number, ...items: T[]): T[] {
        const result = super.splice(start || 0, deleteCount, ...items);
        this.emitChange();
        return result;
    }

    public clear(): void {
        this.splice(0, this.length);
    }

    private emitChange(): void {
        if (this.onArrayChange) {
            this.onArrayChange(Array.from(this) as T[]);
        }
    }

    public setOnArrayChange(callback: (array: T[]) => void): void { // change the name of the method to match the property
        this.onArrayChange = callback;
    }
}
