<?php
include 'db_config.php';
session_start();

// 1. ተማሪው መግባቱን ማረጋገጥ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();    
}

$user_id = $_SESSION['user_id'];

// 2. ከ URL የመጣውን የቻፕተር ID መቀበል
if (isset($_GET['id'])) {
    $chapter_id = mysqli_real_escape_string($conn, $_GET['id']);
} else {
    header("Location: courses.php");
    exit();
}

// 3. የቻፕተሩን መረጃ ከዳታቤዝ ማምጣት
$sql = "SELECT chapters.*, courses.course_name 
        FROM chapters 
        JOIN courses ON chapters.course_id = courses.id 
        WHERE chapters.id = $chapter_id";
$result = mysqli_query($conn, $sql);
$chapter = mysqli_fetch_assoc($result);

if (!$chapter) {
    die("Chapter not found!");
}

// 4. አስተያየት (Comment) ሲሰጥ የሚሰራ ኮድ
if (isset($_POST['submit_comment'])) {
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    
    if (!empty($comment_text)) {
        $insert_sql = "INSERT INTO comments (user_id, chapter_id, comment_text, status) 
                       VALUES ('$user_id', '$chapter_id', '$comment_text', 'new')";
        if (mysqli_query($conn, $insert_sql)) {
            $success_msg = "Feedback sent! The admin will review it.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapter['title']); ?> - IT Prep</title>
    <style>
        :root { 
            --primary: #0866ff; 
            --bg: #f0f2f5; 
            --white: #ffffff; 
            --text-main: #1c1e21;
            --text-sub: #65676b;
        }

        body { 
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
            background-color: var(--bg); 
            margin: 0; 
            padding: 0; 
            color: var(--text-main); 
            line-height: 1.6;
        }

        .content-box { 
            max-width: 800px; 
            margin: 0 auto; 
            background: var(--white); 
            min-height: 100vh;
        }

        .top-nav {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #dddfe2;
            position: sticky;
            top: 0;
            background: var(--white);
            z-index: 100;
        }

        .back-btn { 
            text-decoration: none; 
            color: var(--primary); 
            font-weight: 600; 
            display: flex;
            align-items: center;
        }

        .course-tag { 
            background: #e7f3ff; 
            color: var(--primary); 
            padding: 4px 10px; 
            border-radius: 6px; 
            font-size: 11px; 
            font-weight: bold; 
        }

        .main-article { padding: 25px 20px; }
        h1 { font-size: 24px; margin: 10px 0; color: #050505; }
        .meta-info { color: var(--text-sub); font-size: 14px; margin-bottom: 15px; }

        .reading-content { 
            font-size: 17px; 
            color: #333; 
            white-space: pre-wrap; 
            margin-bottom: 30px;
        }

        /* Improved Download Box with File Name */
        .download-box { 
            background: #f8f9fa; 
            padding: 18px; 
            border-radius: 12px; 
            margin: 30px 0;
            border: 1px solid #e1e4e8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-info-side { display: flex; align-items: center; gap: 12px; }

        .file-icon-box {
            background: var(--primary);
            color: white;
            padding: 10px;
            border-radius: 10px;
            display: flex;
        }

        .download-btn { 
            background: var(--primary); 
            color: white; 
            text-decoration: none; 
            padding: 10px 18px; 
            border-radius: 8px; 
            font-weight: bold;
            font-size: 14px;
        }

        .comment-area {
            background: #f9f9f9;
            padding: 25px 20px;
            border-top: 1px solid #eee;
        }

        textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #dddfe2; 
            border-radius: 10px; 
            margin-top: 10px;
            box-sizing: border-box;
        }

        .btn-send { 
            background: #42b72a; 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 10px; 
            font-weight: bold; 
            width: 100%; 
            margin-top: 10px; 
            cursor: pointer;
        }

        @media (max-width: 480px) {
            .download-box { flex-direction: column; align-items: flex-start; gap: 15px; }
            .download-btn { width: 100%; text-align: center; box-sizing: border-box; }
        }
    </style>
</head>
<body>

<div class="content-box">
    <div class="top-nav">
        <a href="view_chapters.php?course_id=<?php echo $chapter['course_id']; ?>" class="back-btn">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back
        </a>
        <span class="course-tag"><?php echo htmlspecialchars($chapter['course_name']); ?></span>
    </div>

    <div class="main-article">
        <div class="meta-info">Chapter <?php echo $chapter['chapter_num']; ?></div>
        <h1><?php echo htmlspecialchars($chapter['title']); ?></h1>
        
        <div class="reading-content">
            <?php echo nl2br(htmlspecialchars($chapter['content'])); ?>
        </div>

        <?php if(!empty($chapter['file_path'])): ?>
            <div class="download-box">
                <div class="file-info-side">
                    <div class="file-icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 15px;">Study Material</strong>
                        <span style="color:var(--text-sub); font-size: 12px; word-break: break-all;"><?php echo htmlspecialchars($chapter['file_path']); ?></span>
                    </div>
                </div>
                <a href="uploads/<?php echo $chapter['file_path']; ?>" class="download-btn" download>Download</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="comment-area">
        <h3 style="margin:0; font-size:18px;">Ask a Question</h3>
        <?php if(isset($success_msg)) echo "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin:10px 0;'>$success_msg</div>"; ?>
        
        <form method="post">
            <textarea name="comment_text" rows="3" placeholder="Type your question here..." required></textarea>
            <button type="submit" name="submit_comment" class="btn-send">Submit Question</button>
        </form>
    </div>
</div>

</body>
</html>