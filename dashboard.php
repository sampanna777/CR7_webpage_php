<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

$res = mysqli_query($conn,"SELECT * FROM users WHERE id=$uid");
$me  = mysqli_fetch_assoc($res);

// get all posts newest first
$allposts = mysqli_query($conn,
    "SELECT posts.*, users.name as uname, users.profile_image as uimg
     FROM posts
     JOIN users ON posts.user_id = users.id
     ORDER BY posts.created_at DESC"
);
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box; margin:0; padding:0;}
body{font-family:'Poppins', sans-serif; background:#f2f2f2;}

.topbar{
    background:#1a1a1a;
    padding:12px 28px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:3px solid #c8102e;
}
.topbar .brand{
    font-size:22px; font-weight:700;
    color:#c8102e; letter-spacing:2px;
}
.topbar a{
    color:white; text-decoration:none;
    background:#c8102e; padding:7px 16px;
    border-radius:8px; font-size:13px;
    font-weight:500; transition: background 0.2s;
}
.topbar a:hover{background:#a50d26;}

.main{
    max-width:880px;
    margin:20px auto;
    padding:0 14px;
    display:flex;
    gap:18px;
}

.left{ width:240px; flex-shrink:0; }
.profile-box{
    background:white;
    border-radius:10px;
    padding:20px;
    text-align:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}
.profile-box img{
    width:88px; height:88px;
    border-radius:50%; object-fit:cover;
    border:3px solid #c8102e;
}
.profile-box h3{margin:10px 0 3px; font-size:15px; color:#1a1a1a;}
.profile-box p{color:#888; font-size:12px;}
.profile-box .badge{
    display:inline-block; background:#fff0f0;
    color:#c8102e; padding:3px 12px;
    border-radius:20px; font-size:11px;
    margin-top:8px; font-weight:500;
}
.profile-box a.editbtn{
    display:block; margin-top:12px;
    background:#c8102e; color:white;
    padding:8px; border-radius:8px;
    text-decoration:none; font-size:13px;
    font-weight:500; transition: background 0.2s;
}
.profile-box a.editbtn:hover{background:#a50d26;}

.feed{flex:1;}

.new-post{
    background:white;
    border-radius:10px;
    padding:16px;
    margin-bottom:16px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}
.new-post textarea{
    width:100%; padding:10px;
    border:2px solid #eee; border-radius:8px;
    resize:none; font-size:14px;
    font-family:'Poppins', sans-serif;
    transition: border-color 0.2s;
}
.new-post textarea:focus{outline:none; border-color:#c8102e;}
.new-post button{
    float:right; margin-top:8px;
    background:#c8102e; color:white;
    border:none; padding:8px 20px;
    border-radius:8px; cursor:pointer;
    font-size:14px; font-weight:500;
    font-family:'Poppins', sans-serif;
    transition: background 0.2s;
}
.new-post button:hover{background:#a50d26;}

.pcard{
    background:white; border-radius:10px;
    padding:16px; margin-bottom:16px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}
.phead{display:flex; align-items:center; gap:10px; margin-bottom:10px;}
.phead img{width:42px; height:42px; border-radius:50%; object-fit:cover; border:2px solid #c8102e;}
.phead .who h4{font-size:14px; color:#1a1a1a; font-weight:600;}
.phead .who span{font-size:11px; color:#aaa;}
.pbody{font-size:14px; line-height:1.6; margin-bottom:12px; color:#333;}
.pfoot{
    display:flex; gap:9px;
    border-top:1px solid #f0f0f0; padding-top:10px;
}
.pfoot form{display:inline;}
.abtn{
    background:none; border:2px solid #ddd;
    padding:5px 14px; border-radius:8px;
    cursor:pointer; font-size:13px; color:#555;
    font-family:'Poppins', sans-serif;
    transition: all 0.2s;
}
.abtn:hover{background:#f9f9f9; border-color:#c8102e; color:#c8102e;}
.abtn.on{color:#c8102e; border-color:#c8102e; font-weight:600;}
.dbtn{
    background:none; border:2px solid #c8102e;
    color:#c8102e; padding:5px 14px;
    border-radius:8px; cursor:pointer;
    font-size:13px; margin-left:auto;
    font-family:'Poppins', sans-serif;
    transition: all 0.2s;
}
.dbtn:hover{background:#c8102e; color:white;}

.cmts{margin-top:12px; border-top:1px solid #f0f0f0; padding-top:10px;}
.cmt{display:flex; gap:8px; margin-bottom:8px;}
.cmt img{width:30px; height:30px; border-radius:50%; object-fit:cover;}
.cbubble{
    background:#f9f9f9; border-radius:12px;
    padding:6px 12px; font-size:13px;
}
.cbubble strong{font-size:12px; display:block; color:#c8102e;}
.addcmt{display:flex; gap:8px; margin-top:8px;}
.addcmt input{
    flex:1; padding:7px 13px;
    border:2px solid #eee; border-radius:20px;
    font-size:13px; font-family:'Poppins', sans-serif;
    transition: border-color 0.2s;
}
.addcmt input:focus{outline:none; border-color:#c8102e;}
.addcmt button{
    background:#c8102e; color:white;
    border:none; padding:7px 15px;
    border-radius:20px; cursor:pointer;
    font-size:13px; font-family:'Poppins', sans-serif;
    font-weight:500; transition: background 0.2s;
}
.addcmt button:hover{background:#a50d26;}
</style>
</head>
<body>

<div class="topbar">
    <div class="brand">CR7</div>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="left">
        <div class="profile-box">
            <img src="uploads/<?php echo $me['profile_image']; ?>" alt="pic">
            <h3><?php echo $me['name']; ?></h3>
            <p><?php echo $me['email']; ?></p>
            <span class="badge">User</span>
            <a href="edit_profile.php" class="editbtn">Edit Profile</a>
        </div>
    </div>

    <div class="feed">
        <div class="new-post">
            <form method="POST" action="create_post.php">
                <textarea name="content" rows="3" placeholder="Whats on your mind, <?php echo $me['name']; ?>?" required></textarea>
                <button type="submit">Post</button>
                <div style="clear:both;"></div>
            </form>
        </div>

        <?php while($p = mysqli_fetch_assoc($allposts)): ?>
        <?php
            $pid = $p['id'];
            $lcnt = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM likes WHERE post_id=$pid"))['c'];
            $liked = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM likes WHERE post_id=$pid AND user_id=$uid")) > 0;
            $cmts  = mysqli_query($conn,
                "SELECT comments.*, users.name as n, users.profile_image as pi
                 FROM comments
                 JOIN users ON comments.user_id=users.id
                 WHERE post_id=$pid ORDER BY comments.created_at ASC"
            );
        ?>
        <div class="pcard">
            <div class="phead">
                <img src="uploads/<?php echo $p['uimg']; ?>" alt="">
                <div class="who">
                    <h4><?php echo $p['uname']; ?></h4>
                    <span><?php echo $p['created_at']; ?></span>
                </div>
            </div>

            <div class="pbody"><?php echo nl2br(htmlspecialchars($p['content'])); ?></div>

            <div class="pfoot">
                <form method="POST" action="like.php">
                    <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
                    <button class="abtn <?php echo $liked?'on':''; ?>" type="submit">
                        👍 <?php echo $lcnt; ?> Like<?php echo $lcnt!=1?'s':''; ?>
                    </button>
                </form>

                <?php if($p['user_id']==$uid): ?>
                <form method="POST" action="delete_post.php">
                    <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
                    <button class="dbtn" type="submit" onclick="return confirm('Delete this post?')">🗑 Delete</button>
                </form>
                <?php endif; ?>
            </div>

            <div class="cmts">
                <?php while($c = mysqli_fetch_assoc($cmts)): ?>
                <div class="cmt">
                    <img src="uploads/<?php echo $c['pi']; ?>" alt="">
                    <div class="cbubble">
                        <strong><?php echo $c['n']; ?></strong>
                        <?php echo htmlspecialchars($c['comment']); ?>
                    </div>
                </div>
                <?php endwhile; ?>

                <form method="POST" action="comment.php" class="addcmt">
                    <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
                    <input type="text" name="comment" placeholder="Write a comment..." required>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>

    </div>
</div>

</body>
</html>
