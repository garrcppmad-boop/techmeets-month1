<?php
require_once 'db.php';

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

if ($id === 0) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT id, title, content, author FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    $conn->close();
    header('Location: index.php');
    exit;
}

$input = [
    'title'   => $post['title'],
    'content' => $post['content'],
    'author'  => $post['author'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['title']   = trim($_POST['title']   ?? '');
    $input['content'] = trim($_POST['content'] ?? '');
    $input['author']  = trim($_POST['author']  ?? '');

    if ($input['title'] === '') {
        $errors[] = 'タイトルは必須です。';
    }
    if ($input['content'] === '') {
        $errors[] = '本文は必須です。';
    }
    if ($input['author'] === '') {
        $errors[] = '投稿者名は必須です。';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE posts SET title = ?, content = ?, author = ? WHERE id = ?"
        );
        $stmt->bind_param("sssi", $input['title'], $input['content'], $input['author'], $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: show.php?id=' . $id);
            exit;
        }

        $errors[] = '更新に失敗しました。';
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>記事編集</h1>
            <a href="show.php?id=<?php echo $id; ?>" class="back-link">← 記事に戻る</a>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
            <p><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label>タイトル <span class="required">*</span></label>
                <input type="text" name="title"
                    value="<?php echo htmlspecialchars($input['title'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>投稿者名 <span class="required">*</span></label>
                <input type="text" name="author"
                    value="<?php echo htmlspecialchars($input['author'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>本文 <span class="required">*</span></label>
                <textarea name="content" rows="12"><?php echo htmlspecialchars($input['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">更新する</button>
                <a href="show.php?id=<?php echo $id; ?>" class="btn">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>
