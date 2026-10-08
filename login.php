<?php
include 'db_config.php';
session_start();

$error = "";

if (isset($_POST['login'])) {
    // 1. መረጃውን ከፎርሙ መቀበል
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // 2. ተጠቃሚውን በዳታቤዝ ውስጥ መፈለግ
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // 3. የተመሰጠረውን ፓስወርድ ማረጋገጥ
            if (password_verify($password, $user['password'])) {
                // የሴሽን መረጃዎችን መያዝ
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                // 4. እንደ ተጠቃሚው ሚና (Role) ወደሚገባው ገጽ መላክ
                if ($user['role'] === 'admin') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: courses.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Prep Portal</title>
    <style>
        :root { --primary: #0866ff; --bg: #f0f2f5; }
        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: var(--bg); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 12px 28px rgba(0,0,0,0.12); 
            width: 100%; 
            max-width: 400px; 
            text-align: center;
        }

        h2 { color: var(--primary); font-size: 32px; margin-bottom: 10px; font-weight: bold; }
        p.welcome-msg { color: #606770; margin-bottom: 25px; font-size: 16px; }

        .error-box { 
            background: #ffebe8; 
            color: #d93025; 
            padding: 12px; 
            border-radius: 6px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            border: 1px solid #d93025; 
            text-align: left;
        }

        input { 
            width: 100%; 
            padding: 14px; 
            margin: 10px 0; 
            border: 1px solid #dddfe2; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 16px;
        }

        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px #e7f3ff; }

        button { 
            width: 100%; 
            padding: 12px; 
            background: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: bold; 
            font-size: 18px; 
            cursor: pointer; 
            margin-top: 10px;
        }

        button:hover { background: #0055d4; }
        .divider { border-bottom: 1px solid #dadde1; margin: 20px 0; }
        .reg-link { 
            display: inline-block;
            background: #42b72a; 
            color: white; 
            padding: 12px 20px; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: bold;
            font-size: 15px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>CHACHA IT DEVE</h2>
    <p class="welcome-msg">Log in to your account</p>
    
    <?php if(!empty($error)): ?>
        <div class='error-box'><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Log In</button>
    </form>
    
    <div class="divider"></div>
    
    <p style="font-size: 14px; color: #606770;">New to the system?</p>
    <a href="index.php" class="reg-link">Create New Account</a>
</div>

</body>
</html>