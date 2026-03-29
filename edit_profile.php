<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='user'){
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];
$err = "";
$ok  = "";

if($_SERVER['REQUEST_METHOD']=='POST'){
    $n  = mysqli_real_escape_string($conn, $_POST['name']);
    $e  = mysqli_real_escape_string($conn, $_POST['email']);
    $p  = $_POST['password'];

    // check email not taken by someone else
    $chk = mysqli_query($conn,"SELECT id FROM users WHERE email='$e' AND id!=$uid");
    if(mysqli_num_rows($chk)>0){
        $err = "That email is already used.";
    }else{
        $hp = password_hash($p, PASSWORD_DEFAULT);
        $q  = "UPDATE users SET name='$n', email='$e', password='$hp' WHERE id=$uid";
        if(mysqli_query($conn,$q)){
            $ok = "Profile updated successfully!";
            $_SESSION['name'] = $n;
        }else{
            $err = "Something went wrong, try again.";
        }
    }
}

$udata = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$uid"));
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body{
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d0a0a 100%);
    display:flex; justify-content:center;
    align-items:center; height:100vh;
}
.box{
    background:white; padding:28px 24px;
    border-radius:12px; width:350px;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
}
.box h2{
    margin-bottom:16px; font-size:20px;
    color:#1a1a1a; font-weight:600;
}
input{
    display:block; width:100%;
    padding:10px 12px; margin:8px 0;
    border:2px solid #eee; border-radius:8px;
    font-size:14px; font-family:'Poppins', sans-serif;
    box-sizing:border-box; transition: border-color 0.2s;
}
input:focus{outline:none; border-color:#c8102e;}
button{
    width:100%; padding:11px;
    background:#c8102e; color:white;
    border:none; border-radius:8px;
    font-size:15px; font-weight:600;
    font-family:'Poppins', sans-serif;
    cursor:pointer; transition: background 0.2s;
}
button:hover{background:#a50d26;}
.err{
    color:#c8102e; font-size:13px; margin-bottom:10px;
    background:#fff0f0; padding:8px 10px;
    border-radius:6px; border-left:3px solid #c8102e;
}
.ok{
    color:#1a7a1a; font-size:13px; margin-bottom:10px;
    background:#f0fff0; padding:8px 10px;
    border-radius:6px; border-left:3px solid #28a745;
}
a{color:#c8102e; font-size:13px; text-decoration:none; font-weight:500;}
a:hover{text-decoration:underline;}
</style>
</head>
<body>
<div class="box">
    <h2>Edit Profile</h2>
    <?php if($err) echo "<p class='err'>$err</p>"; ?>
    <?php if($ok)  echo "<p class='ok'>$ok</p>"; ?>
    <form method="POST">
        <input type="text"     name="name"     value="<?php echo $udata['name']; ?>" required>
        <input type="email"    name="email"    value="<?php echo $udata['email']; ?>" required>
        <input type="password" name="password" placeholder="New password" required>
        <button type="submit">Save Changes</button>
    </form>
    <br>
    <a href="dashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>
