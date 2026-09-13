<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QuizWorld</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }

        select,
        button {
            padding: 12px 20px;
            margin: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <h1>Welcome to QuizWorld</h1>

    <p>Choose your account type</p>

    <select id="role">
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="admin">Admin</option>
    </select>

    <br>

    <button type="button" onclick="continueToRole()">
        Continue
    </button>


    <script>

        function continueToRole() {

            const role = document.getElementById("role").value;

            if (role === "student") {

                window.location.href = "user/html/login.html";

            } else if (role === "admin") {

                window.location.href = "admin/html/login.html";

            } else {

                alert("Please select a role.");

            }

        }

    </script>

</body>

</html>