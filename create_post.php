<?php
// create_post.php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $uid  = $_SESSION['user_id'];
    $text = mysqli_real_escape_string($conn, $_POST['content']);

    if($text!=""){
        mysqli_query($conn,"INSERT INTO posts(user_id,content) VALUES($uid,'$text')");
    }
}

// send back to right page
if($_SESSION['role']=='admin'){
    header("Location: admin.php");
}else{
    header("Location: dashboard.php");
}
exit();
?>
