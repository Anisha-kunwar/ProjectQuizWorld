// ======================
// Profile Form
// ======================

let profileForm = document.getElementById("profileForm");

profileForm.addEventListener("submit", function(event){

    event.preventDefault();

    let fullname = document.getElementById("fullname").value.trim();
    let username = document.getElementById("username").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let about = document.getElementById("about").value.trim();

    if(fullname === ""){
        alert("Please enter your Full Name.");
        return;
    }

    if(username === ""){
        alert("Please enter your Username.");
        return;
    }

    if(email === ""){
        alert("Please enter your Email.");
        return;
    }

    if(phone === ""){
        alert("Please enter your Phone Number.");
        return;
    }

    if(about === ""){
        alert("Please write something about yourself.");
        return;
    }

    alert("Profile Updated Successfully!");

    // profileForm.submit();
});


// ======================
// Change Password Form
// ======================

let passwordForm = document.getElementById("passwordForm");

passwordForm.addEventListener("submit", function(event){

    event.preventDefault();

    let currentPassword = document.getElementById("currentPassword").value;
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    if(currentPassword === ""){
        alert("Please enter your Current Password.");
        return;
    }

    if(password.length < 8){
        alert("Password must contain at least 8 characters.");
        return;
    }

    if(password !== confirmPassword){
        alert("Passwords do not match.");
        return;
    }

    alert("Password Changed Successfully!");

    // passwordForm.submit();
});