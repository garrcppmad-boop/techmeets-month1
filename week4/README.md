Week4 - ユーザー管理システム（PHP + MySQL + Docker）

学習内容
DockerでMySQL環境を構築し、PHPでCRUD操作を行うユーザー管理システムを実装した。

環境構成

| 技術 | 詳細 |
|------|------|
| PHP | ローカル（XAMPPなど） |
| MySQL | 8.0（Docker） |
| phpMyAdmin | Docker（ブラウザからDB操作） |

起動方法

```bash
# Dockerコンテナを起動
docker-compose up -d

# phpMyAdmin: http://localhost:8080
# ユーザー: root / パスワード: root
```

DBセットアップ

phpMyAdminで以下のSQLを実行してテーブルを作成する。

```sql
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    age        INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ファイル構成

week4/
├── docker-compose.yml          # MySQL + phpMyAdmin の Docker設定
├── db_test.php                 # DB接続テスト用スクリプト
└── user_management/
    ├── db.php                  # DB接続処理（共通）
    ├── index.php               # ユーザー一覧（Read）
    ├── create.php              # ユーザー登録（Create）
    ├── edit.php                # ユーザー編集（Update）
    └── delete.php              # ユーザー削除（Delete）

機能

| 機能 | ファイル | 説明 |
|------|----------|------|
| 一覧表示 | index.php | 全ユーザーを登録日時の降順で表示 |
| 新規登録 | create.php | フォームからユーザーを登録 |
| 編集 | edit.php | URLパラメータ（`?id=`）でユーザーを特定して更新 |
| 削除 | delete.php | URLパラメータ（`?id=`）でユーザーを削除 |


つまずいたポイント

・extension=mysqli が有効になっておらず、データベースになかなか接続できなかった。環境設定に苦戦していた。

