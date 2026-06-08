<?php
require_once 'db.php';
$conn = getDBConnection();

$stmt = $conn->prepare(
    "SELECT id, username, email, age, created_at FROM users ORDER BY created_at DESC"
);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>ユーザー一覧</h1>
            <a href="create.php" class="btn btn-primary">＋ 新規ユーザー登録</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ユーザー名</th>
                    <th>メール</th>
                    <th>年齢</th>
                    <th>登録日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="6" class="empty">登録されているユーザーはいません。</td>
                </tr>
                <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $row['age'] !== null ? (int)$row['age'] : '—'; ?></td>
                    <td><?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-edit">編集</a>
                        <a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-delete">削除</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
