<?php
use XoopsModules\Tadtools\StarRating;
use XoopsModules\Tadtools\Utility;
/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tad_player_play.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/*-----------function區--------------*/

//播放
function play($get_psn = '')
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsUser, $xoopsTpl, $xoTheme;

    $file = get_tad_player($get_psn);
    $ok_cat = chk_cate_power();

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

    $play_code = play_code_player("file{$get_psn}", $file, $get_psn, 'single');

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
    <meta property=\"og:image\" content=\"" . _TAD_PLAYER_IMG_URL . "s_{$file['image']}\">
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
}

//找出選單
function get_cate_play($get_psn = '', $size = 1)
{
    global $xoopsDB, $xoopsTpl;
    $file = get_tad_player($get_psn);

    $sql = 'select a.psn,a.title,b.title from ' . $xoopsDB->prefix('tad_player') . ' as a left join ' . $xoopsDB->prefix('tad_player_cate') . " as b on a.pcsn=b.pcsn where a.pcsn='{$file['pcsn']}' order by a.sort, a.post_date desc";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

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
  <select id='main_opt' name='main_opt' onchange='getList(this)' style='width:150px;'>
  $cate_select
  </select>
  <select id='sub_opt' name='sub_opt' size=1 onChange=\"window.location.href='{$_SERVER['PHP_SELF']}?psn=' + this.value\" >
  $option
  </select>
  </form>";

    return $select;
}

/*-----------執行動作判斷區----------*/
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
$psn = system_CleanVars($_REQUEST, 'psn', 0, 'int');
$pcsn = system_CleanVars($_REQUEST, 'pcsn', 0, 'int');
$mod_name = system_CleanVars($_REQUEST, 'mod_name', '', 'string');
$col_name = system_CleanVars($_REQUEST, 'col_name', '', 'string');
$col_sn = system_CleanVars($_REQUEST, 'col_sn', 0, 'int');
$rank = system_CleanVars($_REQUEST, 'rank', '', 'string');

$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('jquery', Utility::get_jquery(true));
$xoopsTpl->assign('isAdmin', $isAdmin);
$xoopsTpl->assign('isUploader', $isUploader);

$xoopsTpl->assign('psn', $psn);

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
        break;
}

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('select', get_cate_play($psn));
$xoopsTpl->assign('push', Utility::push_url($xoopsModuleConfig['use_social_tools']));

$facebook_comments = Utility::facebook_comments($xoopsModuleConfig['facebook_comments_width'], 'tad_player', 'play.php', 'psn', $psn);
$xoopsTpl->assign('facebook_comments', $facebook_comments);

require_once XOOPS_ROOT_PATH . '/include/comment_view.php';
require_once XOOPS_ROOT_PATH . '/footer.php';
