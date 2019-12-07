
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
<body>
    <form action="Login.php" method="Post" class="photo font" style="background-image: url('https://i.pinimg.com/originals/22/c6/3a/22c63a5df2159dd7492145a7977dfbc1.jpg')">
        <h1>Are you prepared?</h1><br><br>
        <h4>Enter your identifier: </h4>
        <input type="text" id="username" name="username" placeholder="New Username">
        <br><br><h4>Create a passcode:</h4>
        <input type="password" id="password" name="password" placeholder="Secret Code"><br><br>
        <input type="submit">
    </form>
    <?php session_start();?>
</body>
</head>
</html>