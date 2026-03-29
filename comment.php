<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $pid  = $_POST['post_id'];
    $uid  = $_SESSION['user_id'];
    $text = mysqli_real_escape_string($conn, $_POST['comment']);

    if($text!=""){
        mysqli_query($conn,"INSERT INTO comments(post_id,user_id,comment) VALUES($pid,$uid,'$text')");
    }
}

if($_SESSION['role']=='admin'){
    header("Location: admin.php");
}else{
    header("Location: dashboard.php");
}
exit();
?>
