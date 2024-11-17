<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_plyer\Tools;

require_once __DIR__ . '/header.php';

// 關閉除錯訊息
$xoopsLogger->activated = false;

$of_csn = Request::getInt('of_csn');
$def_csn = Request::getInt('def_csn');
$chk_view = Request::getInt('chk_view', 1);
$chk_up = Request::getInt('chk_up', 1);

echo get_option($of_csn, $def_csn, $chk_view, $chk_up);

function get_option($of_csn = '', $def_csn = '', $chk_view = 1, $chk_up = 1)
{
    global $xoopsDB;

    $ok_cat = $ok_up_cat = [];

    if ($chk_view) {
        $ok_cat = Tools::chk_cate_power();
    }

    if ($chk_up) {
        $ok_up_cat = Tools::chk_cate_power('upload');
    }
    $option = '';
    $sql = 'SELECT `pcsn`, `title` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn`=? ORDER BY `sort`';
    $result = Utility::query($sql, 'i', [$of_csn]) or Utility::web_error($sql, __FILE__, __LINE__);
    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        if ($chk_view and is_array($ok_cat)) {
            if (!in_array($pcsn, $ok_cat)) {
                continue;
            }
        }

        if ($chk_up and is_array($ok_up_cat)) {
            if (!in_array($pcsn, $ok_up_cat)) {
                continue;
            }
        }
        $selected = $pcsn == $def_csn ? 'selected' : '';
        $option .= "<option value='$pcsn' $selected>$title</option>\n";
    }

    return $option;
}
