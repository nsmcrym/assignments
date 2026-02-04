ec2インスタンスを作り、ターミナルからssh ec2-user@{IPアドレス} -i {秘密鍵ファイルのパス}でログインする

## vimのインストール
```sh
sudo yum install vim -y
```

## dockerのインストール、設定
```sh
sudo yum install -y docker
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -a -G docker ec2-user
```

## docker composeのインストール
```sh
sudo mkdir -p /usr/local/lib/docker/cli-plugins/
sudo curl -SL https://github.com/docker/compose/releases/download/v2.36.0/docker-compose-linux-x86_64 -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
```
確認:
```sh
docker compose version
```
## mysqlのインストール
```sh
docker compose exec mysql mysql example_db
```

```sql
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `icon_filename` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE `bbs_entries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `body` TEXT NOT NULL,
    `image_filename` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `user_relationships` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `followee_user_id` INT UNSIGNED NOT NULL,
    `follower_user_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
```


以下の構造で実装する
```
.
└── assignments
    ├── Dockerfile
    ├── compose.yml
    ├── nginx
    │   └── conf.d
    │       └── default.conf
    ├── php.ini
    └── public
        ├── bbs.php
        ├── follow.php
        ├── follow_list.php
        ├── follow_remove.php
        ├── follower_list.php
        ├── login.php
        ├── login_finish.php
        ├── profile.php
        ├── setting
        │   ├── birthday.php
        │   ├── cover.php
        │   ├── icon.php
        │   ├── index.php
        │   └── introduction.php
        ├── signup.php
        ├── signup_finish.php
        ├── timeline.php
        ├── timeline_in.php
        ├── timeline_json.php
        ├── timeline_subquery.php
        └── users.php
```
