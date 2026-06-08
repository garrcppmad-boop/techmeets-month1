<?php
require_once 'db.php';
$conn = getDBConnection();

$stmt = $conn->prepare(
    "SELECT id, title, content, author, created_at FROM posts ORDER BY created_at DESC"
);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ブログ記事一覧</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>ブログ記事一覧</h1>
            <a href="create.php" class="btn btn-primary">＋ 新規投稿</a>
        </div>

        <?php if ($result->num_rows === 0): ?>
        <div class="empty-state">
            <p>記事がまだありません。最初の記事を投稿しましょう。</p>
        </div>
        <?php else: ?>
        <div class="post-list">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post-card">
                <div class="post-card-body">
                    <h2 class="post-title">
                        <a href="show.php?id=<?php echo (int)$row['id']; ?>">
                            <?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h2>
                    <p class="post-excerpt">
                        <?php echo htmlspecialchars(mb_substr($row['content'], 0, 100), ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (mb_strlen($row['content']) > 100): ?>…<?php endif; ?>
                    </p>
                    <div class="post-meta">
                        <span class="post-author">投稿者：<?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="post-date"><?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="post-card-actions">
                    <a href="show.php?id=<?php echo (int)$row['id']; ?>" class="btn">詳細</a>
                    <a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-edit">編集</a>
                    <a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-delete">削除</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
