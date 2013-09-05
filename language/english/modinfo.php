<?php
//  ------------------------------------------------------------------------ //
// This module was written by Tad
// Release date: 2008-02-28
// $Id: modinfo.php,v 1.2 2008/05/14 01:22:58 tad Exp $
// ------------------------------------------------------------------------- //
include_once XOOPS_ROOT_PATH."/modules/tadtools/language/{$xoopsConfig['language']}/modinfo_common.php";

define("_MI_TADPLAYER_NAME","Video player");
define("_MI_TADPLAYER_AUTHOR","Tad (tad0616@gmail.com)");
define("_MI_TADPLAYER_CREDITS","ck2 (http://ck2tw.net)");
define("_MI_TADPLAYER_DESC","This module allows you to embed the Flv video player on your website");
define("_MI_TADPLAYER_ADMENU1", "Video management interface");
define("_MI_TADPLAYER_ADMENU2", "Upload video");
define("_MI_TADPLAYER_ADMENU3", "Category management");
define("_MI_TADPLAYER_ADMENU4", "Re-generate playlist");
define("_MI_TADPLAYER_ADMENU5", "Batch upload");
define("_MI_TADPLAYER_ADMENU6", "Module update");
define("_MI_TADPLAYER_BNAME1","Video zone");
define("_MI_TADPLAYER_BDESC1","Play by selected video file");
define("_MI_TADPLAYER_BNAME2","New video");
define("_MI_TADPLAYER_BDESC2","Play new video");
define("_MI_TADPLAYER_BNAME3","Hot videos");
define("_MI_TADPLAYER_BDESC3","Play hot videos");
define("_MI_TADPLAYER_BNAME4","Play by playlist");
define("_MI_TADPLAYER_BDESC4","Play by selected category");

//define("_MI_TADPLAYER_COL_NUM","<b>Number of video columns to show at home page</b>");
//define("_MI_TADPLAYER_COL_NUM_DESC","For small center block layout, please select single column. Otherwise, you can select two-column");
//define("_MI_ONE_COL","single column");
//define("_MI_TWO_COL","two-column");


define("_MI_TADPLAYER_DISPLAY", "<b>Playlist location</b>");
define("_MI_TADPLAYER_DISPLAY_DESC", "Where to locate the playlist? (only for JW Player)");
define("_MI_TADPLAYER_DISPLAY_BOTTOM", "Bottom");
define("_MI_TADPLAYER_DISPLAY_RIGHT", "Right");
define("_MI_TADPLAYER_DISPLAY_MAX", "<b>Max height of the playlist</b>");
define("_MI_TADPLAYER_DISPLAY_MAX_DESC", "Valid only when playlist is located at screen bottom (only for JW Player)");
define("_MI_TADPLAYER_BACKCOLOR", "<b>Player background color</b>");
define("_MI_TADPLAYER_BACKCOLOR_DESC", "Player main color. Please use hex code to assign the color. Example:#00FF00 (only for JW Player)");
define("_MI_TADPLAYER_FONTCOLOR", "<b>Player Text Color</b>");
define("_MI_TADPLAYER_FONTCOLOR_DESC", "Player text color. Please use hex code to assign the color. (only for JW Player)");
define("_MI_TADPLAYER_LIGHTCOLOR", "<b>Player High brighness color</b>");
define("_MI_TADPLAYER_LIGHTCOLOR_DESC", "High brighness color is applied on playback progress bar and hover button. Please use hex code to assign the color. (only for JW Player)");
define("_MI_TADPLAYER_SCREENCOLOR", "<b>Player window color</b>");
define("_MI_TADPLAYER_SCREENCOLOR_DESC", "Player window default color. Please use hex code to assign the color. (only for JW Player)");
define("_MI_TADPLAYER_BORDER_COLOR", "<b>Player border color</b>");
define("_MI_TADPLAYER_BORDER_COLOR_DESC", "Player border main color. You can assign the color by any CSS color code. (only for JW Player)");
define("_MI_TADPLAYER_SHOW_NUM", "<b>Number of videos to show per page</b>");
define("_MI_TADPLAYER_SHOW_NUM_DESC", "Number of videos to show in home page video list");

?>
