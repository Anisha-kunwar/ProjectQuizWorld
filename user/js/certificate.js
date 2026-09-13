// Get user details from localStorage

const userName =
localStorage.getItem("userName") || "Guest";
const quizName = localStorage.getItem("quizName");
const score = localStorage.getItem("score");
const certificateID = localStorage.getItem("certificateID");


// Generate today's date automatically

const today = new Date();

const date = today.toLocaleDateString("en-GB", {
    day: "numeric",
    month: "long",
    year: "numeric"
});


// Display the values on the certificate

document.getElementById("name").textContent = userName;

document.getElementById("subject").textContent = quizName;

document.getElementById("score").textContent =
    "Score : " + score;

document.getElementById("date").textContent =
    "Date : " + date;

document.getElementById("certificateID").textContent =
    "Certificate ID : " + certificateID;