<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;
/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tad_player_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$psn = Request::getInt('psn');
$pcsn = Request::getInt('pcsn');

switch ($op) {
    //預設動作
    default:
        playlist($pcsn);
        $op = 'playlist';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('now_op', $op);
$xoopsTpl->assign('push', Utility::push_url($xoopsModuleConfig['use_social_tools']));
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('psn', $psn);
$xoopsTpl->assign('pcsn', $pcsn);

$cate_select = get_tad_player_cate_option(0, 0, $pcsn);
$xoopsTpl->assign('cate_select', $cate_select);

if (isset($title) and !empty($title)) {
    $xoopsTpl->assign('xoops_pagetitle', $title);
    if (is_object($xoTheme)) {
        $xoTheme->addMeta('meta', 'keywords', $title);
        $xoTheme->addMeta('meta', 'description', $info);
    } else {
        $xoopsTpl->assign('xoops_meta_keywords', 'keywords', $title);
        $xoopsTpl->assign('xoops_meta_description', $info);
    }
}

require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------function區--------------*/

//清單播放
function playlist($pcsn = '0')
{
    global $xoopsModuleConfig, $xoopsUser, $xoopsTpl;
    if (empty($pcsn)) {
        $pcsn = 0;
    }
    Tools::mk_list_json($pcsn);
    $cate = Tools::get_tad_player_cate($pcsn);
    $ok_cat = chk_cate_power();

    if (!empty($pcsn) and !in_array($pcsn, $ok_cat)) {
        redirect_header('index.php', 3, sprintf(_MD_TADPLAYER_NO_POWER, $cate['title']));
    }

    $playcode = Tools::play_code_player("tad_player_list{$pcsn}", $cate, $pcsn, 'playlist');

    $title = (empty($cate[$pcsn])) ? '' : $cate[$pcsn];

    $xoopsTpl->assign('mode', 'list');
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('playcode', $playcode);
}
