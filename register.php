<?php
session_start();
include 'config.php';

if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit();
}

$err = "";
$msg = "";

if($_SERVER['REQUEST_METHOD']=='POST'){

    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];
    $role  = $_POST['role'];

    // basic checks
    if($name=="" || $email=="" || $pass==""){
        $err = "Please fill all fields.";
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $err = "Email format is not valid.";
    }elseif(strlen($pass)<6){
        $err = "Password too short, min 6 chars.";
    }elseif(empty($_FILES['profile_image']['name'])){
        $err = "Please upload a profile picture.";
    }else{

        // check if email used already
        $chk = mysqli_query($conn,"SELECT id FROM users WHERE email='$email'");
        if(mysqli_num_rows($chk)>0){
            $err = "This email is already registered.";
        }else{
            $hpass = password_hash($pass, PASSWORD_DEFAULT);

            // handle image upload
            $imgName  = $_FILES['profile_image']['name'];
            $tmpPath  = $_FILES['profile_image']['tmp_name'];
            $ext      = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
            $allowed  = array('jpg','jpeg','png','gif');

            if(!in_array($ext, $allowed)){
                $err = "Only jpg, jpeg, png and gif allowed.";
            }else{
                $newName = uniqid().'.'.$ext;
                $dest    = "uploads/".$newName;

                if(move_uploaded_file($tmpPath, $dest)){
                    $q = "INSERT INTO users(name,email,password,role,profile_image) VALUES('$name','$email','$hpass','$role','$newName')";
                    if(mysqli_query($conn,$q)){
                        $msg = "Registered successfully! You can login now.";
                    }else{
                        $err = "DB error, try again.";
                    }
                }else{
                    $err = "Image upload failed.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box; margin:0; padding:0;}
body{
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d0a0a 100%);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px 0;
}
.wrap{
    background:#fff;
    width:380px;
    padding:30px 26px;
    border-radius:12px;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
}
.logo{text-align:center; margin-bottom:16px;}
.logo span{font-size:28px; font-weight:700; color:#c8102e; letter-spacing:2px;}
.logo p{font-size:12px; color:#888;}
h2{text-align:center; color:#1a1a1a; margin-bottom:16px; font-size:19px; font-weight:600;}
.photo-area{ text-align:center; margin-bottom:16px; }
.photo-area img{
    width:90px; height:90px; border-radius:50%;
    object-fit:cover; border:3px solid #c8102e;
    display:block; margin:0 auto 8px;
}
.pick-btn{
    background:#c8102e; color:white;
    padding:6px 16px; border-radius:20px;
    font-size:13px; cursor:pointer;
    display:inline-block; font-family:'Poppins', sans-serif;
    font-weight:500;
}
.pick-btn:hover{background:#a50d26;}
input[type=file]{display:none;}
.hint{font-size:11px; color:#aaa; margin-top:4px;}
hr{border:none; border-top:1px solid #eee; margin:14px 0;}
input[type=text],input[type=email],input[type=password],select{
    width:100%; padding:10px 12px; margin:6px 0;
    border:2px solid #eee; border-radius:8px;
    font-size:14px; font-family:'Poppins', sans-serif;
    transition: border-color 0.2s;
}
input:focus,select:focus{border-color:#c8102e; outline:none;}
button[type=submit]{
    width:100%; padding:11px; margin-top:10px;
    background:#c8102e; color:white;
    border:none; border-radius:8px;
    font-size:15px; font-weight:600;
    font-family:'Poppins', sans-serif;
    cursor:pointer; transition: background 0.2s;
}
button[type=submit]:hover{background:#a50d26;}
.err{
    color:#c8102e; font-size:13px; text-align:center;
    margin-bottom:10px; background:#fff0f0;
    padding:8px 10px; border-radius:6px;
    border-left:3px solid #c8102e;
}
.suc{
    color:#1a7a1a; font-size:13px; text-align:center;
    margin-bottom:10px; background:#f0fff0;
    padding:8px 10px; border-radius:6px;
    border-left:3px solid #28a745;
}
.foot{text-align:center; margin-top:14px; font-size:13px; color:#888;}
.foot a{color:#c8102e; font-weight:500; text-decoration:none;}
.foot a:hover{text-decoration:underline;}
</style>
</head>
<body>
<div class="wrap">
    <div class="logo">
        <span>CR7</span>
        <p>Social Network</p>
    </div>
    <h2>Create Account</h2>
    <?php if($err) echo "<p class='err'>$err</p>"; ?>
    <?php if($msg) echo "<p class='suc'>$msg</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="photo-area">
            <img id="prev" src="https://via.placeholder.com/90?text=Photo" alt="">
            <label class="pick-btn" for="profile_image">📷 Choose Photo</label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="showPreview(event)">
            <p class="hint">JPG, PNG or GIF — required</p>
        </div>
        <hr>
        <input type="text" name="name" placeholder="Full Name"
            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
        <input type="email" name="email" placeholder="Email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <input type="password" name="password" placeholder="Password (min 6 characters)">
        <select name="role">
            <option value="user">Normal User</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Register</button>
    </form>

    <div class="foot">
        Already registered? <a href="index.php">Login</a>
    </div>
</div>

<script>
function showPreview(e){
    var f = e.target.files[0];
    if(f){
        var reader = new FileReader();
        reader.onload = function(ev){
            document.getElementById('prev').src = ev.target.result;
        }
        reader.readAsDataURL(f);
    }
}
</script>
</body>
</html>
