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
    $role = $_SESSION['role'];

    // admin deletes any post, normal user only their own
    if($role=='admin'){
        $chk = mysqli_query($conn,"SELECT id FROM posts WHERE id=$pid");
    }else{
        $chk = mysqli_query($conn,"SELECT id FROM posts WHERE id=$pid AND user_id=$uid");
    }

    if(mysqli_num_rows($chk)>0){
        // delete related data first then post
        mysqli_query($conn,"DELETE FROM likes    WHERE post_id=$pid");
        mysqli_query($conn,"DELETE FROM comments WHERE post_id=$pid");
        mysqli_query($conn,"DELETE FROM posts    WHERE id=$pid");
    }
}

if($_SESSION['role']=='admin'){
    header("Location: admin.php");
}else{
    header("Location: dashboard.php");
}
exit();
?>
