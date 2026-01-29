<?php
require_once 'db.php';
require_once 'functions.php';
ensure_logged_in();

$user_id = $_SESSION["user_id"];

// 保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $sql = "INSERT INTO headache_logs (user_id, date, is_period, am_intensity, am_med, pm_intensity, pm_med, night_intensity, night_med, impact_level, improvement_level, note, created_at)
            VALUES (:uid, :date, :period, :am_i, :am_m, :pm_i, :pm_m, :night_i, :night_m, :impact, :improve, :note, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":uid" => $user_id,
        ":date" => $_POST['date'],
        ":period" => isset($_POST['is_period']) ? 1 : 0,
        ":am_i" => $_POST['intensity_am'],
        ":am_m" => isset($_POST['med_am']) ? 1 : 0,
        ":pm_i" => $_POST['intensity_pm'],
        ":pm_m" => isset($_POST['med_pm']) ? 1 : 0,
        ":night_i" => $_POST['intensity_night'],
        ":night_m" => isset($_POST['med_night']) ? 1 : 0,
        ":impact" => $_POST['impact_level'],
        ":improve" => $_POST['improvement_level'],
        ":note" => $_POST['note']
    ]);
}

// データ取得
$stmt = $pdo->prepare("SELECT * FROM headache_logs WHERE user_id = :uid ORDER BY date DESC");
$stmt->execute([":uid" => $user_id]);
$logs = $stmt->fetchAll();

include 'header.php';
?>

<div class="container">
    <section class="entry-form card">
        <h2>本日の記録</h2>
        <form method="post">
            <div class="form-row">
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                <label><input type="checkbox" name="is_period"> 生理中</label>
            </div>

            <table class="log-table">
                <thead>
                    <tr>
                        <th>時間帯</th>
                        <th>痛みの強さ</th>
                        <th>薬</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(['am'=>'午前', 'pm'=>'午後', 'night'=>'夜'] as $k => $v): ?>
                    <tr>
                        <td><?= $v ?></td>
                        <td>
                            <select name="intensity_<?= $k ?>">
                                <option value="なし">なし</option>
                                <option value="弱">弱</option>
                                <option value="中">中</option>
                                <option value="強">強</option>
                            </select>
                        </td>
                        <td><input type="checkbox" name="med_<?= $k ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="range-group">
                <label>生活への影響度 (1:小 - 5:大): <input type="number" name="impact_level" min="1" max="5" value="1"></label>
                <label>改善度 (1:なし - 5:完治): <input type="number" name="improvement_level" min="1" max="5" value="1"></label>
            </div>

            <textarea name="note" placeholder="メモ（天気、食事、睡眠など）"></textarea>
            <button type="submit" name="save" class="btn-primary">記録を保存</button>
        </form>
    </section>

    <section class="log-list">
        <h2>過去の記録</h2>
        <?php foreach ($logs as $log): ?>
            <div class="card log-item">
                <div class="log-header">
                    <span class="log-date"><?= h($log['date']) ?></span>
                    <?php if($log['is_period']): ?><span class="badge-period">生理</span><?php endif; ?>
                </div>
                <div class="log-grid">
                    <div>午前: <?= h($log['am_intensity']) ?> <?= $log['am_med'] ? '💊' : '' ?></div>
                    <div>午後: <?= h($log['pm_intensity']) ?> <?= $log['pm_med'] ? '💊' : '' ?></div>
                    <div>夜間: <?= h($log['night_intensity']) ?> <?= $log['night_med'] ? '💊' : '' ?></div>
                </div>
                <p class="log-note"><?= nl2br(h($log['note'])) ?></p>
            </div>
        <?php endforeach; ?>
    </section>
</div>

<?php include 'footer.php'; ?>