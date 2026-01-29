<?php
try {
    // データベースファイルへの接続（なければ作成される）
    $pdo = new PDO('sqlite:database.sqlite');
    
    // エラーが起きた際に例外を投げる設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // データを取得した際、デフォルトで連想配列（カラム名がキー）にする設定
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ユーザー管理用テーブルの作成
    $pdo->query("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT
    )");
    
    // 頭痛記録用テーブルの作成
    $pdo->query("CREATE TABLE IF NOT EXISTS headache_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        date TEXT NOT NULL,
        is_period INTEGER DEFAULT 0,
        am_intensity TEXT,
        am_med INTEGER DEFAULT 0,
        pm_intensity TEXT,
        pm_med INTEGER DEFAULT 0,
        night_intensity TEXT,
        night_med INTEGER DEFAULT 0,
        impact_level INTEGER,
        improvement_level INTEGER,
        note TEXT,
        created_at TEXT
    )");
} catch (PDOException $e) {
    // 接続に失敗した場合はエラーメッセージを出して終了
    exit('データベース接続エラー: ' . $e->getMessage());
}