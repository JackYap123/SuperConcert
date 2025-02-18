function toggleForm(section) {
    document.getElementById('login-section').classList.remove('active');
    document.getElementById('register-section').classList.remove('active');
    if (section === 'login') {
        document.getElementById('login-section').classList.add('active');
    } else {
        document.getElementById('register-section').classList.add('active');
    }
}