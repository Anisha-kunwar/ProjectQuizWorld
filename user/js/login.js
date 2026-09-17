let form = document.getElementById("loginForm");

form.addEventListener("submit", function (event) {

    let userId = document.getElementById("user_id").value.trim();
    let password = document.getElementById("password").value;

    if (userId === "") {
        event.preventDefault();
        alert("Please enter your User ID.");
        return;
    }

    if (password === "") {
        event.preventDefault();
        alert("Please enter your Password.");
        return;
    }

    if (password.length < 8) {
        event.preventDefault();
        alert("Password must contain at least 8 characters.");
        return;
    }

});