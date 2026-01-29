<?php
require_once 'db.php';
require_once 'functions.php';

$error = "";

if (!empty($_POST["login"])) {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // 入力されたユーザー名が存在するかDBを検索
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // ユーザーが存在し、かつパスワード（ハッシュ化済み）が一致するかチェック
    if ($user && password_verify($password, $user["password"])) {
        // 一致すればセッションに情報を保存してログイン完了
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        header("Location: index.php");
        exit;
    } else {
        $error = "ユーザー名またはパスワードが違います";
    }
}
include 'header.php';
?>
<div class="container">
    <div class="memo-form" style="max-width: 400px; margin: 0 auto;">
        <h2 style="text-align:center;">ログイン</h2>
        <?php if ($error): ?>
            <p style="color:#ff7675; text-align:center; font-size:14px;"><?= h($error) ?></p>
        <?php endif; ?>
        
        <form action="" method="post">
            <input type="text" name="username" placeholder="ユーザー名" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit" name="login" class="btn-blue">ログイン</button>
        </form>
        <p style="text-align:center; margin-top:15px; font-size:14px;">
            <a href="register.php">新規ユーザー登録はこちら</a>
        </p>
    </div>
</div>
