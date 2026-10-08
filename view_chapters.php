<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['course_id'])) {
    $course_id = mysqli_real_escape_string($conn, $_GET['course_id']);
    
    $course_query = "SELECT course_name FROM courses WHERE id = $course_id";
    $course_res = mysqli_query($conn, $course_query);
    $course_data = mysqli_fetch_assoc($course_res);
    
    if (!$course_data) {
        header("Location: courses.php");
        exit();
    }
    
    $sql = "SELECT * FROM chapters WHERE course_id = $course_id ORDER BY chapter_num ASC";
    $result = mysqli_query($conn, $sql);
} else {
    header("Location: courses.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course_data['course_name']); ?> - IT Prep</title>
    <style>
        :root { 
            --primary: #10b981; /* ደማቅ አረንጓዴ */
            --primary-dark: #059669; 
            --bg: #f0fdf4; /* በጣም ቀላ ያለ አረንጓዴ ዳራ */
            --white: #ffffff; 
            --text-dark: #064e3b;
            --text-light: #374151;
        }

        body { 
            font-family: 'Inter', -apple-system, system-ui, sans-serif; 
            background-color: var(--bg); 
            margin: 0; 
            padding: 0; 
        }

        .container { max-width: 600px; margin: 0 auto; min-height: 100vh; }

        /* Green Gradient Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 35px 20px;
            color: white;
            border-bottom-left-radius: 25px;
            border-bottom-right-radius: 25px;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
        }

        .back-link { 
            color: #ecfdf5;
            text-decoration: none; 
            font-size: 14px; 
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            background: rgba(255,255,255,0.15);
            width: fit-content;
            padding: 6px 14px;
            border-radius: 50px;
            backdrop-filter: blur(5px);
        }

        .page-header h2 { 
            margin: 0; 
            font-size: 24px; 
            font-weight: 800;
        }

        /* Chapter Cards */
        .chapter-list { padding: 20px 15px; }

        .chapter-card {
            background: var(--white);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            border: 1px solid #d1fae5;
            transition: all 0.3s ease;
        }

        .chapter-card:hover {
            transform: scale(1.02);
            border-color: var(--primary);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .num-box {
            background: #ecfdf5;
            color: var(--primary-dark);
            min-width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            margin-right: 15px;
            border: 1px solid #d1fae5;
        }

        .chapter-info { flex: 1; }

        .chapter-title { 
            display: block;
            font-weight: 700; 
            font-size: 16px; 
            color: var(--text-dark);
        }

        .chapter-meta { 
            font-size: 12px; 
            color: var(--primary-dark);
            font-weight: 600;
            margin-top: 3px;
        }

        .play-icon {
            background: var(--primary);
            color: white;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .no-data { text-align: center; padding: 60px; color: var(--text-light); }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <a href="courses.php" class="back-link">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back
        </a>
        <h2><?php echo htmlspecialchars($course_data['course_name']); ?></h2>
        <p style="opacity: 0.9; font-size: 14px; margin-top: 4px;">Continue your learning journey</p>
    </div>

    <div class="chapter-list">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                ?>
                <a href="read_chapter.php?id=<?php echo $row['id']; ?>" class="chapter-card">
                    <div class="num-box">
                        <?php echo $row['chapter_num']; ?>
                    </div>
                    <div class="chapter-info">
                        <span class="chapter-title"><?php echo htmlspecialchars($row['title']); ?></span>
                        <span class="chapter-meta">Read Chapter</span>
                    </div>
                    <div class="play-icon">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path>
                        </svg>
                    </div>
                </a>
                <?php
            }
        } else {
            echo "<div class='no-data'><p>No chapters available yet.</p></div>";
        }
        ?>
    </div>
</div>

</body>
</html>