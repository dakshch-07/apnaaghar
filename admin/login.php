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
            --primary: #0F5C4A;
            --primary-hover: #0C4A3B;
            --bg: #F8F8F6;
            --text-heading: #1C1C1C;
            --text-body: #6A6A6A;
            --border: #E8E3DA;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        
        body {
            background-color: var(--bg);
            display: flex;
            min-height: 100vh;
            color: var(--text-body);
        }
        
        .login-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        
        .login-illustration {
            flex: 1;
            background: linear-gradient(rgba(15, 92, 74, 0.8), rgba(15, 92, 74, 0.9)), url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 4rem;
            color: #fff;
            position: relative;
        }
        
        .login-illustration h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
            animation: fadeUp 1s ease-out;
        }
        
        .login-illustration p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 400px;
            animation: fadeUp 1s ease-out 0.2s backwards;
        }
        
        .login-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #fff;
            padding: 2rem;
            position: relative;
        }
        
        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.8s ease-out;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2.5rem;
            color: var(--primary);
            font-size: 2rem;
        }
        
        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .login-subtitle {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-heading);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fafafa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(15, 92, 74, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(15, 92, 74, 0.3);
        }
        
        .error-msg {
            background: rgba(192, 57, 43, 0.1);
            color: #C0392B;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 4px solid #C0392B;
            animation: shake 0.5s;
        }
        
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @media (max-width: 900px) {
            .login-illustration { display: none; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-illustration">
        <h1>Apnaa Ghar<br>Admin Portal</h1>
        <p>Manage premium real estate properties, gallery images, and exclusive listings seamlessly from your tailored dashboard.</p>
    </div>
    
    <div class="login-form-container">
        <div class="login-form-wrapper">
            <div class="login-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="login-title">Welcome Back</h2>
            <p class="login-subtitle">Sign in to your administrative account</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required autocomplete="username" placeholder="admin@apnaghar" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-login">Sign In <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
    // Simple HTML5 validation styling
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            const inputs = this.querySelectorAll('input');
            inputs.forEach(input => {
                if (!input.validity.valid) {
                    input.style.borderColor = '#C0392B';
                }
            });
        }
    });
    
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            if (this.validity.valid) {
                this.style.borderColor = 'var(--primary)';
            }
        });
    });
</script>

</body>
</html>
