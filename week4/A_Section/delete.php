<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT title, author FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$post) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事削除の確認</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>記事の削除</h1>
            <a href="index.php" class="back-link">← 一覧に戻る</a>
        </div>

        <div class="confirm-box">
            <p class="confirm-message">以下の記事を削除してよろしいですか？この操作は取り消せません。</p>
            <table class="confirm-table">
                <tr>
                    <th>タイトル</th>
                    <td><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <th>投稿者</th>
                    <td><?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            </table>
            <form method="POST" action="delete.php?id=<?php echo $id; ?>">
                <div class="form-actions">
                    <button type="submit" class="btn btn-delete">削除する</button>
                    <a href="index.php" class="btn">キャンセル</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
