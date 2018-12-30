<?php
/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";

$of_csn = (int) (str_replace("node-_", "", $_POST['of_csn']));
$pcsn   = (int) (str_replace("node-_", "", $_POST['pcsn']));

if ($of_csn == $pcsn) {
    die(_MA_TREETABLE_MOVE_ERROR1 . "(" . date("Y-m-d H:i:s") . ")");
} elseif (chk_cate_path($pcsn, $of_csn)) {
    die(_MA_TREETABLE_MOVE_ERROR2 . "(" . date("Y-m-d H:i:s") . ")");
}

$sql = "update " . $xoopsDB->prefix("tad_player_cate") . " set `of_csn`='{$of_csn}' where `pcsn`='{$pcsn}'";
$xoopsDB->queryF($sql) or die("Reset Fail! (" . date("Y-m-d H:i:s") . ")");

echo _MA_TREETABLE_MOVE_OK . " (" . date("Y-m-d H:i:s") . ")";

//檢查目的地編號是否在其子目錄下
function chk_cate_path($pcsn, $to_csn)
{
    global $xoopsDB;
    //抓出子目錄的編號
    $sql    = "select pcsn from " . $xoopsDB->prefix("tad_player_cate") . " where of_csn='{$pcsn}'";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    while (list($sub_csn) = $xoopsDB->fetchRow($result)) {
        if (chk_cate_path($sub_csn, $to_csn)) {
            return true;
        }
        if ($sub_csn == $to_csn) {
            return true;
        }
    }

    return false;
}
