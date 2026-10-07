<?php
require_once __DIR__ . '/includes/auth.php';

if (admin_is_logged_in()) {
    redirect('index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $password = (string) ($_POST['password'] ?? '');
        if (admin_login($password)) {
            redirect('index.php');
        }
        $error = 'That password is not right. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Staff login — <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="login-screen">
    <div class="login-screen__box">
        <span class="login-screen__mark">&#10022;</span>
        <h1><?= e(SITE_NAME) ?></h1>
        <p class="login-screen__sub">Staff area</p>

        <form method="post" action="login.php">
            <?= csrf_field() ?>
            <label class="field">
                <span>Password</span>
                <input type="password" name="password" required autofocus autocomplete="current-password">
            </label>

            <?php if ($error): ?>
                <p class="alert alert--error" style="margin-top:16px"><?= e($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="btn btn--primary btn--block" style="margin-top:20px">Log in</button>
        </form>

        <p class="login-screen__hint">
            The default password is <code>ember123</code>.<br>
            Change <code>ADMIN_PASSWORD</code> in <code>config.php</code>.
        </p>
        <a class="login-screen__back" href="../index.php">&larr; Back to the site</a>
    </div>
</div>

</body>
</html>
