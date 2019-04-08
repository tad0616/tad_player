<?php
/*-----------引入檔案區--------------*/
include_once "../../../include/cp_header.php";
include_once "../function.php";

$sort = 1;
foreach ($_POST['psn'] as $sn) {
    $sn  = (int) $sn;
    $sql = "update " . $xoopsDB->prefix("tad_player") . " set `sort`='{$sort}' where `psn`='{$sn}'";
    $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . " (" . date("Y-m-d H:i:s") . ")" . $sql);
    $sort++;
}

mk_list_json($_GET['pcsn']);

echo _TAD_SORTED . "(" . date("Y-m-d H:i:s") . ")";
