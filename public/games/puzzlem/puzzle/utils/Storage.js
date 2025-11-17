/**
 * Storage Manager
 */
export default class Storage {
    /**
     * Storage constructor
     * @param {String} prefix
     */
    constructor(prefix) {
        this.prefix = prefix;
    }

    /**
     * Gets a value from storage
     * @param {String} key
     * @returns {*}
     */
    get(key) {
        const value = localStorage.getItem(`${this.prefix}.${key}`);
        if (value === null) {
            return null;
        }
        try {
            return JSON.parse(value);
        } catch (e) {
            return value;
        }
    }

    /**
     * Sets a value in storage
     * @param {String} key
     * @param {*}      value
     * @returns {Void}
     */
    set(key, value) {
        localStorage.setItem(`${this.prefix}.${key}`, JSON.stringify(value));
    }

    /**
     * Removes a value from storage
     * @param {String} key
     * @returns {Void}
     */
    remove(key) {
        localStorage.removeItem(`${this.prefix}.${key}`);
    }
}

