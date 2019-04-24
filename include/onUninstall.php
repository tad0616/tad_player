<?php

function xoops_module_uninstall_tad_player(&$module)
{
    global $xoopsDB;
    $date = date('Ymd');

    rename(XOOPS_ROOT_PATH . '/uploads/tad_player', XOOPS_ROOT_PATH . "/uploads/tad_player_bak_{$date}");

    return true;
}
