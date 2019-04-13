<?php

function xoops_module_uninstall_tad_player(&$module)
{
    global $xoopsDB;
    $date = date('Ymd');

    rename(XOOPS_ROOT_PATH . '/uploads/tad_player', XOOPS_ROOT_PATH . "/uploads/tad_player_bak_{$date}");

    return true;
}

function tad_player_delete_directory($dirname)
{
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    }
    if (!$dir_handle) {
        return false;
    }
    while ($file = readdir($dir_handle)) {
        if ('.' != $file && '..' != $file) {
            if (!is_dir($dirname . '/' . $file)) {
                unlink($dirname . '/' . $file);
            } else {
                tad_player_delete_directory($dirname . '/' . $file);
            }
        }
    }
    closedir($dir_handle);
    rmdir($dirname);

    return true;
}

//«þ¨©¥Ø¿ý
function tad_player_full_copy($source = '', $target = '')
{
    if (is_dir($source)) {
        if (!mkdir($target) && !is_dir($target)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $target));
        }
        $d = dir($source);
        while (false !== ($entry = $d->read())) {
            if ('.' == $entry || '..' == $entry) {
                continue;
            }

            $Entry = $source . '/' . $entry;
            if (is_dir($Entry)) {
                tad_player_full_copy($Entry, $target . '/' . $entry);
                continue;
            }
            copy($Entry, $target . '/' . $entry);
        }
        $d->close();
    } else {
        copy($source, $target);
    }
}
