<?php
//=====================================
// モンスターデータ 更新情報
//=====================================
require_once("/var/www/class/mysql.php");
require_once("/var/www/class/guestdata.php");
require_once("/var/www/functions/template.php");
require_once("/var/www/functions/form.php");
require_once("/var/www/functions/monster.php");

$PAGE_ID = 50020;
$LIMIT = 50;

$hidden = 1;
if(isset($_GET['hidden'])) {
	if($_GET['hidden'] == 0) {
		setcookie("hidden", 0, time() + 86400);
	} else {
		setcookie("hidden", 0, time() - 3600);
	}
}
if(isset($_COOKIE['hidden'])) {
	$hidden = $_COOKIE['hidden'];
}

$title = "モンスターデータ更新履歴";

$user_file = "/etc/mysql-user/user5000.ini";
if($fp_user = fopen($user_file, "r")) {
	$userName = rtrim(fgets($fp_user));
	$password = rtrim(fgets($fp_user));
	$database = rtrim(fgets($fp_user));
} else {
	die("接続設定の読み込みに失敗しました");
}
$data = new GuestData($userName, $password, $database, $hidden);

if(isset($_GET["page"])) {
	if(preg_match("/[^0-9]/", $_GET["page"])) {
		$page = 0;
	} else {
		$page = $_GET["page"];
	}
} else {
	$page = 0;
}

$rows = $data->select_all_l("zone,id,name,updated", "monster", $page * $LIMIT, $LIMIT, "updated", "desc");

if(($page > 0) && ($rows > 0)) {
	$pagelink = "<a href=\"./updinfo.php?page=".($page - 1)."\"".mbi_ack("*").">".mbi("*.")."前のページ</a> | ";
} else {
	$pagelink = mbi("*.")."前のページ | ";
}
if((($page + 1) * 50) < $rows) {
	$pagelink .= "<a href=\"./updinfo.php?page=".($page + 1)."\"".mbi_ack("#").">".mbi("#.")."次のページ</a>";
} else {
	$pagelink .= mbi("#.")."次のページ";
}
?>
<html>
<head>
<?pagehead($title)?>
</head>
<body>
<div id="all">
<h1>更新履歴</h1>
<hr class="normal">
<div class="cnt"><?=$pagelink?></div>
<hr class="normal">
<ul id="linklist">
<?php
if($rows > 0) {
	while($row = $data->fetch()) {
		$id = $row["zone"].str_pad($row["id"], 4, "0", STR_PAD_LEFT);
		$name = $row["name"];
		$upd = $row["updated"];
?>
<li><?=$upd?> <a href="/db/monster/data/?id=<?=$id?>"><?=$name?></a></li>
<?php
	}
}
?>
</ul>
<hr class="normal">
<div class="cnt"><?=$pagelink?></div>
<hr class="normal">
<ul id="footlink">
<li><a href="./"<?=mbi_ack(9)?>><?=mbi("9.")?>モンスターデータ</a></li>
<li><a href="/db/"<?=mbi_ack(9)?>><?=mbi("9.")?>データベース</a></li>
<li><a href="/"<?=mbi_ack(0)?>><?=mbi("0.")?>トップページ</a></li>
</ul>
<?
$data->select_id("accesscount", $PAGE_ID);
$c_data = $data->fetch();
pagefoot($data->access_count("accesscount", $PAGE_ID, $c_data["count"]));
?>
</div>
</body>
</html>
