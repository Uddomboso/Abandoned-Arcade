// auth sync module
// automatically syncs guest data to user account when user logs in
// transfers localstorage data (save states, favorites, play history) to server
// runs on page load if user is authenticated and has guest data

document.addEventListener('DOMContentLoaded', function() {
    // check if user is authenticated from data attribute on body
    const isAuthenticated = document.body.dataset.userAuthenticated === 'true';
    // check if guest service has any data to sync
    const hasGuestData = window.GuestService && (
        window.GuestService.getData()?.save_states?.length > 0 ||
        window.GuestService.getData()?.favorites?.length > 0 ||
        window.GuestService.getData()?.play_history?.length > 0
    );
    
    // only sync if user is authenticated and has guest data
    if (isAuthenticated && hasGuestData) {
        // get csrf token from meta tag for laravel security
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (csrfToken) {
            // verify user session by fetching user info
            // this ensures session is valid before syncing
            fetch('/api/user', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
            })
            .then(response => response.json())
            .then(data => {
                // get all guest data from localstorage
                const guestData = window.GuestService.getData();
                
                // sync guest data to server using session authentication
                return fetch('/api/guest/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        save_states: guestData.save_states || [],
                        favorites: guestData.favorites || [],
                        play_history: guestData.play_history || [],
                    }),
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    console.log('Guest data synced successfully!');
                    // clear guest data after successful sync
                    // prevents duplicate data and frees localstorage
                    window.GuestService.clear();
                    
                    // show success notification to user
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alert.style.zIndex = '9999';
                    alert.style.maxWidth = '500px';
                    alert.innerHTML = `
                        <strong>Success!</strong> Your guest data has been synced to your account.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alert);
                    
                    // auto remove notification after 5 seconds
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error syncing guest data:', error);
            });
        }
    }
});
