<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            height: 100vh;
            background: #081524;
            color: white;
        }
        .container {
            width: 40%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
        }
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 20px;
            cursor: pointer;
        }
        .tab {
            position: relative;
            padding-bottom: 5px;
        }
        .tab.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background: #bcd4f0;
        }
        .form-box {
            display: none;
            max-width: 400px;
            width: 100%;
        }
        .form-box.active {
            display: block;
        }
        .input-box {
            margin: 10px 0;
        }
        .input-box input {
            width: 100%;
            padding: 10px;
            background: none;
            border: 1px solid #ecf3fb;
            border-radius: 5px;
            color: white;
        }
        .btn {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #112d4e;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: rgb(100, 137, 182);
        }
        .side-image {
            width: 60%;
            background: url('./image/login-bg.jpg') no-repeat center center/cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tabs">
            <div class="tab active" onclick="showForm('login')">Login</div>
            <div class="tab" onclick="showForm('register')">Register</div>
        </div>

        <!-- Login Form -->
        <div class="form-box active" id="login-form">
            <h2>Login</h2>
            <form action="login_action.php" method="POST">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-box">
                    <input type="password" name="psw" placeholder="Password" required>
                </div>
                <button class="btn" type="submit">Login</button>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-box" id="register-form">
            <h2>Register</h2>
            <form action="signup_action.php" method="POST">
                <div class="input-box"><input type="text" name="idno" placeholder="ID Number" required></div>
                <div class="input-box"><input type="text" name="lastname" placeholder="Last Name" required></div>
                <div class="input-box"><input type="text" name="firstname" placeholder="First Name" required></div>
                <div class="input-box"><input type="text" name="middlename" placeholder="Middle Name"></div>
                <div class="input-box"><input type="text" name="course" placeholder="Course" required></div>
                <div class="input-box"><input type="number" name="year" placeholder="Year Level" required></div>
                <div class="input-box"><input type="email" name="email" placeholder="Email"></div>
                <div class="input-box"><input type="text" name="address" placeholder="Address" required></div>
                <hr>
                <div class="input-box"><input type="text" name="username" placeholder="Username" required></div>
                <div class="input-box"><input type="password" name="psw-reg" placeholder="Password" required></div>
                <button class="btn" type="submit">Register</button>
            </form>
        </div>
    </div>
    <div class="side-image"></div>

    <script>
        function showForm(form) {
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('register-form').classList.remove('active');
            document.querySelector('.tab.active').classList.remove('active');
            document.querySelector(`.tab[onclick="showForm('${form}')"]`).classList.add('active');
            document.getElementById(`${form}-form`).classList.add('active');
        }
    </script>
</body>
</html>
