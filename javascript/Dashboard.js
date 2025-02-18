document.getElementById('change-password-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Updating...';

    const formData = new FormData(form);

    fetch("Dashboard.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            const messageElement = document.getElementById('change-password-message');
            if (data.success) {
                document.getElementById('change-password-modal').style.display = 'none';
                alert(data.message);
            } else {
                messageElement.style.display = 'block';
                messageElement.textContent = data.message;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Update Password';
        });
});
