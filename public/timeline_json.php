<?php
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

session_start();
if (empty($_SESSION['login_user_id'])) { // 非ログインの場合利用不可 401 で空のものを返す
  header("HTTP/1.1 401 Unauthorized");
  header("Content-Type: application/json");
  print(json_encode(['entries' => []]));
  return;
}

// 現在のログイン情報を取得する
$user_select_sth = $dbh->prepare("SELECT * from users WHERE id = :id");
$user_select_sth->execute([':id' => $_SESSION['login_user_id']]);
$user = $user_select_sth->fetch();

// 投稿データを取得
$sql = 'SELECT bbs_entries.*, users.name AS user_name, users.icon_filename AS user_icon_filename'
  . ' FROM bbs_entries'
  . ' INNER JOIN users ON bbs_entries.user_id = users.id'
  . ' WHERE'
  . (isset($_GET['last_id']) ? ' bbs_entries.id < :last_id AND' : '')
  . '   ('
  . '     bbs_entries.user_id IN'
  . '       (SELECT followee_user_id FROM user_relationships WHERE follower_user_id = :login_user_id)'
  . '     OR bbs_entries.user_id = :login_user_id'
  . '   )'
  . ' ORDER BY bbs_entries.created_at DESC'
  . ' LIMIT 10';
$sql_params = [];
$sql_params[':login_user_id'] = $_SESSION['login_user_id'];
if (isset($_GET['last_id'])) {
  $sql_params[':last_id'] = intval($_GET['last_id']);
}
$select_sth = $dbh->prepare($sql);
$select_sth->execute($sql_params);

$last_id_sql = 'SELECT bbs_entries.id'
  . ' FROM bbs_entries'
  . ' INNER JOIN users ON bbs_entries.user_id = users.id'
  . ' WHERE'
  . '   bbs_entries.user_id IN'
  . '     (SELECT followee_user_id FROM user_relationships WHERE follower_user_id = :login_user_id)'
  . '   OR bbs_entries.user_id = :login_user_id'
  . ' ORDER BY bbs_entries.created_at ASC'
  . ' LIMIT 1';
$last_id_select_sth = $dbh->prepare($last_id_sql);
$last_id_select_sth->execute([':login_user_id' => $_SESSION['login_user_id']]);
$last_id_result = $last_id_select_sth->fetch();
$last_id = intval($last_id_result['id']);

// bodyのHTMLを出力するための関数を用意する
function bodyFilter (string $body): string
{
  $body = htmlspecialchars($body); // エスケープ処理
  $body = nl2br($body); // 改行文字を<br>要素に変換

  return $body;
}

// JSONに吐き出す用のentries
$result_entries = [];
$last_rendered_entry_id = null;
foreach ($select_sth as $entry) {
  $last_rendered_entry_id = $entry['id'];
  $result_entry = [
    'id' => $entry['id'],
    'user_name' => $entry['user_name'],
@@ -54,4 +77,8 @@

header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
print(json_encode([
  'entries' => $result_entries,
  'last_rendered_entry_id' => $last_rendered_entry_id,
  'last_entries_id' => $last_id,
]));
