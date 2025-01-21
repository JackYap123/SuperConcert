const adminEmail = "admin@example.com";
const adminPassword = "admin123";

function toggleForm(section) {
    document.getElementById('login-section').classList.remove('active');
    document.getElementById('register-section').classList.remove('active');
    if (section === 'login') {
        document.getElementById('login-section').classList.add('active');
    } else {
        document.getElementById('register-section').classList.add('active');
    }
}

document.getElementById('login-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    if (email === adminEmail && password === adminPassword) {
        alert("Welcome, Admin! Redirecting to the admin dashboard...");
        // Redirect to admin dashboard
        window.location.href = "admin-dashboard.html"; // Replace with actual admin page
    } else {
        alert("Invalid email or password. Please try again.");
    }
});