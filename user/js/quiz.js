let selectedOption = null;

// Select an option

let options = document.querySelectorAll(".option");

options.forEach(function(option){

    option.addEventListener("click", function(){

        options.forEach(function(btn){
            btn.classList.remove("selected");
        });

        option.classList.add("selected");

        selectedOption = option.innerText;

    });

});


// Next Question Validation

document.getElementById("next").addEventListener("click", function(){

    if(selectedOption === null){

        alert("Please select an option.");
        return;

    }

    // Load next question here

    selectedOption = null;

});


// Submit Quiz Validation

document.getElementById("submit").addEventListener("click", function(){

    if(selectedOption === null){

        alert("Please select an option.");
        return;

    }

    alert("Quiz Submitted Successfully!");

    window.location.href="result.html";

});