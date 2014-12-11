<?php
//=====================================
// 管理者用 アイテムデータ 追加 更新 削除
//=====================================
require_once("/var/www/class/mysql.php");
require_once("/var/www/class/guestdata.php");
require_once("/var/www/class/admindata.php");
require_once("/var/www/class/form.php");
require_once("/var/www/functions/template.php");
require_once("/var/www/functions/item.php");
require_once("/var/www/functions/xml/item_form_upd.php");
$group_xml = "/var/www/functions/xml/item_group.xml";
$form_login_xml = "/var/www/functions/xml/admin_login_form.xml";
$form_add_xml = "/var/www/functions/xml/item_form_add.xml";
$PAGESIZE = 40;
$table = "items";

$group_id = 10000;
$page = 0;

session_start();

//ログイン
if(isset($_SERVER["REQUEST_METHOD"]) == "POST") {
	if(isset($_POST["submit_login"])) {
		$_SESSION["user"] = $_POST["user"];
		$_SESSION["pass"] = $_POST["pass"];
	}
}

$form = new Form($_SERVER["PHP_SELF"], "POST", "multipart/form-data");
if(isset($_SESSION["user"]) && isset($_SESSION["pass"])) {
	$data = new AdminData($_SESSION["user"], $_SESSION["pass"], "ezdata");
	$data->select_group("id", $table, $group_id, item_group_end($group_id));
	$count = $data->rows();	
}

if(isset($_SERVER["REQUEST_METHOD"]) == "POST") {

	// ログアウト
	if(isset($_POST["submit_logout"])) {
		session_destroy();
		selfpage();
	}

	// 新規作成
	if(isset($_POST["submit_add"])) {
		$cols = array("id","name","text","rare","notrade","price","stack","note","hidden");
		foreach($cols as $col) {
			$values[] = isset($_POST["new_".$col]) ? "'".$_POST["new_".$col]."'" : 0;
		}
		$cols = implode(",", $cols);
		$values = implode(",", $values);
		$data->insert_data($table, $cols, $values);
		$data->timestamp($table, "id=".$_POST["new_id"]);
	}

	// 変更
	if(isset($_POST["submit_upd"])) {
		$id = key($_POST["submit_upd"]);
		$target = "id=".$id;
		$cols = array("name","text","rare","notrade","price","stack","note","hidden");
		foreach($cols as $col) {
			$values[] = isset($_POST[$col][$id]) ? preg_replace("/[\r][\n]/", "\n", $_POST[$col][$id]) : 0;
		}
		$data->update_data($table, $cols, $values, $target);
		$data->timestamp($table, $target);
	}

	// 最初のページ
	if(isset($_POST["submit_group"])) $_POST["page"] = 0;

	//グループの選択
	if(isset($_POST["group"])) $group_id = $_POST["group"];

	//ページの選択
	if(isset($_POST["page"])) $page = $_POST["page"];
}
?>
<html>
<head>
<?=admin_pagehead()?>
</head>
<body>
<?php
if(!isset($_SESSION["user"]) || !isset($_SESSION["pass"])) {
//管理ログイン
?>
<?=$form->start()?>
<?=$form->load_xml_file($form_login_xml)?>
<?=$form->close()?>
管理者ユーザー名とパスワードを入力してください
</body>
</html>
<?php
} else {
//ログイン済
?>
<h3>* * Item List * *</h3>
<?=$form->start()?>
<?=$form->submit("logout", "ログアウト")?>
<div>
アイテムリストに新規追加<br>
<?=$form->load_xml_file($form_add_xml)?>
</div>
<hr>
<div>
グループ
<?=$form->build_select_group($group_xml, $group_id)?>
</div>
<hr>
ページ
<?=$form->build_select_page($count, $PAGESIZE, $page)?>
<hr>
<?php
	$data->select_group_l("*", $table, $group_id, item_group_end($group_id), $page, $PAGESIZE);
	while($row = $data->fetch()){
?>
<hr>
<div>
<?=$form->load_xml_string(xml_item_form_upd($row))?>
</div>
<?php
	}
?>
<hr>
<?=$count?>件ヒット
</form>
</body>
</html>
<?php
}
?>
