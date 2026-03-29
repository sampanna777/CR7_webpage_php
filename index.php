<?php
session_start();
include 'config.php';

// redirect if already loggedin
if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: admin.php");
    }else{
        header("Location: dashboard.php");
    }
    exit();
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // fetch user by email and role
    $sql = "SELECT * FROM users WHERE email='$email' AND role='$role'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result)==1){
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name']    = $row['name'];
            $_SESSION['role']    = $row['role'];

            if($row['role']=='admin'){
                header("Location: admin.php");
                exit();
            }else{
                header("Location: dashboard.php");
                exit();
            }
        }else{
            $error = "Wrong password!";
        }
    }else{
        $error = "User not found or role is wrong!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d0a0a 100%);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box{
    background:white;
    padding:35px 30px;
    border-radius:12px;
    width:320px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}
.logo{
    text-align:center;
    margin-bottom:20px;
}
.logo span{
    font-size:32px; font-weight:700;
    color:#c8102e; letter-spacing:2px;
}
.logo p{font-size:12px; color:#888; margin-top:2px;}
.box h2{
    margin-bottom:18px; font-size:20px;
    color:#1a1a1a; font-weight:600;
}
input, select{
    display:block;
    width:100%;
    padding:10px 12px;
    margin-bottom:13px;
    border:2px solid #eee;
    border-radius:8px;
    font-size:14px;
    font-family:'Poppins', sans-serif;
    transition: border-color 0.2s;
}
input:focus, select:focus{
    outline:none;
    border-color:#c8102e;
}
button{
    width:100%;
    padding:11px;
    background:#c8102e;
    color:white;
    border:none;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    font-family:'Poppins', sans-serif;
    cursor:pointer;
    transition: background 0.2s;
}
button:hover{ background:#a50d26; }
.err{
    color:#c8102e; font-size:13px;
    margin-bottom:12px; background:#fff0f0;
    padding:8px 10px; border-radius:6px;
    border-left:3px solid #c8102e;
}
.bottom{ text-align:center; margin-top:14px; font-size:13px; color:#888; }
.bottom a{ color:#c8102e; font-weight:500; text-decoration:none; }
.bottom a:hover{ text-decoration:underline; }
</style>
</head>
<body>
<div class="box">
    <div class="logo">
        <span>CR7</span>
        <p>Social Network</p>
    </div>
    <h2>Welcome Back</h2>
    <?php if($error!="") echo "<p class='err'>$error</p>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="user">Normal User</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Login</button>
    </form>
    <div class="bottom">
        No account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>
