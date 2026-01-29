<?php
require_once 'db.php';
require_once 'functions.php';

if (!empty($_POST["register"])) {
    $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$_POST["username"], $pass]);
    header("Location: login.php");
    exit;
}
include 'header.php';
?>
<div class="container card">
    <h2>ユーザー登録</h2>
    <form method="post">
        <input type="text" name="username" placeholder="ユーザー名" required>
        <input type="password" name="password" placeholder="パスワード" required>
        <input type="submit" name="register" value="登録" class="btn-primary">
    </form>
    <a href="login.php">ログイン画面へ</a>
</div>
