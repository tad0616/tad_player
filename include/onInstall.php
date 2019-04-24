<?php

use XoopsModules\Tadtools\Utility;

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
