<?php
include_once '../../mainfile.php';

include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_POST, 'op', '', 'string');
$psn = system_CleanVars($_POST, 'psn', 0, 'int');
$pcsn = system_CleanVars($_POST, 'pcsn', 0, 'int');

echo get_cate_options($pcsn, $psn);

function get_cate_options($pcsn = '', $def_psn = '')
{
    global $xoopsDB;

    $sql = 'select `psn` , `title` from `' . $xoopsDB->prefix('tad_player') . "`
    where `pcsn` = '$pcsn' order by `sort`";

    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    $total = $xoopsDB->getRowsNum($result);
    $main = (empty($def_psn) and !empty($total)) ? "<option value=''>" . _MD_TADPLAYER_PICK_A_VIDEO . '</option>' : '';
    $sort = 1;
    while (list($psn, $title) = $xoopsDB->fetchRow($result)) {
        $selected = $def_psn == $psn ? 'selected' : '';
        $main .= "<option value='$psn' $selected>{$sort}. {$title}</option>";
        $sort++;
    }

    return $main;
}
