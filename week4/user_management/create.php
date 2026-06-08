<?php
require_once 'db.php';

$errors = [];
$input  = ['username' => '', 'email' => '', 'age' => ''];

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
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO users (username, email, age) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $input['username'], $input['email'], $age);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit;
        }

        $errors[] = '登録に失敗しました。';
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>新規ユーザー登録</h1>
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
                    value="<?php echo htmlspecialchars($input['age'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">登録する</button>
                <a href="index.php" class="btn">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>
