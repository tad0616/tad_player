<?php
include_once XOOPS_ROOT_PATH . "/modules/tadtools/language/{$xoopsConfig['language']}/modinfo_common.php";

define('_MI_TADPLAYER_NAME', 'Video player');
define('_MI_TADPLAYER_AUTHOR', 'Tad (tad0616@gmail.com)');
define('_MI_TADPLAYER_CREDITS', 'ck2 (http://ck2tw.net)');
define('_MI_TADPLAYER_DESC', 'This module allows you to embed the Flv video player on your website');
define('_MI_TADPLAYER_ADMENU1', 'Video management interface');
define('_MI_TADPLAYER_ADMENU3', 'Category management');
define('_MI_TADPLAYER_ADMENU4', 'Re-generate playlist');
define('_MI_TADPLAYER_BNAME1', 'Video zone');
define('_MI_TADPLAYER_BDESC1', 'Play by selected video file');
define('_MI_TADPLAYER_BNAME2', 'New video');
define('_MI_TADPLAYER_BDESC2', 'Play new video');
define('_MI_TADPLAYER_BNAME3', 'Hot videos');
define('_MI_TADPLAYER_BDESC3', 'Play hot videos');
define('_MI_TADPLAYER_BNAME4', 'Play by playlist');
define('_MI_TADPLAYER_BDESC4', 'Play by selected category');

define('_MI_TADPLAYER_DISPLAY', '<span style="font-weight: bold;">Playlist location</span>');
define('_MI_TADPLAYER_DISPLAY_DESC', 'Where to locate the playlist? (only for JW Player)');
define('_MI_TADPLAYER_DISPLAY_BOTTOM', 'Bottom');
define('_MI_TADPLAYER_DISPLAY_RIGHT', 'Right');
define('_MI_TADPLAYER_DISPLAY_MAX', '<span style="font-weight: bold;">Max height of the playlist</span>');
define('_MI_TADPLAYER_DISPLAY_MAX_DESC', 'Valid only when playlist is located at screen bottom (only for JW Player)');
define('_MI_TADPLAYER_SHOW_NUM', '<span style="font-weight: bold;">Number of videos to show per page</span>');
define('_MI_TADPLAYER_SHOW_NUM_DESC', 'Number of videos to show in home page video list');

define('_MI_TADPLAYER_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_TADPLAYER_HELP_HEADER', __DIR__.'/help/helpheader.html');
define('_MI_TADPLAYER_BACK_2_ADMIN','Back to Administration of ');

//help
define('_MI_TADPLAYER_HELP_OVERVIEW','Overview');
