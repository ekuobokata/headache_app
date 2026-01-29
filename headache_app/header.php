<?php
// --- 共通セッション・ヘッダー処理 ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>頭痛ダイヤリー</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <div class="nav-container">
        <div class="site-info">
            <h1>頭痛ダイヤリー</h1>
            <p>通院に便利な頭痛管理ツール</p>
        </div>

        <div class="user-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="login-status">
                    ログイン中：<?= h($_SESSION["username"]) ?> さん
                </span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class=".btn-edit">ログアウト</button>
                </form>
            <?php else: ?>
                <span class="login-status">ログアウト中</span>
            <?php endif; ?>
        </div>
    </div>
</div>