// main application javascript entry point
// imports and initializes all javascript modules
import './bootstrap';
import './guest';
import './auth-sync';
import './leaderboard';
import './search-autocomplete';

// display guest ID in navbar for guest users
document.addEventListener('DOMContentLoaded', function() {
    const guestIdElement = document.getElementById('guest-id');
    if (guestIdElement && window.GuestService) {
        const guestId = window.GuestService.getGuestId();
        // Extract just the numbers from "Guest12345678"
        const numbers = guestId.replace('Guest', '');
        guestIdElement.textContent = numbers;
    }
});
