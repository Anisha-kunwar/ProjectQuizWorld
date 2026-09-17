// Password Form

let passwordForm = document.getElementById("passwordForm");

passwordForm.addEventListener("submit", function (event) {

    event.preventDefault();

    let currentPassword = document.getElementById("currentPassword").value;
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    if (currentPassword === "") {

        alert("Please enter your Current Password.");
        return;

    }

    if (password === "") {

        alert("Please enter your New Password.");
        return;

    }

    if (password.length < 8) {

        alert("Password must contain at least 8 characters.");
        return;

    }

    if (confirmPassword === "") {

        alert("Please confirm your Password.");
        return;

    }

    if (password !== confirmPassword) {

        alert("Passwords do not match.");
        return;

    }

    alert("Password Changed Successfully!");

    // Later, when using PHP
    // passwordForm.submit();

});