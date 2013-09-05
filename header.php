<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: header.php,v 1.3 2008/05/14 01:22:58 tad Exp $
// ------------------------------------------------------------------------- //
include_once "../../mainfile.php";

if($xoopsModuleConfig['use_pda']=='1'){
  if(file_exists(XOOPS_ROOT_PATH."/modules/tadtools/mobile_device_detect.php")){
    include_once XOOPS_ROOT_PATH."/modules/tadtools/mobile_device_detect.php";
    mobile_device_detect(true,false,true,true,true,true,true,'pda.php',false);
  }
}

include_once "function.php";

$interface_menu[_TAD_TO_MOD]="index.php";

//判斷是否對該模組有管理權限
$isAdmin=false;
if ($xoopsUser) {
    $module_id = $xoopsModule->getVar('mid');
    $isAdmin=$xoopsUser->isAdmin($module_id);
}

$upload_powers=chk_cate_power("upload");
//die(var_export($upload_powers));

if(sizeof($upload_powers) > 0 and $xoopsUser){
	$interface_menu[_MD_TADPLAYER_UPLOAD]="uploads.php";
}

if(!empty($_REQUEST['pcsn'])){
  $pcsn=intval($_REQUEST['pcsn']);
  $interface_menu[_MD_TADPLAYER_LIST]="index.php?op=playlist&pcsn={$pcsn}";
  $ptool="?pcsn={$pcsn}";
}

if($isAdmin){
  $ptool="";

  if(!empty($_REQUEST['psn'])){
    $psn=intval($_REQUEST['psn']);
    $interface_menu[_MD_TADPLAYER_MODIFY_MEDIA]="uploads.php?psn={$psn}#fragment-1";
  }
  $interface_menu[_TAD_TO_ADMIN]="admin/index.php{$ptool}";
}

?>