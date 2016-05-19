<?php
$modversion = array();

//---模組基本資訊---//
$modversion['name']        = _MI_TADPLAYER_NAME;
$modversion['version']     = 3.4;
$modversion['description'] = _MI_TADPLAYER_DESC;
$modversion['author']      = _MI_TADPLAYER_AUTHOR;
$modversion['credits']     = _MI_TADPLAYER_CREDITS;
$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image']       = 'images/logo_' . $xoopsConfig['language'] . '.png';
$modversion['dirname']     = basename(__DIR__);

//---模組狀態資訊---//
$modversion['release_date']        = '2016/05/19';
$modversion['module_website_url']  = 'http://tad0616.net/';
$modversion['module_website_name'] = _MI_TAD_WEB;
$modversion['module_status']       = 'release';
$modversion['author_website_url']  = 'http://tad0616.net/';
$modversion['author_website_name'] = _MI_TAD_WEB;
$modversion['min_php']             = 5.3;
$modversion['min_xoops']           = '2.5';

//---paypal資訊---//
$modversion['paypal']                  = array();
$modversion['paypal']['business']      = 'tad0616@gmail.com';
$modversion['paypal']['item_name']     = 'Donation : ' . _MI_TAD_WEB;
$modversion['paypal']['amount']        = 0;
$modversion['paypal']['currency_code'] = 'USD';

//---資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][1]        = 'tad_player_cate';
$modversion['tables'][2]        = 'tad_player';
$modversion['tables'][3]        = 'tad_player_rank';

//---管理介面設定---//
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/menu.php';

//---使用者主選單設定---//
$modversion['hasMain'] = 1;

//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;

//---安裝設定---//
$modversion['onInstall']   = 'include/onInstall.php';
$modversion['onUpdate']    = 'include/onUpdate.php';
$modversion['onUninstall'] = 'include/onUninstall.php';

//---評論設定---//
$modversion['hasComments']          = 1;
$modversion['comments']['pageName'] = 'play.php';
$modversion['comments']['itemName'] = 'psn';

//---搜尋設定---//
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.php';
$modversion['search']['func'] = 'tad_player_search';

//---樣板設定---//
$modversion['templates']                    = array();
$i                                          = 1;
$modversion['templates'][$i]['file']        = 'tad_player_adm_main_b3.html';
$modversion['templates'][$i]['description'] = 'tad_player_adm_main_b3.html';

$i++;
$modversion['templates'][$i]['file']        = 'tad_player_index_b3.html';
$modversion['templates'][$i]['description'] = 'tad_player_index_b3.html';

$i++;
$modversion['templates'][$i]['file']        = 'tad_player_play_b3.html';
$modversion['templates'][$i]['description'] = 'tad_player_play_b3.html';

$i++;
$modversion['templates'][$i]['file']        = 'tad_player_uploads_b3.html';
$modversion['templates'][$i]['description'] = 'tad_player_uploads_b3.html';

$i++;
$modversion['templates'][$i]['file']        = 'tad_player_playlist_b3.html';
$modversion['templates'][$i]['description'] = 'tad_player_playlist_b3.html';

//---區塊設定---//
$modversion['blocks'][1]['file']        = 'tad_player.php';
$modversion['blocks'][1]['name']        = _MI_TADPLAYER_BNAME1;
$modversion['blocks'][1]['description'] = _MI_TADPLAYER_BDESC1;
$modversion['blocks'][1]['show_func']   = 'tad_player';
$modversion['blocks'][1]['template']    = 'tad_player.html';
$modversion['blocks'][1]['edit_func']   = 'tad_player_edit';
$modversion['blocks'][1]['options']     = '|0|true';

$modversion['blocks'][2]['file']        = 'tad_new_media.php';
$modversion['blocks'][2]['name']        = _MI_TADPLAYER_BNAME2;
$modversion['blocks'][2]['description'] = _MI_TADPLAYER_BDESC2;
$modversion['blocks'][2]['show_func']   = 'tad_new_media';
$modversion['blocks'][2]['template']    = 'tad_new_media.html';
$modversion['blocks'][2]['edit_func']   = 'tad_new_media_edit';
$modversion['blocks'][2]['options']     = '10|1';

$modversion['blocks'][3]['file']        = 'tad_hot_media.php';
$modversion['blocks'][3]['name']        = _MI_TADPLAYER_BNAME3;
$modversion['blocks'][3]['description'] = _MI_TADPLAYER_BDESC3;
$modversion['blocks'][3]['show_func']   = 'tad_player_b_show_3';
$modversion['blocks'][3]['template']    = 'tad_hot_media.html';
$modversion['blocks'][3]['edit_func']   = 'tad_tad_hot_media_edit';
$modversion['blocks'][3]['options']     = '10|1';

$modversion['blocks'][4]['file']        = 'tad_play_list.php';
$modversion['blocks'][4]['name']        = _MI_TADPLAYER_BNAME4;
$modversion['blocks'][4]['description'] = _MI_TADPLAYER_BDESC4;
$modversion['blocks'][4]['show_func']   = 'tad_player_play_list';
$modversion['blocks'][4]['template']    = 'tad_play_list.html';
$modversion['blocks'][4]['edit_func']   = 'tad_player_play_list_edit';
$modversion['blocks'][4]['options']     = '|0|100|true';

//---模組設定---//
$i                                       = 1;
$modversion['config'][$i]['name']        = 'index_show_num';
$modversion['config'][$i]['title']       = '_MI_TADPLAYER_SHOW_NUM';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_SHOW_NUM_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '10';

$i++;
$modversion['config'][$i]['name']        = 'display';
$modversion['config'][$i]['title']       = '_MI_TADPLAYER_DISPLAY';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_DISPLAY_DESC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = 'bottom';
$modversion['config'][$i]['options']     = array('_MI_TADPLAYER_DISPLAY_BOTTOM' => 'bottom', '_MI_TADPLAYER_DISPLAY_RIGHT' => 'right');

$i++;
$modversion['config'][$i]['name']        = 'display_max';
$modversion['config'][$i]['title']       = '_MI_TADPLAYER_DISPLAY_MAX';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_DISPLAY_MAX_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 200;

$i++;
$modversion['config'][$i]['name']        = 'facebook_comments_width';
$modversion['config'][$i]['title']       = '_MI_FBCOMMENT_TITLE';
$modversion['config'][$i]['description'] = '_MI_FBCOMMENT_TITLE_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '1';

$i++;
$modversion['config'][$i]['name']        = 'use_social_tools';
$modversion['config'][$i]['title']       = '_MI_SOCIALTOOLS_TITLE';
$modversion['config'][$i]['description'] = '_MI_SOCIALTOOLS_TITLE_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '1';

$i++;
$modversion['config'][$i]['name']        = 'use_pda';
$modversion['config'][$i]['title']       = '_MI_USE_PDA_TITLE';
$modversion['config'][$i]['description'] = '_MI_USE_PDA_TITLE_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '1';

$i++;
$modversion['config'][$i]['name']        = 'use_star_rating';
$modversion['config'][$i]['title']       = '_MI_STAR_RATING_TITLE';
$modversion['config'][$i]['description'] = '_MI_STAR_RATING_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '1';

$i++;
$modversion['config'][$i]['name']        = 'font_color';
$modversion['config'][$i]['title']       = '_MI_TADPLAYER_FONT_COLOR';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_FONT_COLOR_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '#FFFFFF';

$i++;
$modversion['config'][$i]['name']        = 'border_color';
$modversion['config'][$i]['title']       = '_MI_TADPLAYER_BORDER_COLOR';
$modversion['config'][$i]['description'] = '_MI_TADPLAYER_BORDER_COLOR_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '#000000';
