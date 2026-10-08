<?php
// 1. Database Connection
include 'db_config.php';
session_start();

$error = "";
$success = "";

// 2. Form Processing
if (isset($_POST['register'])) {
    // Sanitize and trim inputs
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation Logic
    if (empty($fullname) || empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } 
    elseif (strlen($username) < 4) {
        $error = "Username must be at least 4 characters.";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } 
    else {
        // Check if username already exists using Prepared Statement
        $check_stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Username '$username' is already taken!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            // Insert new user using Prepared Statement
            $insert_stmt = $conn->prepare("INSERT INTO users (fullname, username, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $fullname, $username, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                $success = "Registration successful! Redirecting to login...";
                // JavaScript redirect after 2 seconds
                echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error = "Database error: Unable to register. Please try again.";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - IT Prep</title>
    <style>
        :root {
            --primary: #0866ff;
            --success: #42b72a;
            --bg: #f0f2f5;
            --error-bg: #ffebe8;
            --error-text: #d93025;
        }

        body { 
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif; 
            background-color: var(--bg); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }

        .register-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 { color: #1c1e21; font-size: 26px; margin-bottom: 5px; letter-spacing: -0.5px; }
        .subtitle { color: #65676b; margin-bottom: 25px; font-size: 15px; }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: left;
        }
        .alert-error { background: var(--error-bg); color: var(--error-text); border: 1px solid #f5c2c7; }
        .alert-success { background: #e7f3ff; color: #1877f2; border: 1px solid #b6d4fe; }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 12px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px #e7f3ff;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }

        button:hover { background-color: #36a420; }

        .footer-links {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dddfe2;
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Join IT EXIT EXAM Prep</h2>
    <p class="subtitle">Quick and simple registration.</p>

    <?php if($error): ?>
        <div class="alert alert-error"><strong>Error:</strong> <?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success"><strong>Success!</strong> <?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="fullname" placeholder="Full Name" 
               value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
        
        <input type="text" name="username" placeholder="Choose Username" 
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
        
        <input type="password" name="password" placeholder="New Password" required>
        
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        
        <button type="submit" name="register">Sign Up</button>
    </form>

    <div class="footer-links">
        <a href="login.php">Already have an account?</a>
    </div>
</div>

</body>
</html>