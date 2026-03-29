<?php
session_start();
include 'config.php';

// only admin allowed here
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: index.php");
    exit();
}

$ok  = "";
$fail = "";

// update user info
if(isset($_POST['update_user'])){
    $uid   = $_POST['user_id'];
    $uname = mysqli_real_escape_string($conn, $_POST['name']);
    $uemail= mysqli_real_escape_string($conn, $_POST['email']);
    $urole = $_POST['role'];

    $upd = "UPDATE users SET name='$uname', email='$uemail', role='$urole' WHERE id=$uid";
    if(mysqli_query($conn,$upd)){
        $ok = "User updated!";
    }else{
        $fail = "Could not update user.";
    }
}

// grab all users
$all_users = mysqli_query($conn,"SELECT * FROM users");

// all posts with who posted them
$all_posts = mysqli_query($conn,
    "SELECT posts.*, users.name, users.profile_image
     FROM posts
     JOIN users ON posts.user_id=users.id
     ORDER BY posts.created_at DESC"
);

// quick stats
$cnt_users    = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM users"));
$cnt_posts    = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM posts"));
$cnt_comments = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM comments"));
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box; margin:0; padding:0;}
body{font-family:'Poppins', sans-serif; background:#f2f2f2;}

.nav{
    background:#1a1a1a;
    padding:13px 28px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:3px solid #c8102e;
}
.nav .brand{color:#c8102e; font-size:20px; font-weight:700; letter-spacing:2px;}
.nav .badge{
    background:#c8102e; color:white;
    padding:3px 10px; border-radius:20px;
    font-size:11px; margin-left:9px;
    font-weight:500; vertical-align:middle;
}
.nav a{
    color:white; text-decoration:none;
    background:#c8102e; padding:7px 15px;
    border-radius:8px; font-size:13px;
    font-weight:500; transition: background 0.2s;
}
.nav a:hover{background:#a50d26;}

/* stats row */
.stats{
    display:flex; gap:14px;
    max-width:980px; margin:20px auto 0; padding:0 14px;
}
.sbox{
    flex:1; background:white; border-radius:10px;
    padding:16px 18px; text-align:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    border-top:3px solid #c8102e;
}
.sbox h2{font-size:28px; color:#c8102e; font-weight:700;}
.sbox p{font-size:12px; color:#888; margin-top:3px; font-weight:500;}

/* tabs */
.tabs{
    display:flex; max-width:980px;
    margin:18px auto 0; padding:0 14px;
}
.tbtn{
    padding:10px 24px; cursor:pointer;
    background:#ddd; border:none;
    font-size:14px; border-radius:8px 8px 0 0;
    margin-right:3px; font-family:'Poppins', sans-serif;
    font-weight:500; transition: all 0.2s;
}
.tbtn.active{background:white; font-weight:600; color:#c8102e;}

.tsec{display:none; max-width:980px; margin:0 auto; padding:0 14px 22px;}
.tsec.show{display:block;}

.card{
    background:white; padding:20px;
    border-radius:0 10px 10px 10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

/* users table */
table{width:100%; border-collapse:collapse;}
th,td{border:1px solid #f0f0f0; padding:10px; font-size:13px; text-align:left;}
th{background:#1a1a1a; color:white; font-weight:500;}
tr:hover{background:#fafafa;}
td input[type=text],td input[type=email]{
    padding:5px 8px; border:2px solid #eee;
    border-radius:6px; width:115px; font-size:12px;
    font-family:'Poppins', sans-serif;
}
td input:focus{outline:none; border-color:#c8102e;}
td select{
    padding:5px; border:2px solid #eee;
    border-radius:6px; font-size:12px;
    font-family:'Poppins', sans-serif;
}
.ubtn{
    background:#c8102e; color:white;
    border:none; padding:6px 11px;
    border-radius:6px; cursor:pointer;
    font-size:12px; font-family:'Poppins', sans-serif;
    font-weight:500; transition: background 0.2s;
}
.ubtn:hover{background:#a50d26;}

/* post cards in admin */
.apost{
    border:1px solid #f0f0f0; border-radius:10px;
    padding:14px; margin-bottom:12px;
}
.ahead{display:flex; align-items:center; gap:10px; margin-bottom:8px;}
.ahead img{width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #c8102e;}
.ahead .inf h4{font-size:13px; font-weight:600; color:#1a1a1a;}
.ahead .inf span{font-size:11px; color:#aaa;}
.acontent{font-size:13px; color:#333; margin-bottom:10px; line-height:1.5;}
.ameta{font-size:12px; color:#999; display:flex; gap:13px; margin-bottom:9px;}
.aactions{display:flex; gap:8px; flex-wrap:wrap;}
.lbtn{
    background:#c8102e; color:white;
    border:none; padding:6px 13px;
    border-radius:7px; cursor:pointer;
    font-size:12px; font-family:'Poppins', sans-serif;
    font-weight:500; transition: background 0.2s;
}
.lbtn:hover{background:#a50d26;}
.delbtn{
    background:#1a1a1a; color:white;
    border:none; padding:6px 13px;
    border-radius:7px; cursor:pointer;
    font-size:12px; font-family:'Poppins', sans-serif;
    font-weight:500; transition: background 0.2s;
}
.delbtn:hover{background:#333;}

.cmtlist{border-top:1px solid #f0f0f0; padding-top:8px; margin-top:8px;}
.cmtitem{font-size:12px; color:#666; margin-bottom:4px;}
.cmtitem strong{color:#c8102e;}

.ok{
    color:#1a7a1a; font-size:13px; margin-bottom:10px;
    background:#f0fff0; padding:8px 12px;
    border-radius:6px; border-left:3px solid #28a745;
}
.no{
    color:#c8102e; font-size:13px; margin-bottom:10px;
    background:#fff0f0; padding:8px 12px;
    border-radius:6px; border-left:3px solid #c8102e;
}
</style>
</head>
<body>

<div class="nav">
    <div>
        <span class="brand">CR7</span>
        <span class="badge">Administrator</span>
    </div>
    <a href="logout.php">Logout</a>
</div>

<div class="stats">
    <div class="sbox"><h2><?php echo $cnt_users; ?></h2><p>Total Users</p></div>
    <div class="sbox"><h2><?php echo $cnt_posts; ?></h2><p>Total Posts</p></div>
    <div class="sbox"><h2><?php echo $cnt_comments; ?></h2><p>Total Comments</p></div>
</div>

<div class="tabs">
    <button class="tbtn active" onclick="openTab(event,'tusers')">👥 Users</button>
    <button class="tbtn" onclick="openTab(event,'tposts')">📝 Posts</button>
</div>

<!-- USERS -->
<div id="tusers" class="tsec show">
<div class="card">
    <?php if($ok)   echo "<p class='ok'>$ok</p>"; ?>
    <?php if($fail) echo "<p class='no'>$fail</p>"; ?>
    <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Photo</th><th>Edit</th></tr>
    <?php while($u = mysqli_fetch_assoc($all_users)): ?>
    <tr>
        <td><?php echo $u['id']; ?></td>
        <td><?php echo $u['name']; ?></td>
        <td><?php echo $u['email']; ?></td>
        <td><?php echo $u['role']; ?></td>
        <td><img src="uploads/<?php echo $u['profile_image']; ?>" width="38" height="38" style="border-radius:50%;object-fit:cover;border:2px solid #c8102e;"></td>
        <td>
            <form method="POST" style="display:flex; gap:5px; flex-wrap:wrap;">
                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                <input type="text"  name="name"  value="<?php echo $u['name']; ?>">
                <input type="email" name="email" value="<?php echo $u['email']; ?>">
                <select name="role">
                    <option value="user"  <?php if($u['role']=='user')  echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if($u['role']=='admin') echo 'selected'; ?>>Admin</option>
                </select>
                <button type="submit" name="update_user" class="ubtn">Save</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
    </table>
</div>
</div>

<!-- POSTS -->
<div id="tposts" class="tsec">
<div class="card">
    <?php while($p = mysqli_fetch_assoc($all_posts)):
        $pid   = $p['id'];
        $lcnt  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM likes WHERE post_id=$pid"))['c'];
        $ccnt  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM comments WHERE post_id=$pid"))['c'];
        $mylike= mysqli_num_rows(mysqli_query($conn,"SELECT id FROM likes WHERE post_id=$pid AND user_id=".$_SESSION['user_id'])) > 0;
        $pcmts = mysqli_query($conn,
            "SELECT comments.*, users.name as n
             FROM comments JOIN users ON comments.user_id=users.id
             WHERE post_id=$pid ORDER BY comments.created_at ASC"
        );
    ?>
    <div class="apost">
        <div class="ahead">
            <img src="uploads/<?php echo $p['profile_image']; ?>" alt="">
            <div class="inf">
                <h4><?php echo $p['name']; ?></h4>
                <span><?php echo $p['created_at']; ?></span>
            </div>
        </div>
        <div class="acontent"><?php echo nl2br(htmlspecialchars($p['content'])); ?></div>
        <div class="ameta">
            <span>👍 <?php echo $lcnt; ?> Likes</span>
            <span>💬 <?php echo $ccnt; ?> Comments</span>
        </div>
        <div class="aactions">
            <form method="POST" action="like.php">
                <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
                <button type="submit" class="lbtn"><?php echo $mylike?'👍 Liked':'👍 Like'; ?></button>
            </form>
            <form method="POST" action="delete_post.php">
                <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
                <button type="submit" class="delbtn" onclick="return confirm('Delete post?')">🗑 Delete</button>
            </form>
        </div>

        <?php if($ccnt>0): ?>
        <div class="cmtlist">
            <?php while($c = mysqli_fetch_assoc($pcmts)): ?>
            <div class="cmtitem"><strong><?php echo $c['n']; ?>:</strong> <?php echo htmlspecialchars($c['comment']); ?></div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="comment.php" style="display:flex; gap:8px; margin-top:10px;">
            <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
            <input type="text" name="comment" placeholder="Add comment..." required
                style="flex:1; padding:6px 10px; border:2px solid #eee; border-radius:7px; font-size:12px; font-family:'Poppins',sans-serif;">
            <button type="submit" class="lbtn">Send</button>
        </form>
    </div>
    <?php endwhile; ?>
</div>
</div>

<script>
function openTab(e, id){
    document.querySelectorAll('.tsec').forEach(function(t){ t.classList.remove('show'); });
    document.querySelectorAll('.tbtn').forEach(function(b){ b.classList.remove('active'); });
    document.getElementById(id).classList.add('show');
    e.target.classList.add('active');
}
</script>
</body>
</html>
