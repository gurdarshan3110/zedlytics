let logoutTimer;

function resetLogoutTimer() {
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(function() {
        // Perform logout or redirect to logout route
        window.location.href = '/logout'; // Replace with your logout route
    }, 900000); // 15 minutes in milliseconds
}

// Initialize the timer when the page is loaded or user interacts with the page
document.addEventListener('DOMContentLoaded', function() {
    resetLogoutTimer();
});

// Reset the timer on user activity
document.addEventListener('mousemove', function() {
    resetLogoutTimer();
});

document.addEventListener('keydown', function() {
    resetLogoutTimer();
});
