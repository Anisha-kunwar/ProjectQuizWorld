let selectedAnswer = null;

const optionButtons = document.querySelectorAll(".option");
const nextButton = document.getElementById("next");
const submitButton = document.getElementById("submit");

// Select an option
optionButtons.forEach(function(option) {

    option.addEventListener("click", function() {

        optionButtons.forEach(function(btn) {
            btn.classList.remove("selected");
        });

        option.classList.add("selected");

        selectedAnswer = option.dataset.option;
    });

});


// Next Question Validation
nextButton.addEventListener("click", function() {

    if (selectedAnswer === null) {
        alert("Please select an option.");
        return;
    }

    // Save answer
    const questionId = questions[currentQuestion].question_id;
    answers[questionId] = selectedAnswer;

    // Move to next question
    currentQuestion++;

    // Reset selection
    selectedAnswer = null;

    // Load next question
    loadQuestion();

});


// Submit Quiz Validation
submitButton.addEventListener("click", function() {

    if (selectedAnswer === null) {
        alert("Please select an option.");
        return;
    }

    // Save final answer
    const questionId = questions[currentQuestion].question_id;
    answers[questionId] = selectedAnswer;

    // Submit the complete quiz
    submitQuiz();

});