<?php
/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";
$updateRecordsArray     = $_POST['node-'];

$sort = 1;
foreach ($updateRecordsArray as $recordIDValue) {
  $sql="update ".$xoopsDB->prefix("tad_player_cate")." set `sort`='{$sort}' where `pcsn`='{$recordIDValue}'";
  $xoopsDB->queryF($sql) or die("Save Sort Fail! (".date("Y-m-d H:i:s").")");
  $sort++;
}

echo "Save Sort OK! (".date("Y-m-d H:i:s").")";
?>