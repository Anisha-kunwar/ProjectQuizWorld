// Get the form

let form = document.getElementById("loginForm");


// Run validation when the form is submitted

form.addEventListener("submit", function (event) {

    event.preventDefault();


    // Get input values

    let username =
    document.getElementById("username").value.trim();

    let password =
    document.getElementById("password").value;


    // Username or Email Validation

    if (username === "") {

        alert("Please enter your Username or Email.");
        return;

    }


    // Password Validation

    if (password === "") {

        alert("Please enter your Password.");
        return;

    }


    // Password Length Validation

    if (password.length < 8) {

        alert("Password must contain at least 8 characters.");
        return;

    }


    // Success Message

    alert("Login Successful!");

window.location.href = "dashboard.html";


});