// live search autocomplete functionality
// displays dropdown with games as user types

class SearchAutocomplete {
    constructor(searchInputId, dropdownId) {
        this.searchInput = document.getElementById(searchInputId);
        this.dropdown = document.getElementById(dropdownId);
        this.debounceTimer = null;
        this.currentRequest = null;
        
        if (!this.searchInput || !this.dropdown) {
            return;
        }
        
        this.init();
    }
    
    init() {
        // show dropdown on focus if there's a value
        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.trim()) {
                this.performSearch(this.searchInput.value);
            }
        });
        
        // hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.searchInput.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.hideDropdown();
            }
        });
        
        // handle input with debounce (reduced delay for faster results)
        this.searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            if (query.length === 0) {
                this.hideDropdown();
                return;
            }
            
            // cancel previous request if still pending
            if (this.currentRequest) {
                this.currentRequest.abort();
            }
            
            // debounce search (reduced from 200ms to 150ms for faster response)
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.performSearch(query);
            }, 150);
        });
        
        // handle keyboard navigation
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideDropdown();
                this.searchInput.blur();
            }
            // Let Enter key submit the form naturally - don't prevent default
        });
    }
    
    async performSearch(query) {
        if (!query || query.length === 0) {
            this.hideDropdown();
            return;
        }
        
        try {
            // create abort controller for request cancellation
            const controller = new AbortController();
            this.currentRequest = controller;
            
            const apiUrl = `/api/games/autocomplete?q=${encodeURIComponent(query)}`;
            console.log('Fetching:', apiUrl);
            
            const response = await fetch(apiUrl, {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            if (!response.ok) {
                console.error('Search failed:', response.status, response.statusText);
                throw new Error(`Search failed: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Search results:', data);
            this.displayResults(data.games, data.has_more, query);
            this.currentRequest = null;
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Search error:', error);
                this.hideDropdown();
            }
            this.currentRequest = null;
        }
    }
    
    displayResults(games, hasMore, searchTerm) {
        if (games.length === 0) {
            this.dropdown.innerHTML = `
                <div class="search-dropdown-item text-muted">
                    No games found starting with "${searchTerm}"
                </div>
            `;
            this.showDropdown();
            return;
        }
        
        let html = '';
        
        // display games
        games.forEach(game => {
            html += `
                <a href="${game.url}" class="search-dropdown-item" onclick="event.preventDefault(); window.location.href='${game.url}';">
                    <div class="search-item-title">${this.highlightMatch(game.title, searchTerm)}</div>
                    <div class="search-item-genre">${game.genre}</div>
                </a>
            `;
        });
        
        // add "show more" option if there are more results
        if (hasMore) {
            const gamesUrl = `/games?search=${encodeURIComponent(searchTerm)}`;
            html += `
                <a href="${gamesUrl}" class="search-dropdown-item search-show-more" onclick="event.preventDefault(); window.location.href='${gamesUrl}';">
                    <div class="search-item-title">Show more results...</div>
                </a>
            `;
        }
        
        this.dropdown.innerHTML = html;
        this.showDropdown();
    }
    
    highlightMatch(text, searchTerm) {
        if (!searchTerm) return text;
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    }
    
    showDropdown() {
        this.dropdown.style.display = 'block';
    }
    
    hideDropdown() {
        this.dropdown.style.display = 'none';
    }
}

// initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('navbar-search-input');
    const dropdown = document.getElementById('navbar-search-dropdown');
    
    if (searchInput && dropdown) {
        window.searchAutocomplete = new SearchAutocomplete('navbar-search-input', 'navbar-search-dropdown');
    } else {
        console.warn('Search autocomplete elements not found:', { searchInput, dropdown });
    }
});

