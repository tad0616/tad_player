<?php
/*-----------引入檔案區--------------*/
include_once "../../../include/cp_header.php";
include_once "../function.php";
$updateRecordsArray = $_POST['recordsArray'];

$sort = 1;
foreach ($updateRecordsArray as $recordIDValue) {
    $sql = "update " . $xoopsDB->prefix("tad_player") . " set `sort`='{$sort}' where psn='{$recordIDValue}'";
    $xoopsDB->queryF($sql) or die("Save Sort Fail! (" . date("Y-m-d H:i:s") . ")");
    $sort++;
}
mk_list_xml($_GET['pcsn']);

echo "Save Sort OK! (" . date("Y-m-d H:i:s") . ")";
