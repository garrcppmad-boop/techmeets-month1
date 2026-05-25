<?php
$errors = [];
//
$name = $email = $subject = $message = '';
//フォームの各入力値を保持する。最初は空文字で初期化
$submitted = false;
//送信が成功したかどうか

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    //XSS対策
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //フォームが送信されたときの処理
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '')    $errors['name']    = '名前は必須です。';
    if ($email === '') {
        $errors['email'] = 'メールアドレスは必須です。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'メールアドレスの形式が正しくありません。';
    }
    if ($subject === '') $errors['subject'] = '件名は必須です。';
    if ($message === '') $errors['message'] = 'メッセージは必須です。';
    //空入力対策
    if (empty($errors)) {
        $submitted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            padding: 40px 16px;
        }
        .card {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            padding: 40px;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 24px;
            border-left: 4px solid #4a90e2;
            padding-left: 12px;
        }
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            font-size: .875rem;
            font-weight: bold;
            margin-bottom: 6px;
        }
        label .required {
            color: #e74c3c;
            margin-left: 4px;
            font-size: .75rem;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color .2s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            outline: none;
            border-color: #4a90e2;
        }
        input.error, textarea.error { border-color: #e74c3c; }
        textarea { height: 140px; resize: vertical; }
        .error-msg {
            color: #e74c3c;
            font-size: .8rem;
            margin-top: 4px;
        }
        .alert-error {
            background: #fdecea;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #721c24;
            font-size: .875rem;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background .2s;
        }
        button[type="submit"]:hover { background: #357abd; }

        /* 確認画面 */
        .confirm-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .confirm-table th,
        .confirm-table td {
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
            text-align: left;
            vertical-align: top;
        }
        .confirm-table th {
            background: #f0f4ff;
            font-size: .875rem;
            width: 140px;
            white-space: nowrap;
        }
        .confirm-table td { font-size: .95rem; line-height: 1.6; }
        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background: #888;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: .9rem;
            transition: background .2s;
        }
        .back-link:hover { background: #666; }
        .success-icon { font-size: 2rem; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="card">
<?php if ($submitted): ?>
    <div class="success-icon">&#10003;</div>
    <h1>送信内容の確認</h1>
    <table class="confirm-table">
        <tr>
            <th>名前</th>
            <td><?= h($name) ?></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><?= h($email) ?></td>
        </tr>
        <tr>
            <th>件名</th>
            <td><?= h($subject) ?></td>
        </tr>
        <tr>
            <th>メッセージ</th>
            <td><?= nl2br(h($message)) ?></td>
        </tr>
    </table>
    <a href="contact.php" class="back-link">&#8592; フォームに戻る</a>

<?php else: ?>
    <h1>お問い合わせ</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">入力内容にエラーがあります。確認してください。</div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="form-group">
            <label for="name">名前<span class="required">必須</span></label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= h($name) ?>"
                class="<?= isset($errors['name']) ? 'error' : '' ?>"
            >
            <?php if (isset($errors['name'])): ?>
                <p class="error-msg"><?= h($errors['name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">メールアドレス<span class="required">必須</span></label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= h($email) ?>"
                class="<?= isset($errors['email']) ? 'error' : '' ?>"
            >
            <?php if (isset($errors['email'])): ?>
                <p class="error-msg"><?= h($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="subject">件名<span class="required">必須</span></label>
            <input
                type="text"
                id="subject"
                name="subject"
                value="<?= h($subject) ?>"
                class="<?= isset($errors['subject']) ? 'error' : '' ?>"
            >
            <?php if (isset($errors['subject'])): ?>
                <p class="error-msg"><?= h($errors['subject']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="message">メッセージ<span class="required">必須</span></label>
            <textarea
                id="message"
                name="message"
                class="<?= isset($errors['message']) ? 'error' : '' ?>"
            ><?= h($message) ?></textarea>
            <?php if (isset($errors['message'])): ?>
                <p class="error-msg"><?= h($errors['message']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">送信する</button>
    </form>
<?php endif; ?>
</div>
</body>
</html>
