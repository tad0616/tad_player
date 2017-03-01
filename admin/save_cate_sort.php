<?php
/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";

$pcsn = intval($_POST['pcsn']);
$sort = intval($_POST['sort']);
$sql  = "update " . $xoopsDB->prefix("tad_player_cate") . " set `sort`='{$sort}' where pcsn='{$pcsn}'";
$xoopsDB->queryF($sql) or die("Save Sort Fail! (" . date("Y-m-d H:i:s") . ")");

echo "Save Sort OK! (" . date("Y-m-d H:i:s") . ") ";
