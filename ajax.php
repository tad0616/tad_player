<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;

require_once dirname(dirname(__DIR__)) . '/mainfile.php';

// 關閉除錯訊息
header('HTTP/1.1 200 OK');
$xoopsLogger->activated = false;

$op = Request::getString('op');
$psn = Request::getInt('psn');
$pcsn = Request::getInt('pcsn');

echo get_cate_options($pcsn, $psn);

function get_cate_options($pcsn = '', $def_psn = '')
{
    global $xoopsDB;

    $sql = 'SELECT `psn`, `title` FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `pcsn` =? ORDER BY `sort`';
    $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $total = $xoopsDB->getRowsNum($result);

    $main = "<option value=''>" . _MD_TADPLAYER_PICK_A_VIDEO . '</option>';
    $sort = 1;
    while (list($psn, $title) = $xoopsDB->fetchRow($result)) {
        $selected = $def_psn == $psn ? 'selected' : '';
        $main .= "<option value='$psn' $selected>{$sort}. {$title}</option>";
        $sort++;
    }

    return $main;
}
