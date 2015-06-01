<?php
include_once "../../mainfile.php";

include_once "function.php";
if ($xoopsModuleConfig['use_pda'] == '1' and strpos($_SESSION['theme_kind'], 'bootstrap') === false) {
    if (file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/mobile_device_detect.php")) {
        include_once XOOPS_ROOT_PATH . "/modules/tadtools/mobile_device_detect.php";
        mobile_device_detect(true, false, true, true, true, true, true, 'pda.php', false);
    }
}

$interface_menu[_TAD_TO_MOD] = "index.php";

$isAdmin = false;
if ($xoopsUser) {
    $module_id = $xoopsModule->getVar('mid');
    $isAdmin   = $xoopsUser->isAdmin($module_id);
}

$upload_powers = chk_cate_power("upload");
//die(var_export($upload_powers));

if (sizeof($upload_powers) > 0 and $xoopsUser) {
    $interface_menu[_MD_TADPLAYER_UPLOAD] = "uploads.php";
}

if (!empty($_REQUEST['pcsn'])) {
    $pcsn                               = (int)($_REQUEST['pcsn']);
    $interface_menu[_MD_TADPLAYER_LIST] = "playlist.php?pcsn={$pcsn}";
    $ptool                              = "?pcsn={$pcsn}";
}

if ($isAdmin) {
    $ptool = "";

    if (!empty($_REQUEST['psn'])) {
        $psn                                        = (int)($_REQUEST['psn']);
        $interface_menu[_MD_TADPLAYER_MODIFY_MEDIA] = "uploads.php?psn={$psn}#fragment-1";
    }
    $interface_menu[_TAD_TO_ADMIN] = "admin/index.php{$ptool}";
}
