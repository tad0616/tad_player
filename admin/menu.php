<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: menu.php,v 1.2 2008/05/14 01:22:58 tad Exp $
// ------------------------------------------------------------------------- //
$adminmenu = array();
$icon_dir=substr(XOOPS_VERSION,6,3)=='2.6'?"":"images/";

$i = 1;
$adminmenu[$i]['title'] = _MI_TAD_ADMIN_HOME ;
$adminmenu[$i]['link'] = 'admin/index.php' ;
$adminmenu[$i]['desc'] = _MI_TAD_ADMIN_HOME_DESC ;
$adminmenu[$i]['icon'] = 'images/admin/home.png' ;

$i++;
$adminmenu[$i]['title'] = _MI_TADPLAYER_ADMENU1;
$adminmenu[$i]['link'] = "admin/main.php";
$adminmenu[$i]['desc'] = _MI_TADPLAYER_ADMENU1 ;
$adminmenu[$i]['icon'] = "images/admin/movie.png";

$i++;
$adminmenu[$i]['title'] = _MI_TADPLAYER_ADMENU3;
$adminmenu[$i]['link'] = "admin/cate.php";
$adminmenu[$i]['desc'] = _MI_TADPLAYER_ADMENU3 ;
$adminmenu[$i]['icon'] = "images/admin/new_folder.png";

$i++;
$adminmenu[$i]['title'] = _MI_TADPLAYER_ADMENU4;
$adminmenu[$i]['link'] = "admin/index.php?op=mk_all_xml";
$adminmenu[$i]['desc'] = _MI_TADPLAYER_ADMENU4 ;
$adminmenu[$i]['icon'] = "images/admin/downloads_folder.png";

$i++;
$adminmenu[$i]['title'] = _MI_TAD_ADMIN_ABOUT;
$adminmenu[$i]['link'] = 'admin/about.php';
$adminmenu[$i]['desc'] = _MI_TAD_ADMIN_ABOUT_DESC;
$adminmenu[$i]['icon'] = 'images/admin/about.png';
?>