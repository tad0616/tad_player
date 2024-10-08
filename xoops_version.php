<?php

global $xoopsConfig;

$modversion = [];

//---模組基本資訊---//
$modversion['name'] = _MI_TADPLAYER_NAME;
$modversion['version'] = $_SESSION['xoops_version'] >= 20511 ? '4.0.0-Stable' : '4.0';
// $modversion['version'] = 3.67;
$modversion['description'] = _MI_TADPLAYER_DESC;
$modversion['author'] = _MI_TADPLAYER_AUTHOR;
$modversion['credits'] = _MI_TADPLAYER_CREDITS;
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image'] = 'images/logo_' . $xoopsConfig['language'] . '.png';
$modversion['dirname'] = basename(__DIR__);

//---模組狀態資訊---//
$modversion['release_date'] = '2021-12-13';
$modversion['module_website_url'] = 'https://tad0616.net/';
$modversion['module_website_name'] = _MI_TAD_WEB;
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'https://tad0616.net/';
$modversion['author_website_name'] = _MI_TAD_WEB;
$modversion['min_php'] = 5.4;
$modversion['min_xoops'] = '2.5';

//---paypal資訊---//
$modversion['paypal'] = [];
$modversion['paypal']['business'] = 'tad0616@gmail.com';
$modversion['paypal']['item_name'] = 'Donation : ' . _MI_TAD_WEB;
$modversion['paypal']['amount'] = 0;
$modversion['paypal']['currency_code'] = 'USD';

//---資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][1] = 'tad_player_cate';
$modversion['tables'][2] = 'tad_player';
$modversion['tables'][3] = 'tad_player_rank';

//---管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

//---使用者主選單設定---//
$modversion['hasMain'] = 1;

//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;

//---安裝設定---//
$modversion['onInstall'] = 'include/onInstall.php';
$modversion['onUpdate'] = 'include/onUpdate.php';
$modversion['onUninstall'] = 'include/onUninstall.php';

//---評論設定---//
$modversion['hasComments'] = 0;

//---搜尋設定---//
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.php';
$modversion['search']['func'] = 'tad_player_search';

//---樣板設定---//
$modversion['templates'] = [];
$i = 1;
$modversion['templates'][$i]['file'] = 'tad_player_adm_main.tpl';
$modversion['templates'][$i]['description'] = 'tad_player_adm_main.tpl';

$i++;
$modversion['templates'][$i]['file'] = 'tad_player_index.tpl';
$modversion['templates'][$i]['description'] = 'tad_player_index.tpl';

$i++;
$modversion['templates'][$i]['file'] = 'tad_player_play.tpl';
$modversion['templates'][$i]['description'] = 'tad_player_play.tpl';

$i++;
$modversion['templates'][$i]['file'] = 'tad_player_uploads.tpl';
$modversion['templates'][$i]['description'] = 'tad_player_uploads.tpl';

$i++;
$modversion['templates'][$i]['file'] = 'tad_player_playlist.tpl';
$modversion['templates'][$i]['description'] = 'tad_player_playlist.tpl';

//---區塊設定---//
$modversion['blocks'][] = [
    'file' => 'tad_player.php',
    'name' => _MI_TADPLAYER_BNAME1,
    'description' => _MI_TADPLAYER_BDESC1,
    'show_func' => 'tad_player',
    'template' => 'tad_player.tpl',
    'edit_func' => 'tad_player_edit',
    'options' => '|0|true',
];

$modversion['blocks'][] = [
    'file' => 'tad_new_media.php',
    'name' => _MI_TADPLAYER_BNAME2,
    'description' => _MI_TADPLAYER_BDESC2,
    'show_func' => 'tad_new_media',
    'template' => 'tad_new_media.tpl',
    'edit_func' => 'tad_new_media_edit',
    'options' => '10|1',
];

$modversion['blocks'][] = [
    'file' => 'tad_hot_media.php',
    'name' => _MI_TADPLAYER_BNAME3,
    'description' => _MI_TADPLAYER_BDESC3,
    'show_func' => 'tad_hot_media',
    'template' => 'tad_hot_media.tpl',
    'edit_func' => 'tad_tad_hot_media_edit',
    'options' => '10|1',
];

$modversion['blocks'][] = [
    'file' => 'tad_play_list.php',
    'name' => _MI_TADPLAYER_BNAME4,
    'description' => _MI_TADPLAYER_BDESC4,
    'show_func' => 'tad_player_play_list',
    'template' => 'tad_play_list.tpl',
    'edit_func' => 'tad_player_play_list_edit',
    'options' => '|0|100|true',
];

//---模組設定---//
$i = 1;
$modversion['config'][$i]['name'] = 'index_show_num';
$modversion['config'][$i]['title'] = '_MI_TADPLAYER_SHOW_NUM';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_SHOW_NUM_DESC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '10';

$i++;
$modversion['config'][$i]['name'] = 'use_social_tools';
$modversion['config'][$i]['title'] = '_MI_SOCIALTOOLS_TITLE';
$modversion['config'][$i]['description'] = '_MI_SOCIALTOOLS_TITLE_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';

$i++;
$modversion['config'][$i]['name'] = 'use_star_rating';
$modversion['config'][$i]['title'] = '_MI_STAR_RATING_TITLE';
$modversion['config'][$i]['description'] = '_MI_STAR_RATING_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';

$i++;
$modversion['config'][$i]['name'] = 'font_color';
$modversion['config'][$i]['title'] = '_MI_TADPLAYER_FONT_COLOR';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_FONT_COLOR_DESC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '#FFFFFF';

$i++;
$modversion['config'][$i]['name'] = 'border_color';
$modversion['config'][$i]['title'] = '_MI_TADPLAYER_BORDER_COLOR';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_BORDER_COLOR_DESC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '#000000';
