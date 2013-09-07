<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: admin.php,v 1.2 2008/05/14 01:22:58 tad Exp $
// ------------------------------------------------------------------------- //
include_once "../../tadtools/language/{$xoopsConfig['language']}/admin_common.php";
define("_TAD_NEED_TADTOOLS","Need modules/tadtools. You can download tadtools from <a href='http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50' target='_blank'>Tad's web</a>.");

define("_MA_INPUT_CATE_FORM","分類設定表單");

define("_MA_TADPLAYER_PSN","媒體編號");
define("_MA_TADPLAYER_PCSN","分類編號");
define("_MA_TADPLAYER_TITLE","標題");
define("_MA_TADPLAYER_CREATOR","作者");
define("_MA_TADPLAYER_LOCATION","影片");
define("_MA_TADPLAYER_YOUTUBE","Youtube影片");
define("_MA_TADPLAYER_IMAGE","縮圖位置");
define("_MA_TADPLAYER_INFO","下載位置");
define("_MA_TADPLAYER_POST_DATE","發佈日期");
define("_MA_TADPLAYER_LSN","流水號");
define("_MA_TADPLAYER_SN","流水號");
define("_MA_TADPLAYER_CSN","所屬類別");
define("_MA_TADPLAYER_OF_LSN","所屬項目");
define("_MA_TADPLAYER_UID","發佈者");
define("_MA_TADPLAYER_COUNTER","人氣");
define("_MA_TADPLAYER_OF_CSN","所屬分類");
define("_MA_TADPLAYER_ENABLE_GROUP","可見群組");
define("_MA_TADPLAYER_ENABLE_UPLOAD_GROUP","可傳群組");
define("_MA_TADPLAYER_SORT","排序");
define("_MA_TADPLAYER_ALL_OK","所有群組");
define("_MA_TADPLAYER_LIST_CATE","分類一覽表");
define("_MA_TADPLAYER_CANT_OPEN","無法建立 %s");
define("_MA_TADPLAYER_CANT_WRITE","無法寫入 %s");
define("_MA_TADPLAYER_SHOW_DATE","（%s 發佈）");
define("_MA_TADPLAYER_CATE_SELECT","不分類");
define("_MA_TADPLAYER_XML_OK","「%s」的播放清單製作完成！");
define("_MA_TADPLAYER_CLICK_EDIT_TITLE","《點選編輯標題》");
define("_MA_TADPLAYER_CLICK_EDIT_DESC","加入說明");
define("_MD_TADPLAYER_BLANK","　");
define("_MA_TADPLAYER_THE_ACT_IS","將勾選的影片：");
define("_MA_TADPLAYER_MOVE_TO","搬移到：");
define("_MA_TADPLAYER_ADD_TITLE","加入標題：");
define("_MA_TADPLAYER_ADD_INFO","加入說明：");
define("_MA_TADPLAYER_SELECT_ALL","全選");
define("_MA_TADPLAYER_LINK_TO_CATE","觀看「%s」分類的影片");
define("_MA_TADPLAYER_VIDEO_AMOUNT","影片數");


//update
define("_MA_TADPLAYER_AUTOUPDATE","模組升級");
define("_MA_TADPLAYER_AUTOUPDATE_VER","版本");
define("_MA_TADPLAYER_AUTOUPDATE_DESC","作用");
define("_MA_TADPLAYER_AUTOUPDATE_STATUS","更新狀況");
define("_MA_TADPLAYER_AUTOUPDATE_GO","立即更新");
define("_MA_TADPLAYER_AUTOUPDATE1","加入排序欄位，以便讓影片也可以排序。");
define("_MA_TADPLAYER_AUTOUPDATE2","加入影片內容欄位，可以讓您撰寫相關描述。");
define("_MA_TADPLAYER_AUTOUPDATE3","加入Youtube欄位");
define("_MA_TADPLAYER_VIEW","檢視");
?>
