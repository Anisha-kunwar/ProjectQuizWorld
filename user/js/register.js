// Get the form
function changeRole() {

    const role = document.getElementById("role").value;

    if (role === "admin") {

        window.location.href = "../../admin/html/adminlogin.html";

    }

}

let form = document.getElementById("registerForm");


// Run validation when the form is submitted

form.addEventListener("submit", function (event) {

    event.preventDefault();


    // Get input values

    let fullname =
    document.getElementById("fullname").value.trim();

    let email =
    document.getElementById("email").value.trim();

    let username =
    document.getElementById("username").value.trim();

    let password =
    document.getElementById("password").value;

    let confirmPassword =
    document.getElementById("confirmPassword").value;

    let terms =
    document.getElementById("terms").checked;



    // Full Name Validation

    if (fullname === "") {

        alert("Please enter your Full Name.");
        return;

    }


    // Email Validation

    if (email === "") {

        alert("Please enter your Email.");
        return;

    }


    // Check email format

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!emailPattern.test(email)) {
    alert("Please enter a valid email.");
    return;
  }


    // Username Validation

    if (username === "") {

        alert("Please enter your Username.");
        return;

    }


    // Password Validation

    if (password === "") {

        alert("Please enter your Password.");
        return;

    }


    // Password Length

    if (password.length < 8) {

        alert("Password must contain at least 8 characters.");
        return;

    }


    // Confirm Password Validation

    if (confirmPassword === "") {

        alert("Please confirm your Password.");
        return;

    }


    // Match Passwords

    if (password !== confirmPassword) {

        alert("Passwords do not match.");
        return;

    }


    // Terms & Conditions Validation

    if (!terms) {

        alert("Please accept the Terms & Conditions.");
        return;

    }



    // Success Message

    alert("Registration Successful!");


    


    // Temporary redirect for frontend testing

    window.location.href = "login.html";

});