<?php

$fullname = trim($_POST["fullname"]);
$email = trim($_POST["email"]);
$username = trim($_POST["username"]);
$phone = trim($_POST["phone"]);
$password = $_POST["password"];
$confirmPassword = $_POST["confirmPassword"];



if(empty($fullname))
{
    die("Full Name is required.");
}


if(empty($email))
{
    die("Email is required.");
}


if(empty($username))
{
    die("Username is required.");
}


if(empty($phone))
{
    die("Phone Number is required.");
}


if(strlen($password) < 8)
{
    die("Password must contain at least 8 characters.");
}


if($password != $confirmPassword)
{
    die("Passwords do not match.");
}


echo "Profile Updated Successfully!";


// Later:
//
// Update user information in database.
//

?>