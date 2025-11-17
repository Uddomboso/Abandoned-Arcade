/**
 * Utils
 */
export default {
    /**
     * Gets the target element from an event
     * @param {Event}   event
     * @param {...String} classes
     * @returns {HTMLElement}
     */
    getTarget(event, ...classes) {
        let element = event.target;
        while (element && element !== document.body) {
            if (classes.length === 0) {
                return element;
            }
            for (const className of classes) {
                if (element.classList && element.classList.contains(className)) {
                    return element;
                }
            }
            element = element.parentElement;
        }
        return element;
    },

    /**
     * Gets the mouse position from an event
     * @param {MouseEvent} event
     * @param {Boolean}    useOffset
     * @returns {{top: Number, left: Number}}
     */
    getMousePos(event, useOffset = true) {
        if (useOffset) {
            return {
                top  : event.clientY,
                left : event.clientX,
            };
        }
        const rect = event.target.getBoundingClientRect();
        return {
            top  : event.clientY - rect.top,
            left : event.clientX - rect.left,
        };
    },

    /**
     * Returns true if the position is inside the element
     * @param {{top: Number, left: Number}} pos
     * @param {HTMLElement}                 element
     * @returns {Boolean}
     */
    inElement(pos, element) {
        const rect = element.getBoundingClientRect();
        return pos.left >= rect.left && pos.left <= rect.right &&
               pos.top >= rect.top && pos.top <= rect.bottom;
    },

    /**
     * Returns true if the position is within bounds
     * @param {{top: Number, left: Number}} pos
     * @param {{top: Number, left: Number, width: Number, height: Number}} bounds
     * @returns {Boolean}
     */
    inBounds(pos, bounds) {
        return pos.left >= bounds.left && pos.left <= bounds.left + bounds.width &&
               pos.top >= bounds.top && pos.top <= bounds.top + bounds.height;
    },

    /**
     * Calculates the distance between two positions
     * @param {{top: Number, left: Number}} pos1
     * @param {{top: Number, left: Number}} pos2
     * @returns {Number}
     */
    dist(pos1, pos2) {
        const dx = pos1.left - pos2.left;
        const dy = pos1.top - pos2.top;
        return Math.sqrt(dx * dx + dy * dy);
    },

    /**
     * Creates a translate CSS transform string
     * @param {Number} left
     * @param {Number} top
     * @returns {String}
     */
    translate(left, top) {
        return `translate(${left}px, ${top}px)`;
    },

    /**
     * Converts a number to a pixel string
     * @param {Number} value
     * @returns {String}
     */
    toPX(value) {
        return `${value}px`;
    },

    /**
     * Parses time in seconds to an array of [hours, minutes, seconds]
     * @param {Number} time
     * @returns {String[]}
     */
    parseTime(time) {
        const hours   = Math.floor(time / 3600);
        const minutes = Math.floor((time % 3600) / 60);
        const seconds = Math.floor(time % 60);
        return [
            String(hours).padStart(2, "0"),
            String(minutes).padStart(2, "0"),
            String(seconds).padStart(2, "0"),
        ];
    },

    /**
     * Generates a random number between min and max (inclusive)
     * @param {Number} min
     * @param {Number} max
     * @returns {Number}
     */
    rand(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },

    /**
     * Returns a random element from an array
     * @param {Array} array
     * @returns {*}
     */
    randArray(array) {
        return array[this.rand(0, array.length - 1)];
    },

    /**
     * Removes an element from the DOM
     * @param {HTMLElement} element
     * @returns {Void}
     */
    removeElement(element) {
        if (element && element.parentElement) {
            element.parentElement.removeChild(element);
        }
    },
};

