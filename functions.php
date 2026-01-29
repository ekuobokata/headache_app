<?php
// セッションが開始されていなければ開始する
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// XSS（クロスサイトスクリプティング）対策：HTML特殊文字を無害化する
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ログイン状態をチェックする関数
function ensure_logged_in() {
    $current_file = basename($_SERVER['PHP_SELF']);
    // 未ログイン（sessionが無）かつ ログイン/登録画面以外にアクセスしようとした場合
    if (empty($_SESSION["user_id"])) {
        if ($current_file !== 'login.php' && $current_file !== 'register.php') {
            header("Location: login.php"); // ログイン画面へ強制移動
            exit;
        }
    }
}