<?php

use XoopsModules\Tad_player\Utility;

function xoops_module_update_tad_player(&$module, $old_version)
{
    global $xoopsDB;

    if (!Utility::chk_chk1()) {
        Utility::go_update1();
    }
    if (!Utility::chk_chk2()) {
        Utility::go_update2();
    }
    if (!Utility::chk_chk3()) {
        Utility::go_update3();
    }
    if (!Utility::chk_chk4()) {
        Utility::go_update4();
    }
    if (Utility::chk_uid()) {
        Utility::go_update_uid();
    }

    Utility::chk_tad_player_block();

    $old_fckeditor = XOOPS_ROOT_PATH . '/modules/tad_player/fckeditor';
    if (is_dir($old_fckeditor)) {
        Utility::delete_directory($old_fckeditor);
    }

    return true;
}
