
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
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
        border: 2px solid white;
        border-radius: 2px;
        background: black;
    }
    </style>
</head>
<body>
    <form name="form" action="index.html" method="Post" class="photo font" >
<!--         
    <p><h1>Hello, <label id="userLabel">GenericUser</label></h1></p> -->
    <input type="submit">
    </form>
</body>
<?php
session_start();
$username = $_POST['username'];
$password = $_POST['password'];
// getUsers();
if($_POST['existing'] == 1){
    login($username, $password);
}
else{
    $wat = addUser($username, $password);
    echo $wat;
}

 $_SESSION["words"] = loadWord();
// $isAvailable = checkAvailability($username);
// echo "<br> Is this available? ". $isAvailable;
// if($isAvailable){
//     //echo "available name";
//     addUser($username, $password);
// }
// else{
//     echo "Please pick another name";
// }
//Session variables to track: username
// echo "on login";
 $_SESSION["username"] = $username;
// echo $_SESSION["username"] = "Alex";
// echo "<script type=\"text/javascript\">";
// echo "lbl = document.getElementById(\"username\").value;";
// echo "lbl.innerText = " . $_SESSION["username"]; . ";";
// echo "</script>";

function checkAvailability($username): boolean{
    echo $username;
    $hostName = "sql207.epizy.com";
    $user = "epiz_24465118";
    $DBpassword = "typeordie1";
    $databaseName = "epiz_24465118_typeordiedb";
    
    $conn = mysqli_connect($hostName, $user, $DBpassword, $databaseName);
    if (mysqli_connect_errno()) {
        echo "Connect failed: %s\n", mysqli_connect_error();
        exit();
    }
    
    // // if ($conn->connect_error) 
    // // {
    //     //     die("Connection failed: " . conn->connect_error);
    // // }
    echo "in check";
    // Check the username for availability
    $sql = "SELECT * FROM UserLogin WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    echo "<br>" .msqli_num_rows($result);
    if(mysqli_num_rows($result) > 0){
        mysqli_close($conn);
        return false;
    }
    mysqli_close($conn);
    echo "username " .$result. " available";
    return true;        
}
// 100% works
function getUsers(): string{
    $hostName = "sql207.epizy.com";
    $user = "epiz_24465118";
    $DBpassword = "typeordie1";
    $databaseName = "epiz_24465118_typeordiedb";

    $conn = mysqli_connect($hostName, $user, $DBpassword, $databaseName);
    if (mysqli_connect_errno()) {
        echo "Connect failed: %s\n", mysqli_connect_error();
        exit();
    }
    $sql = "Select * from UserLogin";
    $result = mysqli_query($conn, $sql);
    $result2 = mysqli_fetch_all($result);
    
    mysqli_free_result($result);
    mysqli_close($conn);
}
function addUser($username, $password): string{
    $hostName = "sql207.epizy.com";
    $user = "epiz_24465118";
    $DBpassword = "typeordie1";
    $databaseName = "epiz_24465118_typeordiedb";
    
    if(!checkAvailability($username)){
        exit();
    }
    $conn = mysqli_connect($hostName, $user, $DBpassword, $databaseName);
    if (mysqli_connect_errno()) {
        echo "Connect failed: %s\n", mysqli_connect_error();
        exit();
    }

    $salt = createSalt(32);
    
    $hash = hashPass($password . $salt);

    // //Insert the user
    $sql = "INSERT INTO UserLogin (username, salt, hash) VALUES ('".$username."','".$salt."','".$hash."')";
    // $values = ["manualUser", "fakeSalt","fakeHash1"];
    $result = mysqli_query($conn, $sql);
    $result2 = mysqli_fetch_all($result);
    // echo "result2? ". $result2;
    $numrows = mysqli_num_rows($result); 
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($result,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
        if (isset($resrow[$col])){
            echo $resrow[$col];
        }
    }
    
    $numrows = mysqli_num_rows($result2); 
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($result2,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($result2) : mysqli_fetch_assoc($result2);
        if (isset($resrow[$col])){
            echo "<br>".$resrow[$col];
        }
    }
    
    // // mysqli_free_result($result);
    // mysqli_close($conn);
    return $username . " is logged in";
}
function createSalt($numBytes): string{
    return random_bytes($numBytes);
}

function hashPass($password): string{
    return hash("sha256", $password);
}
function login($username, $password): boolean{
    $hostName = "sql207.epizy.com";
    $user = "epiz_24465118";
    $DBpassword = "typeordie1";
    $databaseName = "epiz_24465118_typeordiedb";
    
    $conn = mysqli_connect($hostName, $user, $DBpassword, $databaseName);

    $sql = "Select * from UserLogin WHERE username = '".$username."'";
    $result = mysqli_query($conn, $sql);
    $result2 = mysqli_fetch_all($result);
    
    $hash = hashPass($password . $result2->salt);
    
    $sql = "Select * from UserLogin WHERE username = '".$username."' AND hash = '".$hash."'";
    $result = mysqli_query($conn, $sql);
    $result2 = mysqli_fetch_all($result);
    
    if($result){
        echo "Logged in!";
    }
    else {
        echo "Bad password.";
    }

    mysqli_free_result($result);
    mysqli_close($conn);

}
function loadWord(){
    $hostName = "sql207.epizy.com";
    $user = "epiz_24465118";
    $password = "typeordie1";
    $databaseName = "epiz_24465118_typeordiedb";
    
    $conn = mysqli_connect($hostName, $user, $password, $databaseName);
    // if (mysqli_connect_errno()) {
    //     echo "Connect failed: %s\n", mysqli_connect_error();
    //     exit();
    // }
    $sql = "Select * from Words";
    $result = mysqli_query($conn, $sql);
    // $result2 = mysqli_fetch_all($result);
    $numrows = mysqli_num_rows($result); 
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($result,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
        if (isset($resrow[$col])){
            echo $resrow[$col];
        }
    }
    // mysqli_free_result($result);
    mysqli_close($conn);
    return $result;
}
?>
</html>