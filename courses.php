<?php
include 'db_config.php';
session_start();

// Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_display_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['username'];

// Inspirational Quotes in Amharic
$quotes = [
    "ስኬት ማለት ከውድቀት ወደ ውድቀት ጉልበት ሳይቀንሱ መጓዝ ነው።",
    "ትልቁ ስኬት ዛሬ የምታደርገው ጥቃቅን ጥረት ውጤት ነው።",
    "ዕውቀት ለነፃነት ቁልፍ ነው። በደንብ አጥኑ!",
    "የዛሬ ድካምህ የነገ ኩራትህ ነው።",
    "ፈተናን ለማለፍ ትልቁ ምስጢር አስቀድሞ መዘጋጀት ነው።",
    "አይቻልም የሚለው ቃል በሰነፎች መዝገብ ላይ ብቻ የሚገኝ ነው።"
];
$daily_quote = $quotes[array_rand($quotes)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Dashboard - IT Prep</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --primary-glow: rgba(16, 185, 129, 0.4);
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg); 
            margin: 0; 
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Fixed Ticker Style - Now Starts Immediately */
        .ticker-wrapper {
            background: var(--gradient);
            color: white;
            padding: 12px 0;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px var(--primary-glow);
            position: relative;
            overflow: hidden;
        }

        .ticker-text {
            display: inline-block;
            white-space: nowrap;
            padding-left: 50%; /* Starts more to the center for faster first appearance */
            animation: ticker 30s linear infinite;
        }

        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            padding: 15px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(16, 185, 129, 0.1);
        }

        .welcome-logo { font-size: 22px; font-weight: 800; color: var(--primary); letter-spacing: -1px; }

        .logout-link { 
            color: #ef4444; 
            text-decoration: none;
            background: #fff1f2;
            padding: 10px 20px; 
            border-radius: 12px; 
            font-weight: 700;
            font-size: 14px;
            transition: 0.3s;
        }
        .logout-link:hover { background: #ffe4e6; transform: translateY(-2px); }

        /* Main Layout */
        .main-container { padding: 40px 8%; max-width: 1300px; margin: 0 auto; }

        /* Hero Section */
        .hero-section {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 30px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            border: 1px solid rgba(16, 185, 129, 0.1);
            position: relative;
        }

        .hero-section h2 { font-size: 32px; margin: 10px 0; font-weight: 800; }
        .badge { background: #dcfce7; color: #065f46; padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; text-transform: uppercase; }

        /* Quote Card */
        .quote-card {
            background: #f0fdf4;
            border-left: 5px solid var(--primary);
            padding: 22px;
            border-radius: 15px;
            margin: 25px 0;
            font-style: italic;
            color: #166534;
            font-size: 18px;
            line-height: 1.6;
        }

        /* Course Grid */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .course-card {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 30px;
            border: 1px solid #f1f5f9;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.1);
            border-color: var(--primary);
        }

        .icon-circle {
            width: 65px;
            height: 65px;
            background: #ecfdf5;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .course-card h3 { font-size: 21px; margin: 10px 0; font-weight: 700; }
        .course-code-tag { font-size: 12px; color: var(--text-muted); font-weight: 700; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; }

        .action-btn {
            background: #f1f5f9;
            color: var(--text-dark);
            text-decoration: none;
            padding: 16px;
            border-radius: 18px;
            margin-top: 25px;
            font-weight: 800;
            text-align: center;
            transition: 0.3s;
        }

        .course-card:hover .action-btn {
            background: var(--gradient);
            color: white;
        }

        @media (max-width: 768px) {
            .main-container { padding: 20px; }
            .hero-section h2 { font-size: 26px; }
        }
    </style>
</head>
<body>

<div class="ticker-wrapper">
    <div class="ticker-text">
        🚀 IT EXIT EXAM PORTAL &nbsp;&nbsp; | &nbsp;&nbsp; Developed by Chacha &nbsp;&nbsp; | &nbsp;&nbsp; Support: 0919961315 / 0989209561 &nbsp;&nbsp; | &nbsp;&nbsp; 💡 Wisdom of the day: "<?php echo $daily_quote; ?>" &nbsp;&nbsp; | &nbsp;&nbsp; Your success is our mission!
    </div>
</div>

<nav class="navbar">
    <div class="welcome-logo">🎓 CHA-IT-WSU</div>
    <div style="display: flex; align-items: center; gap: 20px;">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin_panel.php" style="text-decoration:none; color:var(--primary); font-weight:800; font-size:14px;">ADMIN</a>
        <?php endif; ?>
        <a href="logout.php" class="logout-link">LOGOUT</a>
    </div>
</nav>

<div class="main-container">
    
    <div class="hero-section">
        <span class="badge">Student Dashboard</span>
        <h2>👋 Welcome back, MR. <?php echo htmlspecialchars($user_display_name); ?>!</h2>
        <p style="color:var(--text-muted); font-size: 16px;">Ready to study? Choose a course below to begin your preparation for the Exit Exam.</p>
        
        <div class="quote-card">
            "<?php echo $daily_quote; ?>"
        </div>
    </div>

    <div style="margin-bottom: 35px;">
        <h3 style="font-size: 24px; font-weight: 800; margin: 0;">Available Courses</h3>
        <p style="color: var(--text-muted); margin: 5px 0 0 0;">Select a subject to explore chapters and materials.</p>
    </div>

    <div class="course-grid">
        <?php
        $query = "SELECT * FROM courses";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $initial = strtoupper(substr($row['course_name'], 0, 1));
                ?>
                <div class="course-card">
                    <div>
                        <div class="icon-circle"><?php echo $initial; ?></div>
                        <span class="course-code-tag">ID: <?php echo htmlspecialchars($row['course_code']); ?></span>
                        <h3><?php echo htmlspecialchars($row['course_name']); ?></h3>
                    </div>
                    <a href="view_chapters.php?course_id=<?php echo $row['id']; ?>" class="action-btn">Open Course Content →</a>
                </div>
                <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; text-align: center; padding: 60px; color:var(--text-muted);'>No courses found in the database.</div>";
        }
        ?>
    </div>
</div>

</body>
</html>