<?php
include_once "../../../../mainfile.php";

if($_POST['op']=="GO"){
  start_update1();
}

$ver="1.0 -> 1.1";
$title=_MA_TADPLAYER_AUTOUPDATE1;
$ok=update_chk1();


function update_chk1(){
	global $xoopsDB;
	$sql="select count(`sort`) from ".$xoopsDB->prefix("tad_player");
	$result=$xoopsDB->query($sql);
	if(empty($result)) return false;
	return true;
}


function start_update1(){
	global $xoopsDB;
	$sql="ALTER TABLE ".$xoopsDB->prefix("tad_player")." ADD `sort` smallint(5) unsigned NOT NULL";
	$xoopsDB->queryF($sql) or redirect_header(XOOPS_URL,3,  mysql_error());

	header("location:{$_SERVER["HTTP_REFERER"]}");
	exit;
}
?>
