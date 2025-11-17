/**
 * Sounds Manager
 */
export default class Sounds {
    /**
     * Sounds constructor
     * @param {String} prefix
     */
    constructor(prefix) {
        this.prefix  = prefix;
        this.enabled = this.load();
        this.audio   = {};
    }

    /**
     * Loads the sound preference from localStorage
     * @returns {Boolean}
     */
    load() {
        const stored = localStorage.getItem(`${this.prefix}.enabled`);
        return stored !== null ? stored === "true" : true;
    }

    /**
     * Saves the sound preference to localStorage
     * @returns {Void}
     */
    save() {
        localStorage.setItem(`${this.prefix}.enabled`, String(this.enabled));
    }

    /**
     * Toggles the sound on/off
     * @returns {Void}
     */
    toggle() {
        this.enabled = !this.enabled;
        this.save();
    }

    /**
     * Plays a sound
     * @param {String} name
     * @returns {Void}
     */
    play(name) {
        if (!this.enabled) {
            return;
        }
        if (!this.audio[name]) {
            const audio = new Audio(`audio/${name}.mp3`);
            audio.volume = 0.5;
            this.audio[name] = audio;
        }
        const sound = this.audio[name].cloneNode();
        sound.play().catch(() => {
            // Ignore play errors (e.g., user hasn't interacted with page)
        });
    }
}

