<?php
session_start();
require_once 'functions.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if (login($user, $pass)) {
        header('Location: tracker.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – Previous Leg Tracker</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="centered">
<div class="card small-card">
    <h1>Previous Leg Tracker</h1>
    <p class="subtitle">Sign in to check previous leg delays</p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="username">User</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Log in</button>
    </form>
</div>
</body>
</html>

