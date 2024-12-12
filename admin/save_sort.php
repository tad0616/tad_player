<?php
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;
/*-----------引入檔案區--------------*/
require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once dirname(__DIR__) . '/function.php';
// 關閉除錯訊息
header('HTTP/1.1 200 OK');
$xoopsLogger->activated = false;
$sort = 1;
foreach ($_POST['psn'] as $sn) {
    $sn = (int) $sn;
    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_player') . '` SET `sort`=? WHERE `psn`=?';
    Utility::query($sql, 'ii', [$sort, $sn]) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')' . $sql);

    $sort++;
}

Tools::mk_list_json($_GET['pcsn']);

echo _TAD_SORTED . '(' . date('Y-m-d H:i:s') . ')';
