<?php
require_once 'db.php';

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

if ($id === 0) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

// 編集対象のユーザーを取得
$stmt = $conn->prepare("SELECT id, username, email, age FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// フォームの初期値（POSTがなければDBの値を使う）
$input = [
    'username' => $user['username'],
    'email'    => $user['email'],
    'age'      => $user['age'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['username'] = trim($_POST['username'] ?? '');
    $input['email']    = trim($_POST['email'] ?? '');
    $input['age']      = trim($_POST['age'] ?? '');

    if ($input['username'] === '') {
        $errors[] = 'ユーザー名は必須です。';
    }
    if ($input['email'] === '') {
        $errors[] = 'メールアドレスは必須です。';
    }

    if (empty($errors)) {
        $age  = $input['age'] !== '' ? (int)$input['age'] : null;
        $stmt = $conn->prepare(
            "UPDATE users SET username = ?, email = ?, age = ? WHERE id = ?"
        );
        $stmt->bind_param("ssii", $input['username'], $input['email'], $age, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php');
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
    <title>ユーザー編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>ユーザー編集</h1>
            <a href="index.php" class="back-link">← 一覧に戻る</a>
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
                <label>ユーザー名 <span class="required">*</span></label>
                <input type="text" name="username"
                    value="<?php echo htmlspecialchars($input['username'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>メールアドレス <span class="required">*</span></label>
                <input type="email" name="email"
                    value="<?php echo htmlspecialchars($input['email'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>年齢</label>
                <input type="number" name="age" min="0" max="150"
                    value="<?php echo htmlspecialchars((string)$input['age'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">更新する</button>
                <a href="index.php" class="btn">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>
