<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: modinfo.php,v 1.2 2008/05/14 01:22:58 tad Exp $
// ------------------------------------------------------------------------- //
include_once XOOPS_ROOT_PATH."/modules/tadtools/language/{$xoopsConfig['language']}/modinfo_common.php";

define("_MI_TADPLAYER_NAME","影音播放");
define("_MI_TADPLAYER_AUTHOR","Tad (tad0616@gmail.com)");
define("_MI_TADPLAYER_CREDITS","ck2 (http://ck2tw.net)");
define("_MI_TADPLAYER_DESC","此模組可讓您的網站嵌入Flv影音播放器");
define("_MI_TADPLAYER_ADMENU1", "影片管理介面");
define("_MI_TADPLAYER_ADMENU2", "上傳影片");
define("_MI_TADPLAYER_ADMENU3", "分類管理");
define("_MI_TADPLAYER_ADMENU4", "重新產生播放清單");
define("_MI_TADPLAYER_ADMENU5", "批次上傳");
define("_MI_TADPLAYER_ADMENU6", "模組更新");
define("_MI_TADPLAYER_BNAME1","影音特區");
define("_MI_TADPLAYER_BDESC1","可指定某一影音檔案來播放");
define("_MI_TADPLAYER_BNAME2","新進影音檔");
define("_MI_TADPLAYER_BDESC2","播放新進的影音檔案");
define("_MI_TADPLAYER_BNAME3","熱門影音檔");
define("_MI_TADPLAYER_BDESC3","播放熱門的影音檔案");
define("_MI_TADPLAYER_BNAME4","影音清單播放");
define("_MI_TADPLAYER_BDESC4","播放某個分類的影音檔案");

//define("_MI_TADPLAYER_COL_NUM","<b>首頁影片欄位數</b>");
//define("_MI_TADPLAYER_COL_NUM_DESC","中間版面小的，請選一欄式的，版面夠大者，可選兩欄式的。");
//define("_MI_ONE_COL","單一欄式");
//define("_MI_TWO_COL","左右兩欄式");


define("_MI_TADPLAYER_DISPLAY", "<b>清單方向</b>");
define("_MI_TADPLAYER_DISPLAY_DESC", "播放清單要放在螢幕的哪一邊？（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_DISPLAY_BOTTOM", "下方");
define("_MI_TADPLAYER_DISPLAY_RIGHT", "右方");
define("_MI_TADPLAYER_DISPLAY_MAX", "<b>播放清單的最大高度</b>");
define("_MI_TADPLAYER_DISPLAY_MAX_DESC", "僅對播放清單放在螢幕下方時有效（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_BACKCOLOR", "<b>播放器背景顏色</b>");
define("_MI_TADPLAYER_BACKCOLOR_DESC", "播放器的主要顏色，務必使用「#00FF00」這種網頁顏色表示法。（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_FONTCOLOR", "<b>播放器文字顏色</b>");
define("_MI_TADPLAYER_FONTCOLOR_DESC", "播放器的文字呈現顏色，務必使用「#00FF00」這種網頁顏色表示法。（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_LIGHTCOLOR", "<b>播放器高亮度顏色</b>");
define("_MI_TADPLAYER_LIGHTCOLOR_DESC", "播放器的進度列或滑鼠移到按鈕的高亮度顏色，務必使用「#00FF00」這種網頁顏色表示法。（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_SCREENCOLOR", "<b>播放器螢幕顏色</b>");
define("_MI_TADPLAYER_SCREENCOLOR_DESC", "播放器的螢幕預設顏色，務必使用「#00FF00」這種網頁顏色表示法。（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_BORDER_COLOR", "<b>播放器邊框顏色</b>");
define("_MI_TADPLAYER_BORDER_COLOR_DESC", "播放器圓角外框的主要顏色，可用任何 CSS 允許的顏色表示法。（僅播放器為 JW Player 時有效）");
define("_MI_TADPLAYER_SHOW_NUM", "<b>每頁顯示的影片數</b>");
define("_MI_TADPLAYER_SHOW_NUM_DESC", "在首頁列表每頁要秀出多少影片？");

?>
