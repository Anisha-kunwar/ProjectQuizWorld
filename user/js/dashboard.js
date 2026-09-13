// Continue Quiz Button

let continueBtn =
document.getElementById("continueBtn");


continueBtn.addEventListener("click",function(){

    window.location.href =
    "categories.html";

});




// Logout Button

let logout =
document.getElementById("logout");


logout.addEventListener("click",function(){

    alert("Logged Out Successfully!");

    window.location.href =
    "login.html";

});

logout.addEventListener("click", function(){

    if(confirm("Are you sure you want to logout?")){

        alert("Logged Out Successfully!");

        window.location.href="login.html";

    }

});