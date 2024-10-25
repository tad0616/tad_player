<?php
use XoopsModules\Tadtools\Utility;
require_once dirname(dirname(__DIR__)) . '/mainfile.php';
require_once __DIR__ . '/function.php';

//判斷是否對該模組有管理權限
if (!isset($_SESSION['tad_player_adm'])) {
    $_SESSION['tad_player_adm'] = ($xoopsUser) ? $xoopsUser->isAdmin() : false;
}

$interface_menu[_MD_TADPLAYER_INDEX] = 'index.php';
$interface_icon[_MD_TADPLAYER_INDEX] = "fa-video-camera";

$upload_powers = chk_cate_power('upload');

if (count($upload_powers) > 0 and $xoopsUser) {
    $isUploader = true;
    $interface_menu[_MD_TADPLAYER_UPLOAD] = 'uploads.php';
    $interface_icon[_MD_TADPLAYER_UPLOAD] = 'fa-upload';
}

if (!empty($_REQUEST['pcsn'])) {
    $pcsn = (int) ($_REQUEST['pcsn']);
    $interface_menu[_MD_TADPLAYER_LIST] = "playlist.php?pcsn={$pcsn}";
    $interface_icon[_MD_TADPLAYER_LIST] = 'fa-play-circle';
    $ptool = "?pcsn={$pcsn}";
}

if ($_SESSION['tad_player_adm']) {
    if (!empty($_REQUEST['psn'])) {
        $psn = (int) ($_REQUEST['psn']);
        $interface_menu[_MD_TADPLAYER_MODIFY_MEDIA] = "uploads.php?psn={$psn}#fragment-1";
        $interface_icon[_MD_TADPLAYER_MODIFY_MEDIA] = 'fa-pencil-square-o';
    }
}
