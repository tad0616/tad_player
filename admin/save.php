<?php
/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";

$sql = "update " . $xoopsDB->prefix("tad_player") . " set `{$_POST['col_name']}`='{$_POST['value']}' where psn='{$_POST['sn']}'";
$xoopsDB->queryF($sql);
echo $_POST['value'];
