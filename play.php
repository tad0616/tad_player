<?php
use Xmf\Request;
use XoopsModules\Tadtools\StarRating;
use XoopsModules\Tadtools\SweetAlert;
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
$col_sn = Request::getInt('col_sn');
$col_name = Request::getString('col_name');
$mod_name = Request::getString('mod_name');
$rank = Request::getString('rank');

switch ($op) {
    case 'delete_tad_player_file':
        delete_tad_player($psn);
        header("location:index.php?pcsn=$pcsn");
        exit;

    case 'save_rating':
        StarRating::save_rating($mod_name, $col_name, $col_sn, $rank);
        break;

    default:
        if (empty($psn)) {
            header('location:index.php');
            exit;
        }
        play($psn);
        $op = 'play';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('now_op', $op);
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('tad_player_adm', $tad_player_adm);
$xoopsTpl->assign('isUploader', $isUploader);
$xoopsTpl->assign('psn', $psn);
$xoopsTpl->assign('select', get_cate_play($psn));
$xoopsTpl->assign('push', Utility::push_url($xoopsModuleConfig['use_social_tools']));

require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------function區--------------*/

//播放
function play($get_psn = '')
{
    global $xoopsModuleConfig, $xoopsUser, $xoopsTpl, $xoTheme;

    $file = Tools::get_tad_player($get_psn);
    $ok_cat = Tools::chk_cate_power();

    $user_group = [];
    if ($xoopsUser) {
        $user_group = $xoopsUser->getGroups();
    }

    $enable_group_arr = explode(',', $file['enable_group']);
    $same = array_intersect($enable_group_arr, $user_group);
    if ((!empty($file['pcsn']) and !in_array($file['pcsn'], $ok_cat)) or (!empty($file['enable_group']) and empty($same))) {
        redirect_header('index.php', 3, sprintf(_MD_TADPLAYER_NO_POWER, $file['title']));
    }

    add_counter($get_psn);

    $play_code = Tools::play_code_player("file{$get_psn}", $file, $get_psn, 'single');

    $all['pcsn'] = $file['pcsn'];

    $info = $file['info'];

    if (empty($info)) {
        $info = xoops_substr(strip_tags($file['content']), 0, 100);
    }

    if (empty($info)) {
        $info = $file['creator'] . ' ' . $file['post_date'];
    }

    $jquery_path = Utility::get_jquery(true);

    $xoops_module_header = "
    $jquery_path
    <meta proprery=\"og:title\" content=\"{$file['title']}\">
    <meta proprery=\"og:description\" content=\"{$info}\">
    <meta property=\"og:image\" content=\"" . Tools::_TAD_PLAYER_IMG_URL . "s_{$file['image']}\">
    <meta property=\"og:video\" content=\"" . XOOPS_URL . "/modules/tad_player/play.php?psn=$get_psn\">
    <meta name=\"video_height\" content=\"{$file['width']}\">
    <meta name=\"video_width\" content=\"{$file['height']}\">
    <meta name=\"video_type\" content=\"application/x-shockwave-flash\">
    ";

    if ($xoopsModuleConfig['use_star_rating']) {
        $StarRating = new StarRating('tad_player', '10', '', 'simple');
        $StarRating->add_rating(XOOPS_URL . '/modules/tad_player/play.php', 'psn', $get_psn);
        $star_rating = $StarRating->render();
        $star_rating .= "<div id='rating_psn_{$get_psn}'></div>";
    }

    $xoopsTpl->assign('title', $file['title']);

    $xoopsTpl->assign('media', $play_code);
    $xoopsTpl->assign('content', $file['content']);
    if (is_object($xoTheme)) {
        $xoTheme->addMeta('meta', 'keywords', $file['title']);
        $xoTheme->addMeta('meta', 'description', $info);
    } else {
        $xoopsTpl->assign('xoops_meta_keywords', 'keywords', $file['title']);
        $xoopsTpl->assign('xoops_meta_description', $info);
    }

    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    $xoopsTpl->assign('xoops_pagetitle', $file['title']);
    $xoopsTpl->assign('star_rating', $star_rating);
    $xoopsTpl->assign('pcsn', $file['pcsn']);
    $SweetAlert = new SweetAlert();
    $SweetAlert->render("delete_tad_player_file_func", "play.php?op=delete_tad_player_file&pcsn={$file['pcsn']}&psn=", 'psn');
}

//找出選單
function get_cate_play($get_psn = '')
{
    global $xoopsDB, $xoopsTpl;
    $file = Tools::get_tad_player($get_psn);

    $sql = 'SELECT `a`.`psn`, `a`.`title`, `b`.`title` FROM `' . $xoopsDB->prefix('tad_player') . '` AS `a` LEFT JOIN `' . $xoopsDB->prefix('tad_player_cate') . '` AS `b` ON `a`.`pcsn` = `b`.`pcsn` WHERE `a`.`pcsn` = ? ORDER BY `a`.`sort`, `a`.`post_date` DESC';
    $result = Utility::query($sql, 'i', [$file['pcsn']]) or Utility::web_error($sql, __FILE__, __LINE__);

    $option = '';
    while (list($psn, $title, $cate_title) = $xoopsDB->fetchRow($result)) {
        $selected = ($psn == $get_psn) ? 'selected' : '';
        $option .= "<option value='{$psn}' $selected>$title</option>\n";
    }

    //$cate_arr=get_tad_player_cate_path($file['pcsn']);
    $cate_select = get_tad_player_cate_option(0, 0, $file['pcsn']);
    $xoopsTpl->assign('cate_select', $cate_select);

    $select = "
    <form action='' method='post'>
    <select id='main_opt' name='main_opt' onchange='getList(this)' style='width:150px;' title='select category'>
    $cate_select
    </select>
    <select id='sub_opt' name='sub_opt' size=1 onChange=\"window.location.href='{$_SERVER['PHP_SELF']}?psn=' + this.value\" title='select sub-category'>
    $option
    </select>
    </form>";

    return $select;
}
