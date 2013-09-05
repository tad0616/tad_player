<?php
include_once "../../../../mainfile.php";

if($_POST['op']=="GO"){
  start_update3();
}

$ver="1.1 -> 1.3";
$title=_MA_TADPLAYER_AUTOUPDATE3;
$ok=update_chk3();


function update_chk3(){
	global $xoopsDB;
	$sql="select count(`youtube`) from ".$xoopsDB->prefix("tad_player");
	$result=$xoopsDB->query($sql);
	if(empty($result)) return false;
	return true;
}


function start_update3(){
	global $xoopsDB;
	$sql="ALTER TABLE ".$xoopsDB->prefix("tad_player")." ADD `youtube` varchar(255) NOT NULL default ''";
	$xoopsDB->queryF($sql) or redirect_header(XOOPS_URL,3,  mysql_error());

	header("location:{$_SERVER["HTTP_REFERER"]}");
	exit;
}
?>
