// guest mode service
// handles guest user data management using browser localstorage
// allows users to save game states, favorites, and play history without account
// data can be synced to server when user creates account

class GuestService {
    constructor() {
        // localstorage key for guest data
        this.storageKey = 'abandoned_arcade_guest_data';
        this.init();
    }

    // initialize guest data structure
    // creates default data object if none exists
    init() {
        if (!this.getData()) {
            this.setData({
                save_states: [],      // array of game save states
                favorites: [],        // array of favorited game ids
                play_history: [],     // array of recently played games
                created_at: new Date().toISOString(),
            });
        }
    }

    // get all guest data from localstorage
    // returns parsed json object or null if error
    getData() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : null;
        } catch (e) {
            console.error('Error reading guest data:', e);
            return null;
        }
    }

    // save all guest data to localstorage
    // converts object to json string before storing
    setData(data) {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(data));
            return true;
        } catch (e) {
            console.error('Error saving guest data:', e);
            return false;
        }
    }

    // clear all guest data
    // removes data from localstorage and reinitializes
    clear() {
        localStorage.removeItem(this.storageKey);
        this.init();
    }

    // ============ save states ============

    // get all save states for a specific game
    getSaveStates(gameId) {
        const data = this.getData();
        return data?.save_states?.filter(save => save.game_id === gameId) || [];
    }

    // save a game state to localstorage
    // creates new save state with timestamp and unique id
    saveGameState(gameId, saveData, saveName = null) {
        const data = this.getData();
        if (!data) return false;

        const saveState = {
            id: Date.now().toString(),
            game_id: gameId,
            save_name: saveName,
            save_data: saveData,
            last_played_at: new Date().toISOString(),
            created_at: new Date().toISOString(),
        };

        // remove existing save with same name for this game
        // prevents duplicate saves with same name
        if (saveName) {
            data.save_states = data.save_states.filter(
                save => !(save.game_id === gameId && save.save_name === saveName)
            );
        }

        data.save_states.push(saveState);
        return this.setData(data);
    }

    // load a specific save state by game id and save id
    loadSaveState(gameId, saveId) {
        const data = this.getData();
        return data?.save_states?.find(
            save => save.game_id === gameId && save.id === saveId
        ) || null;
    }

    // delete a save state
    deleteSaveState(gameId, saveId) {
        const data = this.getData();
        if (!data) return false;

        data.save_states = data.save_states.filter(
            save => !(save.game_id === gameId && save.id === saveId)
        );
        return this.setData(data);
    }

    // ============ favorites ============

    // get all favorited game ids
    getFavorites() {
        const data = this.getData();
        return data?.favorites || [];
    }

    // add game to favorites list
    addFavorite(gameId) {
        const data = this.getData();
        if (!data) return false;

        // only add if not already in favorites
        if (!data.favorites.includes(gameId)) {
            data.favorites.push(gameId);
            return this.setData(data);
        }
        return true;
    }

    // remove game from favorites list
    removeFavorite(gameId) {
        const data = this.getData();
        if (!data) return false;

        data.favorites = data.favorites.filter(id => id !== gameId);
        return this.setData(data);
    }

    // check if game is in favorites
    isFavorite(gameId) {
        const favorites = this.getFavorites();
        return favorites.includes(gameId);
    }

    // ============ play history ============

    // get play history with optional limit
    // returns most recently played games
    getPlayHistory(limit = 10) {
        const data = this.getData();
        const history = data?.play_history || [];
        return history.slice(0, limit);
    }

    // add game to play history
    // moves game to top of history if already exists
    addToPlayHistory(gameId, gameTitle) {
        const data = this.getData();
        if (!data) return false;

        // remove existing entry if it exists
        // prevents duplicates in history
        data.play_history = data.play_history.filter(
            entry => entry.game_id !== gameId
        );

        // add to beginning of array (most recent first)
        data.play_history.unshift({
            game_id: gameId,
            game_title: gameTitle,
            played_at: new Date().toISOString(),
        });

        // keep only last 50 entries to prevent storage bloat
        data.play_history = data.play_history.slice(0, 50);

        return this.setData(data);
    }

    // ============ sync with server ============

    // sync guest data to server when user logs in
    // transfers localstorage data to user account
    // clears localstorage after successful sync
    async syncToServer(token) {
        const data = this.getData();
        if (!data) return false;

        try {
            const response = await fetch('/api/guest/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    save_states: data.save_states,
                    favorites: data.favorites,
                    play_history: data.play_history,
                }),
            });

            if (response.ok) {
                // clear guest data after successful sync
                this.clear();
                return true;
            }
            return false;
        } catch (e) {
            console.error('Error syncing guest data:', e);
            return false;
        }
    }
}

// create global instance
// makes GuestService available throughout the application
window.GuestService = new GuestService();

// export for module systems
// allows importing in other javascript files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GuestService;
}
