<?php

// Get the form data

$fullname = trim($_POST["fullname"]);
$email = trim($_POST["email"]);
$username = trim($_POST["username"]);
$password = $_POST["password"];
$confirmPassword = $_POST["confirmPassword"];


// Full Name Validation

if (empty($fullname))
{
    die("Full Name is required.");
}


// Email Validation

if (empty($email))
{
    die("Email is required.");
}


// Check Email Format

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
{
    die("Please enter a valid Email.");
}


// Username Validation

if (empty($username))
{
    die("Username is required.");
}


// Password Validation

if (empty($password))
{
    die("Password is required.");
}


// Password Length

if (strlen($password) < 8)
{
    die("Password must contain at least 8 characters.");
}


// Confirm Password Validation

if (empty($confirmPassword))
{
    die("Please confirm your password.");
}


// Password Match

if ($password != $confirmPassword)
{
    die("Passwords do not match.");
}


// Terms and Conditions

if (!isset($_POST["terms"]))
{
    die("Please accept the Terms & Conditions.");
}


// If all validations are successful

echo "Registration Successful!";




?>