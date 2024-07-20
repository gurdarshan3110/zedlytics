let logoutTimer;

function resetLogoutTimer() {
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(function() {
        document.getElementById('logoutForm').submit();
    }, 3600000);
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
