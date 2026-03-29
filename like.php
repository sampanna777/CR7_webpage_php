<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $pid = $_POST['post_id'];
    $uid = $_SESSION['user_id'];

    // if already liked then unlike, else like
    $chk = mysqli_query($conn,"SELECT id FROM likes WHERE post_id=$pid AND user_id=$uid");
    if(mysqli_num_rows($chk)>0){
        mysqli_query($conn,"DELETE FROM likes WHERE post_id=$pid AND user_id=$uid");
    }else{
        mysqli_query($conn,"INSERT INTO likes(post_id,user_id) VALUES($pid,$uid)");
    }
}

if($_SESSION['role']=='admin'){
    header("Location: admin.php");
}else{
    header("Location: dashboard.php");
}
exit();
?>
