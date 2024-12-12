<?php
$modversion = [];
global $xoopsConfig;

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
$modversion['release_date'] = '2024-12-12';
$modversion['module_website_url'] = 'https://tad0616.net/';
$modversion['module_website_name'] = _MI_TAD_WEB;
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'https://tad0616.net/';
$modversion['author_website_name'] = _MI_TAD_WEB;
$modversion['min_php'] = 5.4;
$modversion['min_xoops'] = '2.5.10';

//---paypal資訊---//
$modversion['paypal'] = [
    'business' => 'tad0616@gmail.com',
    'item_name' => 'Donation : ' . _MI_TAD_WEB,
    'amount' => 0,
    'currency_code' => 'USD',
];

//---資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = ['tad_player_cate', 'tad_player', 'tad_player_rank'];

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
$modversion['templates'] = [
    ['file' => 'tad_player_admin.tpl', 'description' => 'tad_player_admin.tpl'],
    ['file' => 'tad_player_index.tpl', 'description' => 'tad_player_index.tpl'],
];

//---區塊設定---//
$modversion['blocks'] = [
    1 => [
        'file' => 'tad_player.php',
        'name' => _MI_TADPLAYER_BNAME1,
        'description' => _MI_TADPLAYER_BDESC1,
        'show_func' => 'tad_player',
        'template' => 'tad_player.tpl',
        'edit_func' => 'tad_player_edit',
        'options' => '|0|true',
    ],
    [
        'file' => 'tad_new_media.php',
        'name' => _MI_TADPLAYER_BNAME2,
        'description' => _MI_TADPLAYER_BDESC2,
        'show_func' => 'tad_new_media',
        'template' => 'tad_new_media.tpl',
        'edit_func' => 'tad_new_media_edit',
        'options' => '10|1',
    ],
    [
        'file' => 'tad_hot_media.php',
        'name' => _MI_TADPLAYER_BNAME3,
        'description' => _MI_TADPLAYER_BDESC3,
        'show_func' => 'tad_hot_media',
        'template' => 'tad_hot_media.tpl',
        'edit_func' => 'tad_tad_hot_media_edit',
        'options' => '10|1',
    ],
    [
        'file' => 'tad_play_list.php',
        'name' => _MI_TADPLAYER_BNAME4,
        'description' => _MI_TADPLAYER_BDESC4,
        'show_func' => 'tad_player_play_list',
        'template' => 'tad_play_list.tpl',
        'edit_func' => 'tad_player_play_list_edit',
        'options' => '|0|100|true',
    ],
];

//---模組設定---//
$modversion['config'] = [
    [
        'name' => 'index_show_num',
        'title' => '_MI_TADPLAYER_SHOW_NUM',
        'description' => '_MI_TADPLAYER_SHOW_NUM_DESC',
        'formtype' => 'textbox',
        'valuetype' => 'int',
        'default' => '10',
    ],
    [
        'name' => 'use_social_tools',
        'title' => '_MI_SOCIALTOOLS_TITLE',
        'description' => '_MI_SOCIALTOOLS_TITLE_DESC',
        'formtype' => 'yesno',
        'valuetype' => 'int',
        'default' => '1',
    ],
    [
        'name' => 'use_star_rating',
        'title' => '_MI_STAR_RATING_TITLE',
        'description' => '_MI_STAR_RATING_DESC',
        'formtype' => 'yesno',
        'valuetype' => 'int',
        'default' => '1',
    ],
    [
        'name' => 'font_color',
        'title' => '_MI_TADPLAYER_FONT_COLOR',
        'description' => '_MI_TADPLAYER_FONT_COLOR_DESC',
        'formtype' => 'textbox',
        'valuetype' => 'text',
        'default' => '#FFFFFF',
    ],
    [
        'name' => 'border_color',
        'title' => '_MI_TADPLAYER_BORDER_COLOR',
        'description' => '_MI_TADPLAYER_BORDER_COLOR_DESC',
        'formtype' => 'textbox',
        'valuetype' => 'text',
        'default' => '#000000',
    ],
];
