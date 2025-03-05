<?php
use XoopsModules\Tad_player\Tools;
if (!class_exists('XoopsModules\Tad_player\Tools')) {
    require XOOPS_ROOT_PATH . '/modules/tad_player/preloads/autoloader.php';
}

//判斷是否對該模組有管理權限
if (!isset($tad_player_adm)) {
    $tad_player_adm = isset($xoopsUser) && \is_object($xoopsUser) ? $xoopsUser->isAdmin() : false;
}

$interface_menu[_MD_TADPLAYER_INDEX] = 'index.php';
$interface_icon[_MD_TADPLAYER_INDEX] = "fa-video-camera";

$upload_powers = Tools::chk_cate_power('upload');

if (count($upload_powers) > 0 and isset($xoopsUser)) {
    $isUploader                           = true;
    $interface_menu[_MD_TADPLAYER_UPLOAD] = 'uploads.php';
    $interface_icon[_MD_TADPLAYER_UPLOAD] = 'fa-upload';
}

if (!empty($_REQUEST['pcsn'])) {
    $pcsn                               = (int) ($_REQUEST['pcsn']);
    $interface_menu[_MD_TADPLAYER_LIST] = "playlist.php?pcsn={$pcsn}";
    $interface_icon[_MD_TADPLAYER_LIST] = 'fa-play-circle';
    $ptool                              = "?pcsn={$pcsn}";
}

if ($tad_player_adm) {
    if (!empty($_REQUEST['psn'])) {
        $psn                                        = (int) ($_REQUEST['psn']);
        $interface_menu[_MD_TADPLAYER_MODIFY_MEDIA] = "uploads.php?psn={$psn}#fragment-1";
        $interface_icon[_MD_TADPLAYER_MODIFY_MEDIA] = 'fa-pen-to-square';
    }
}
