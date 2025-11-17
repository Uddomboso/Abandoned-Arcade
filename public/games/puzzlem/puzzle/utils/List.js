/**
 * List Manager
 */
export default class List {
    /**
     * List constructor
     * @param {Array=} items
     */
    constructor(items = []) {
        this.items = Array.isArray(items) ? [...items] : [];
    }

    /**
     * Iterates over all items
     * @param {Function} callback
     * @returns {Void}
     */
    forEach(callback) {
        this.items.forEach(callback);
    }

    /**
     * Empties the list
     * @returns {Void}
     */
    empty() {
        this.items = [];
    }

    /**
     * Removes items that match the condition
     * @param {Function} condition
     * @returns {Void}
     */
    remove(condition) {
        this.items = this.items.filter((item) => !condition(item));
    }

    /**
     * Finds the first item that matches the condition
     * @param {Function} condition
     * @returns {*}
     */
    find(condition) {
        return this.items.find(condition);
    }

    /**
     * Converts the list to an array, optionally mapping each item
     * @param {Function=} mapper
     * @returns {Array}
     */
    toArray(mapper = null) {
        if (mapper) {
            return this.items.map(mapper);
        }
        return [...this.items];
    }

    /**
     * Maps the list items
     * @param {Function} mapper
     * @returns {Array}
     */
    map(mapper) {
        return this.items.map(mapper);
    }

    /**
     * Gets the length of the list
     * @returns {Number}
     */
    get length() {
        return this.items.length;
    }
}

