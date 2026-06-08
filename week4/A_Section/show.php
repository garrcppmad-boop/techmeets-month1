<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id, title, content, author, created_at, updated_at FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$post) {
    header('Location: index.php');
    exit;
}

$isEdited = $post['updated_at'] !== $post['created_at'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>記事詳細</h1>
            <a href="index.php" class="back-link">← 一覧に戻る</a>
        </div>

        <article class="post-detail">
            <h2 class="post-detail-title">
                <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <div class="post-meta">
                <span class="post-author">投稿者：<?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="post-date">投稿日：<?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if ($isEdited): ?>
                <span class="post-date">更新日：<?php echo htmlspecialchars($post['updated_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?>
            </div>
            <div class="post-detail-actions">
                <a href="edit.php?id=<?php echo (int)$post['id']; ?>" class="btn btn-edit">編集</a>
                <a href="delete.php?id=<?php echo (int)$post['id']; ?>" class="btn btn-delete">削除</a>
            </div>
        </article>
    </div>
</body>
</html>
