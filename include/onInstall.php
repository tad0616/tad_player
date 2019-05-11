<?php

use XoopsModules\Tadtools\Utility;

if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}


function xoops_module_install_tad_player(&$module)
{
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/file');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/image');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/image/.thumbs');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/img');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/flv');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player_batch_uploads');

    return true;
}
