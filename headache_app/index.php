<?php
// 設定ファイルと共通関数の読み込み
require_once 'db.php';
require_once 'functions.php';
ensure_logged_in();

// ログインしていなければログイン画面へ飛ばす
$user_id = $_SESSION["user_id"];
$edit_data = null;

// --- 1. 削除処理 ---
if (isset($_GET['delete'])) {
    // URLに ?delete=ID がある場合、そのIDかつ自分のデータのみ削除
    $stmt = $pdo->prepare("DELETE FROM headache_logs WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    header("Location: index.php");
    exit;
}

// --- 2. 編集データの取得 ---
if (isset($_GET['edit'])) {
    // URLに ?edit=ID がある場合、フォームに表示するために1件取得
    $stmt = $pdo->prepare("SELECT * FROM headache_logs WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['edit'], $user_id]);
    $edit_data = $stmt->fetch();
}

// --- 3. 保存・更新処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // フォームから送られてきた各値を変数に代入（三項演算子でチェックボックス対応）
    $date = $_POST['date'];
    $is_period = isset($_POST['is_period']) ? 1 : 0;
    $am_i = $_POST['intensity_am'];
    $am_m = isset($_POST['med_am']) ? 1 : 0;
    $pm_i = $_POST['intensity_pm'];
    $pm_m = isset($_POST['med_pm']) ? 1 : 0;
    $night_i = $_POST['intensity_night'];
    $night_m = isset($_POST['med_night']) ? 1 : 0;
    $impact = $_POST['impact_level'];
    $improve = $_POST['improvement_level'];
    $note = $_POST['note'];

    if (!empty($_POST['id'])) {
        // IDがある場合は既存データの「更新(UPDATE)」
        $sql = "UPDATE headache_logs SET date=?, is_period=?, am_intensity=?, am_med=?, pm_intensity=?, pm_med=?, night_intensity=?, night_med=?, impact_level=?, improvement_level=?, note=? WHERE id=? AND user_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $is_period, $am_i, $am_m, $pm_i, $pm_m, $night_i, $night_m, $impact, $improve, $note, $_POST['id'], $user_id]);
    } else {
        // IDがない場合は「新規登録(INSERT)」
        $sql = "INSERT INTO headache_logs (user_id, date, is_period, am_intensity, am_med, pm_intensity, pm_med, night_intensity, night_med, impact_level, improvement_level, note, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now', 'localtime'))";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $date, $is_period, $am_i, $am_m, $pm_i, $pm_m, $night_i, $night_m, $impact, $improve, $note]);
    }
    header("Location: index.php");
    exit;
}

// 日付の昇順に取得
$stmt = $pdo->prepare("SELECT * FROM headache_logs WHERE user_id = ? ORDER BY date ASC");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll();

include 'header.php';// 共通ヘッダー読み込み
?>

<div class="container">
    <div class="memo-form">
        <h2 style="margin-top:0;"><?= $edit_data ? '記録を編集' : '記録の入力' ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= h($edit_data['id'] ?? '') ?>">
            <div style="display:flex; gap:20px; margin-bottom:15px; align-items:center;">
                <input type="date" name="date" value="<?= h($edit_data['date'] ?? date('Y-m-d')) ?>" style="width:200px; margin-bottom:0;" required>
                <label style="margin-bottom:0;"><input type="checkbox" name="is_period" <?= ($edit_data['is_period'] ?? 0) ? 'checked' : '' ?>> 生理あり</label>
            </div>
            
            <table class="headache-input-table">
                <tr><th>時間帯</th><th>痛みの程度</th><th>薬</th></tr>
                <?php foreach(['am'=>'午前','pm'=>'午後','night'=>'夜'] as $k=>$v): 
                    $intensity = $edit_data[$k.'_intensity'] ?? 'なし';
                    $med = $edit_data[$k.'_med'] ?? 0;
                ?>
                <tr>
                    <td><?= $v ?></td>
                    <td>
                        <select name="intensity_<?= $k ?>">
                            <?php foreach(['なし','軽度','中程度','重度'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $intensity === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="checkbox" name="med_<?= $k ?>" <?= $med ? 'checked' : '' ?>></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <textarea name="note" class="memo-textarea" placeholder="メモ"><?= h($edit_data['note'] ?? '') ?></textarea>
            <button type="submit" name="save" class="btn-blue"><?= $edit_data ? '更新する' : '保存する' ?></button>
            <?php if($edit_data): ?><a href="index.php" style="display:block; text-align:center; margin-top:10px; font-size:14px; color:#666;">キャンセル</a><?php endif; ?>
        </form>
    </div>

    <section class="log-table-section">
        <h2 style="border-left: 5px solid #4da6ff; padding-left: 10px; margin-bottom: 20px;">記録データ一覧</h2>
        <div class="table-wrapper">
            <table class="data-view-table">
                <thead>
                    <tr>
                        <th>日付</th><th>生理</th><th>午前</th><th>午後</th><th>夜間</th><th>メモ</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="white-space:nowrap;"><?= h($log['date']) ?></td>
                        <td><?= $log['is_period'] ? '◯' : '-' ?></td>
                        <td><?= h($log['am_intensity']) ?><?= $log['am_med'] ? '◯' : '' ?></td>
                        <td><?= h($log['pm_intensity']) ?><?= $log['pm_med'] ? '◯' : '' ?></td>
                        <td><?= h($log['night_intensity']) ?><?= $log['night_med'] ? '◯' : '' ?></td>
                        <td class="text-left"><?= mb_strimwidth(h($log['note']), 0, 40, "...") ?></td>
                        <td style="white-space:nowrap;">
                            <a href="?edit=<?= $log['id'] ?>" class="btn-edit">編集</a>
                            <a href="?delete=<?= $log['id'] ?>" class="btn-delete" onclick="return confirm('削除しますか？')">削除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>