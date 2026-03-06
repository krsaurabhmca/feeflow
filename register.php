<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    }
    else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM institutes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered!";
        }
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO institutes (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['institute_id'] = $pdo->lastInsertId();
                $_SESSION['institute_name'] = $name;
                redirect('dashboard.php');
            }
            else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FeeFlow</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card glass-card fade-in">
            <h1 style="text-align: center; color: var(--primary);">FeeFlow</h1>
            <p style="text-align: center; margin-bottom: 2rem; color: var(--secondary);">Register your institute to get started</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php
endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="name">Institute Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Skyline Academy" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@example.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem;">
                Already have an account? <a href="index.php" style="color: var(--primary); font-weight: 600;">Login</a>
            </p>
        </div>
    </div>
</body>
</html>
