
<!DOCTYPE html>
<html lang="en">
<head>
        
    <meta charset="UTF-8">
    <title>Type.... or Die</title>
    <style type="text/css">
    form{
        text-align: center;
        }
    .photo{
        background-position: center center;
        background-size: cover;
        height: auto;
        left: 0;
        min-height: 100%;
        min-width: 100%;
        position: absolute;
        top: 0;
        width: auto;
    }
    .font{
        color: white;
    }
    h1, h4{
        border: 2px solid black;
        border-radius: 2px;
        background: black;
    }
    </style>
</head>
<body>
    <form name="login" method="post" action="Login.php" class="photo font" style="background-image: url('https://www.pixelstalk.net/wp-content/uploads/2016/10/Animal-tiger-bing-background.jpg')">
        <h1>Welcome to Type or Die</h1>
        <div>
            <h4>New User?</h4><br>
            <button onclick="login.action='newUser.php'">Create New User</button><br><br>
        </div>
        <div>
            <h4>Existing User?</h4><br>
            <input type="text" name="username" id="username" placeholder="Username"><br>
            <input type="password" name="password" id="password" placeholder="Password"><br>
            <input type="hidden" name="existing" value="1">
            <input type="submit" value="Login">
            <br><br>
            <p id="err"></p>
        </div>

<?php
session_start();
?>
</form>
</body>
</html>