<?php
//==============================
// BBS用 Boadクラス
//==============================
class Boad {

	//--------------------------
	// 変数の宣言
	//--------------------------
	public $bid;
	public $sname;
	public $name;
	public $count;
	public $rpasset;
	public $wpasset;
	public $default_name;

	//--------------------------
	// コンストラクタ
	//--------------------------
	function Boad($array) {
		$this->bid = $array["bid"];
		$this->sname = $array["sname"];
		$this->name = $array["name"];
		$this->count = $array["count"];
		$this->rpasset = $array["rpasset"];
		$this->wpasset = $array["wpasset"];
		$this->default_name = $array["default_name"];
	}
}
?>
