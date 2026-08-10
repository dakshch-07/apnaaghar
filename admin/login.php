<?php
session_start();
require_once '../includes/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password_hash'])) {
                // Login success
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apnaa Ghar - Admin Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #C79A4A;
            --primary-hover: #B8893A;
            --bg: #F4F1EA;
            --card-left-bg: #EAE3D5;
            --text-heading: #0B1B33;
            --text-body: #6A6A6A;
            --border: #E8E3DA;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        
        body {
            
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-body);
        }
        
        .login-card {
    display: flex;
    width: 100%;
    max-width: 950px;
    background: transparent;
    border-radius: 20px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    overflow: hidden;
    animation: scaleUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    margin: 2rem;
    border: 1px solid rgba(199,154,74,0.3);
}
        
        .login-left {
    flex: 1;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 3rem;
    position: relative;
}
        
        /* Mix blend mode removes the white background from the logo */
        .login-left img {
            width: 80%;
            max-width: 300px;
            mix-blend-mode: multiply;
            animation: fadeUp 1s ease-out 0.2s backwards;
        }
        
        .login-left p {
            margin-top: 1rem;
            color: var(--text-heading);
            font-weight: 600;
            letter-spacing: 1px;
            animation: fadeUp 1s ease-out 0.4s backwards;
        }
        
        .login-right {
    flex: 1;
    padding: 4rem 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: rgba(11, 27, 51, 0.9);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-left: 1px solid rgba(255,255,255,0.05);
}
        
        .login-title {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.5rem;
    text-align: center;
    animation: fadeUp 0.8s ease-out 0.2s backwards;
}
        
        .login-subtitle {
    text-align: center;
    margin-bottom: 2rem; margin-top: 0.5rem;
    font-size: 0.95rem;
    color: rgba(255,255,255,0.8);
    animation: fadeUp 0.8s ease-out 0.3s backwards;
}
        
        .form-group {
            margin-bottom: 1.5rem;
            animation: fadeUp 0.8s ease-out 0.4s backwards;
        }
        
        .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #ffffff;
}
        
        .form-control {
    width: 100%;
    padding: 0.9rem 1.2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s;
    background: rgba(255, 255, 255, 0.05);
    color: #ffffff;
}
.form-control::placeholder {
    color: rgba(255, 255, 255, 0.4);
}
        
        .form-control:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(11, 27, 51, 0.88);
    box-shadow: 0 0 0 4px rgba(199, 154, 74, 0.15);
}
        
        .btn-login {
    width: 100%;
    padding: 1rem;
    background: #C79A4A;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 1rem;
    animation: fadeUp 0.8s ease-out 0.5s backwards;
}
        
        .btn-login:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(199, 154, 74, 0.3);
        }
        
        .error-msg {
    background: rgba(231, 76, 60, 0.2);
    color: #ff6b6b;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    font-weight: 500;
    border-left: 4px solid #ff6b6b;
    animation: slideDownFade 0.5s ease-out;
    display: flex;
    align-items: center;
    gap: 10px;
}
        
        @keyframes scaleUp {
            0% { opacity: 0; transform: scale(0.95) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @media (max-width: 768px) {
            .login-card {
    display: flex;
    width: 100%;
    max-width: 950px;
    background: transparent;
    border-radius: 20px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    overflow: hidden;
    animation: scaleUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    margin: 2rem;
    border: 1px solid rgba(199,154,74,0.3);
}
            .login-left {
    flex: 1;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 3rem;
    position: relative;
}
            .login-right {
    flex: 1;
    padding: 4rem 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: rgba(11, 27, 51, 0.9);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-left: 1px solid rgba(255,255,255,0.05);
}
        }
            .login-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: linear-gradient(rgba(11, 27, 51, 0.75), rgba(11, 27, 51, 0.75)), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
    z-index: -2;
    animation: kenBurns 20s infinite alternate ease-in-out;
}

@keyframes kenBurns {
    0% { transform: scale(1); }
    100% { transform: scale(1.08); }
}
        

        
        /* Make the card pop out even more against the dark background */
        .login-card {
    display: flex;
    width: 100%;
    max-width: 950px;
    background: transparent;
    border-radius: 20px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    overflow: hidden;
    animation: scaleUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    margin: 2rem;
    border: 1px solid rgba(199,154,74,0.3);
}

        .success-msg {
            background: rgba(46, 125, 50, 0.2);
            color: #4CAF50;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 4px solid #4CAF50;
            animation: slideDownFade 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        @keyframes slideDownFade {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
</style>
</head>
<body>
<div class="login-bg"></div>


<div class="login-card">
    <div class="login-left">
        <img src="../logo.png" alt="Apnaa Ghar">
        <p>APNAA GHAR ADMIN</p>
    </div>
    
    <div class="login-right">
        <h2 class="login-title">Secure Portal</h2>
        <p class="login-subtitle">Sign in to your administrative account</p>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="loginForm" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required autocomplete="off" placeholder="admin@apnaghar">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password" placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>
</div>



</body>
</html>



