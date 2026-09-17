const form = document.getElementById("registerForm");

form.addEventListener("submit", function (event) {

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password.length < 8) {
        event.preventDefault();
        alert("Password must contain at least 8 characters.");
        return;
    }

    if (password !== confirmPassword) {
        event.preventDefault();
        alert("Passwords do not match.");
        return;
    }

    // Form submits normally to register.php
});