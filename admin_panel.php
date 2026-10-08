<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// 1. ተማሪ መመዝገቢያ
if (isset($_POST['register_student'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $sql = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$password', 'student')";
    if (mysqli_query($conn, $sql)) { $msg = "ተማሪ $fullname ተመዝግቧል!"; }
}

// ተማሪ ዲሊት ማድረጊያ
if (isset($_POST['delete_user'])) {
    $u_id = intval($_POST['user_id']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$u_id");
    $msg = "ተማሪው ጠፍቷል!";
}

// ተማሪ ኤዲት ማድረጊያ
if (isset($_POST['update_user'])) {
    $u_id = intval($_POST['user_id']);
    $f_name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $u_pass = mysqli_real_escape_string($conn, $_POST['password']);
    mysqli_query($conn, "UPDATE users SET fullname='$f_name', password='$u_pass' WHERE id=$u_id");
    $msg = "የተማሪው መረጃ ታድሷል!";
}

// 2. አዲስ ኮርስ መጨመሪያ
if (isset($_POST['add_course'])) {
    $c_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $c_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    mysqli_query($conn, "INSERT INTO courses (course_name, course_code) VALUES ('$c_name', '$c_code')");
    $msg = "አዲስ ኮርስ ተጨምሯል!";
}

// ኮርስ ኤዲት ማድረጊያ
if (isset($_POST['update_course'])) {
    $c_id = intval($_POST['course_id']);
    $c_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $c_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    mysqli_query($conn, "UPDATE courses SET course_name='$c_name', course_code='$c_code' WHERE id=$c_id");
    $msg = "ኮርሱ ታድሷል!";
}

// 3. አዲስ ቻፕተር መጨመሪያ
if (isset($_POST['add_chapter'])) {
    $course_id = intval($_POST['course_id']);
    $ch_num = intval($_POST['chapter_num']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $file = mysqli_real_escape_string($conn, $_POST['file_path']);
    mysqli_query($conn, "INSERT INTO chapters (course_id, chapter_num, title, content, file_path) VALUES ($course_id, $ch_num, '$title', '$content', '$file')");
    $msg = "አዲስ ምዕራፍ ተጨምሯል!";
}

// 4. ፊድባክ ስታተስ መቀየሪያ
if (isset($_POST['reply_comment'])) {
    $com_id = intval($_POST['comment_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE comments SET status='$status' WHERE id=$com_id");
    $msg = "የፊድባክ ሁኔታ ተቀይሯል!";
}

// ዳታዎችን ማምጣት
$users = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY id DESC");
$courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
$comments = mysqli_query($conn, "SELECT comments.*, users.fullname, chapters.title FROM comments JOIN users ON comments.user_id = users.id JOIN chapters ON comments.chapter_id = chapters.id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Full</title>
    <style>
        :root { --primary: #0866ff; --bg: #f0f2f5; --white: #fff; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding: 10px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: var(--white); padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .nav-bar a { text-decoration: none; color: var(--primary); font-weight: bold; font-size: 14px; }
        .card { background: var(--white); padding: 15px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        h3 { color: var(--primary); margin-top: 0; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; font-size: 18px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn { padding: 10px; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; }
        .btn-blue { background: var(--primary); width: 100%; }
        .btn-green { background: #42b72a; width: 100%; }
        .btn-red { background: #d93025; padding: 5px; font-size: 11px; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 13px; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>

<div class="nav-bar">
    <strong>Admin Dashboard</strong>
    <div>
        <a href="courses.php">📖 View Courses</a> | 
        <a href="logout.php" style="color:red;">Logout</a>
    </div>
</div>

<?php if($msg) echo "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:8px; text-align:center; margin-bottom:15px;'>$msg</div>"; ?>

<div class="card">
    <h3>👤 Students Management</h3>
    <form method="POST" style="margin-bottom: 20px;">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="password" placeholder="Password" required>
        <button type="submit" name="register_student" class="btn btn-green">Register Student</button>
    </form>
    
    <div class="table-wrapper">
        <table>
            <tr><th>Full Name</th><th>User</th><th>Pass</th><th>Action</th></tr>
            <?php while($u = mysqli_fetch_assoc($users)): ?>
            <form method="POST">
                <tr>
                    <td><input type="text" name="fullname" value="<?php echo $u['fullname']; ?>" style="width:100px;"></td>
                    <td><small><?php echo $u['username']; ?></small></td>
                    <td><input type="text" name="password" value="<?php echo $u['password']; ?>" style="width:80px;"></td>
                    <td>
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="update_user" class="btn btn-blue" style="width:auto; padding:5px;">Save</button>
                        <button type="submit" name="delete_user" class="btn btn-red" onclick="return confirm('Delete?')">X</button>
                    </td>
                </tr>
            </form>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<div class="card">
    <h3>📚 Manage Courses</h3>
    <form method="POST" style="margin-bottom: 20px;">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <input type="text" name="course_code" placeholder="Subject Code" required>
        <button type="submit" name="add_course" class="btn btn-blue">Add New Course</button>
    </form>

    <div class="table-wrapper">
        <table>
            <tr><th>Code</th><th>Course Name</th><th>Action</th></tr>
            <?php while($c = mysqli_fetch_assoc($courses)): ?>
            <form method="POST">
                <tr>
                    <td><input type="text" name="course_code" value="<?php echo $c['course_code']; ?>" style="width:70px;"></td>
                    <td><input type="text" name="course_name" value="<?php echo $c['course_name']; ?>" style="width:130px;"></td>
                    <td>
                        <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                        <button type="submit" name="update_course" class="btn btn-blue" style="width:auto; padding:5px;">Update</button>
                    </td>
                </tr>
            </form>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<div class="card">
    <h3>➕ Add Chapter</h3>
    <form method="POST">
        <select name="course_id" required>
            <option value="">Select Course</option>
            <?php mysqli_data_seek($courses, 0); while($c = mysqli_fetch_assoc($courses)) echo "<option value='".$c['id']."'>".$c['course_name']."</option>"; ?>
        </select>
        <input type="number" name="chapter_num" placeholder="Chapter No." required>
        <input type="text" name="title" placeholder="Chapter Title" required>
        <input type="text" name="file_path" placeholder="File Name (e.g. lesson1.pdf)">
        <textarea name="content" placeholder="Description/Summary"></textarea>
        <button type="submit" name="add_chapter" class="btn btn-blue">Upload Chapter</button>
    </form>
</div>

<div class="card">
    <h3>💬 Student Feedback</h3>
    <div class="table-wrapper">
        <table>
            <tr><th>Student</th><th>Comment</th><th>Status</th></tr>
            <?php mysqli_data_seek($comments, 0); while($com = mysqli_fetch_assoc($comments)): ?>
            <form method="POST">
                <tr>
                    <td><strong><?php echo $com['fullname']; ?></strong></td>
                    <td><small><?php echo $com['comment_text']; ?></small></td>
                    <td>
                        <input type="hidden" name="comment_id" value="<?php echo $com['id']; ?>">
                        <select name="status" onchange="this.form.submit()" style="width:auto; font-size:11px;">
                            <option value="new" <?php if($com['status']=='new') echo 'selected'; ?>>New</option>
                            <option value="Updated" <?php if($com['status']=='Updated') echo 'selected'; ?>>Updated</option>
                        </select>
                        <input type="hidden" name="reply_comment">
                    </td>
                </tr>
            </form>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>