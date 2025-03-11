function addUser() {
    const name = document.getElementById('new-name').value.trim();
    const email = document.getElementById('new-email').value.trim();

    if (name && email) {
        fetch('add_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email)
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            if (data.includes("successfully")) {
                location.reload(); // reload to show new user
            }
        });
    } else {
        alert('Please fill in both fields.');
    }
}

function removeUser(button) {
    const userItem = button.closest('.user-item');
    const emailText = userItem.querySelector('.user-info').innerText;
    const email = emailText.match(/\(([^)]+)\)/)[1]; // Extract email from (email)

    if (confirm('Are you sure you want to remove ' + email + '?')) {
        fetch('remove_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            if (data.includes("successfully")) {
                userItem.remove();
            }
        });
    }
}

function notifyUser(email) {
    fetch('notify_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
    });
}
