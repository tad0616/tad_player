<?php
use Xmf\Request;
use XoopsModules\Tadtools\CkEditor;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\Wcag;
/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tad_player_uploads.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

if (count($upload_powers) <= 0 or empty($xoopsUser)) {
    redirect_header(XOOPS_URL . '/user.php', 3, _MD_TADPLAYER_NO_UPLOAD_POWER);
}

/*-----------function區--------------*/
function uploads_tabs($psn = '', $pcsn = '')
{
    global $xoopsTpl;

    $op = (!isset($_REQUEST['op'])) ? '' : $_REQUEST['op'];
    if ('to_batch_upload' === $op) {
        $xoopsTpl->assign('show_to_batch_upload', true);
    } else {
        $xoopsTpl->assign('show_to_batch_upload', false);
    }
    tad_player_form($psn, $pcsn);
}

//tad_player編輯表單
function tad_player_form($psn = '', $pcsn = '')
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsTpl;
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    //抓取預設值
    if (!empty($psn)) {
        $DBV = get_tad_player($psn);
    } else {
        $DBV = [];
    }

    //預設值設定

    $pcsn = (!isset($DBV['pcsn'])) ? $pcsn : $DBV['pcsn'];
    $title = (!isset($DBV['title'])) ? '' : $DBV['title'];
    $creator = (!isset($DBV['creator'])) ? '' : $DBV['creator'];
    $location = (!isset($DBV['location'])) ? '' : $DBV['location'];
    $image = (!isset($DBV['image'])) ? '' : $DBV['image'];
    $info = (!isset($DBV['info'])) ? '' : $DBV['info'];
    $uid = (!isset($DBV['uid'])) ? '' : $DBV['uid'];
    $post_date = (!isset($DBV['post_date'])) ? '' : $DBV['post_date'];
    $enable_group = (!isset($DBV['enable_group'])) ? [] : explode(',', $DBV['enable_group']);
    $counter = (!isset($DBV['counter'])) ? '' : $DBV['counter'];
    $content = (!isset($DBV['content'])) ? '' : $DBV['content'];
    $youtube = (!isset($DBV['youtube'])) ? '' : $DBV['youtube'];
    $logo = (!isset($DBV['logo'])) ? '' : $DBV['logo'];

    $CkEditor = new CkEditor('tad_player', 'content', $content);
    $CkEditor->setHeight(200);
    $editor = $CkEditor->render();

    $cate_select = get_tad_player_cate_option(0, 0, $pcsn, 1, false, 'upload');

    $logo_col = false;

    //可見群組
    $memberHandler = xoops_getHandler('member');
    $group_arr = $memberHandler->getGroupList();
    $xoopsTpl->assign('group_arr', $group_arr);
    $xoopsTpl->assign('enable_group', $enable_group);
    //die(var_export($enable_group));

    // $SelectGroup_name = new \XoopsFormSelectGroup("", "enable_group", false,$enable_group, 4, true);
    // $SelectGroup_name->addOption("", _MD_TADPLAYER_ALL_OK, false);
    // $SelectGroup_name->setExtra("class='span12'");
    // $enable_group = $SelectGroup_name->render();

    $op = (empty($psn)) ? 'insert_tad_player' : 'update_tad_player';
    //$op="replace_tad_player";

    $selected_link = $selected_local = $selected_youtube = $selected_img_local = $selected_img_link = '';
    if (0 === mb_strpos($location, 'http')) {
        $hide = "$('#flv_youtube').hide();
        $('#flv_local').hide();";
        $selected_link = 'selected';
    } elseif (!empty($location) and empty($youtube)) {
        $hide = "$('#flv_youtube').hide();
        $('#flv_link').hide();";
        $selected_local = 'selected';
    } else {
        $hide = "$('#flv_local').hide();
        $('#flv_link').hide();
        $('#thumb_config').hide();
        ";
        $selected_youtube = 'selected';
    }

    if (0 === mb_strpos($image, 'http')) {
        $hide_img = "$('#img_local').hide();";
        $selected_img_link = 'selected';
    } else {
        $hide_img = "$('#img_link').hide();";
        $selected_img_local = 'selected';
    }

    $xoopsTpl->assign('cate_select', $cate_select);
    $xoopsTpl->assign('hide', $hide);
    $xoopsTpl->assign('hide_img', $hide_img);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('selected_local', $selected_local);
    $xoopsTpl->assign('selected_link', $selected_link);
    $xoopsTpl->assign('selected_youtube', $selected_youtube);
    $xoopsTpl->assign('location', $location);
    $xoopsTpl->assign('youtube', $youtube);
    $xoopsTpl->assign('selected_img_local', $selected_img_local);
    $xoopsTpl->assign('selected_img_link', $selected_img_link);
    $xoopsTpl->assign('image', $image);
    $xoopsTpl->assign('logo_col', $logo_col);
    $xoopsTpl->assign('creator', $creator);
    $xoopsTpl->assign('enable_group', $enable_group);
    $xoopsTpl->assign('editor', $editor);
    $xoopsTpl->assign('next_op', $op);
    $xoopsTpl->assign('psn', $psn);
}

//新增資料到tad_player中
function insert_tad_player()
{
    global $xoopsDB, $xoopsUser;

    $myts = \MyTextSanitizer::getInstance();

    if (!empty($_POST['new_pcsn']) and _MD_TADPLAYER_NEW_PCSN != $_POST['new_pcsn']) {
        $pcsn = add_tad_player_cate();
    } else {
        $pcsn = (int) $_POST['pcsn'];
    }

    $uid = $xoopsUser->uid();
    $enable_group = $_POST['enable_group'] ? implode(',', $_POST['enable_group']) : '';

    //$now=xoops_getUserTimestamp(time());

    if (empty($_FILES['location']['name']) and !empty($_POST['location'])) {
        $location = $xoopsDB->escape($_POST['location']);
    } else {
        $location = $xoopsDB->escape($_FILES['location']['name']);
    }
    $location = mb_strtolower($location);

    if (!empty($_POST['image'])) {
        $image = $xoopsDB->escape($_POST['image']);
    } elseif (!empty($_POST['youtube'])) {
        $youtube_id = getYTid($_POST['youtube']);

        $url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$youtube_id}&format=json";
        $contents = Utility::vita_get_url_content($url);
        // $contents = utf8_encode($contents);
        //$ytb = json_decode($contents,false);
        $ytb = get_object_vars(json_decode($contents));
        /*
        $thumbnail_width = 480;
        $title = 王心凌 Cyndi Wang 變成陌生人 官方HD MV;
        $type = video;
        $provider_name = YouTube;
        $provider_url = https://www.youtube.com/;
        $thumbnail_height = 360;
        $width = 480;
        $height = 270;
        $html = <iframe width="480" height="270" src="https://www.youtube.com/embed/3B4fyi-xXzo?feature=oembed" frameborder="0" allowfullscreen></iframe>;
        $author_name = universaltwn;
        $version = 1.0;
        $author_url = https://www.youtube.com/user/universaltwn;
        $thumbnail_url = https://i4.ytimg.com/vi/3B4fyi-xXzo/hqdefault.jpg;
         */
        $_POST['height'] = round(($ytb['height'] / $ytb['width']) * $_POST['width']);
        $image = $ytb['thumbnail_url'];
        if (empty($_POST['title'])) {
            $_POST['title'] = $ytb['title'];
        }

        if (empty($_POST['creator'])) {
            $_POST['creator'] = $ytb['author_name'];
        }
    } else {
        $image = $xoopsDB->escape($_FILES['image']['name']);
    }

    if (!empty($_POST['title'])) {
        $title = $xoopsDB->escape($_POST['title']);
    } else {
        $title = $xoopsDB->escape(basename($location));
    }

    $creator = $xoopsDB->escape($_POST['creator']);
    $content = $xoopsDB->escape($_POST['content']);
    $content = removeEmoji($content);
    $content = Wcag::amend($content);
    $youtube = $xoopsDB->escape($_POST['youtube']);
    $logo_name = $xoopsDB->escape($_POST['logo_name']);

    $now = date('Y-m-d H:i:s', xoops_getUserTimestamp(time()));

    $sql = 'insert into ' . $xoopsDB->prefix('tad_player') . " (pcsn,title,creator,location,image,info,uid,post_date,enable_group,counter,content,youtube,logo) values('{$pcsn}','{$title}','{$creator}','{$location}','{$image}','{$location}','{$uid}','{$now}','{$enable_group}','0','{$content}','{$youtube}','{$logo_name}')";

    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    //取得最後新增資料的流水編號
    $psn = $xoopsDB->getInsertId();

    //上傳影片
    if (!empty($_FILES['location']['name'])) {
        upload_flv($psn);
    }

    //上傳圖檔
    if (!empty($_FILES['image']['name'])) {
        upload_pic($psn);
    } elseif (!empty($youtube)) {
        $youtube_id = getYTid($youtube);
        $image = "https://i3.ytimg.com/vi/{$youtube_id}/0.jpg";
        $type = getimagesize($image);
        $pic_s_file = _TAD_PLAYER_IMG_DIR . 's_' . $psn . '.png';
        mk_video_thumbnail($image, $pic_s_file, $type['mime'], '480');
    } elseif (!empty($_POST['image'])) {
        $filename = basename($_POST['image']);
        $type = getimagesize($_POST['image']);
        $pic_s_file = _TAD_PLAYER_IMG_DIR . 's_' . $psn . '.png';
        mk_video_thumbnail($_POST['image'], $pic_s_file, $type['mime'], '480');
    }

    if (!empty($_FILES['logo']['name'])) {
        upload_logo($psn);
    }

    mk_list_json($pcsn);

    return $psn;
}

//更新tad_player某一筆資料
function update_tad_player($psn = '')
{
    global $xoopsDB;

    $myts = \MyTextSanitizer::getInstance();

    if (!empty($_POST['new_pcsn']) and _MD_TADPLAYER_NEW_PCSN != $_POST['new_pcsn']) {
        $pcsn = add_tad_player_cate();
    } else {
        $pcsn = $_POST['pcsn'];
    }

    //上傳影片
    if (!empty($_FILES['location']['name'])) {
        upload_flv($psn, true);
    }

    //上傳圖檔
    if (!empty($_FILES['image']['name'])) {
        upload_pic($psn, true);
        $image_sql = '';
    } elseif (!empty($_POST['youtube'])) {
        $youtube_id = getYTid($_POST['youtube']);
        $image = "https://i3.ytimg.com/vi/{$youtube_id}/0.jpg";
        $type = getimagesize($image);
        $pic_s_file = _TAD_PLAYER_IMG_DIR . 's_' . $psn . '.png';
        mk_video_thumbnail($image, $pic_s_file, $type['mime'], '480');
        $image_sql = ", image = '{$image}'";
    } elseif (!empty($_POST['image'])) {
        $filename = basename($_POST['image']);
        $type = getimagesize($_POST['image']);
        $pic_s_file = _TAD_PLAYER_IMG_DIR . 's_' . $psn . '.png';
        mk_video_thumbnail($_POST['image'], $pic_s_file, $type['mime'], '480');
        $image_sql = ", image = '{$_POST['image']}'";
    }
    if (!empty($_POST['location'])) {
        $location_sql = ", location = '{$_POST['location']}', youtube=''";
    } elseif (!empty($_POST['youtube'])) {
        $location_sql = ", location = '', youtube='{$_POST['youtube']}'";
    } else {
        $location_sql = '';
    }

    if (!empty($_POST['title'])) {
        $title = $xoopsDB->escape($_POST['title']);
    } else {
        $title = $xoopsDB->escape(basename($location));
    }

    $enable_group = implode(',', $_POST['enable_group']);

    $creator = $xoopsDB->escape($_POST['creator']);
    $content = $xoopsDB->escape($_POST['content']);
    $content = removeEmoji($content);
    $content = Wcag::amend($content);
    $youtube = $xoopsDB->escape($_POST['youtube']);
    $logo_name = $xoopsDB->escape($_POST['logo_name']);
    $width = (int) $_POST['width'];
    $height = (int) $_POST['height'];

    //$now=xoops_getUserTimestamp(time());
    $now = date('Y-m-d H:i:s', xoops_getUserTimestamp(time()));
    $sql = 'update ' . $xoopsDB->prefix('tad_player') . " set  pcsn = '{$pcsn}', title = '{$title}', creator = '{$creator}' {$location_sql} {$image_sql}, post_date = '{$now}', enable_group = '{$enable_group}', width = '{$width}', height = '{$height}' , content = '{$content}', logo='{$logo_name}' where psn='$psn'";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    if (!empty($_FILES['logo']['name'])) {
        upload_logo($psn);
    }

    mk_list_json($pcsn);

    return $psn;
}

//上傳影片
function upload_flv($psn = '', $update_sql = false)
{
    global $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/tadtools/upload/class.upload.php';
    set_time_limit(0);
    ini_set('memory_limit', '50M');
    $flv_handle = new \Verot\Upload\Upload($_FILES['location'], 'zh_TW');
    if ($flv_handle->uploaded) {
        $name = mb_substr($_FILES['location']['name'], 0, -4);
        $flv_handle->file_safe_name = false;
        $flv_handle->auto_create_dir = true;
        $flv_handle->file_new_name_body = mb_strtolower("{$psn}_{$name}");
        $flv_handle->process(_TAD_PLAYER_FLV_DIR);
        if ($flv_handle->processed) {
            $flv_handle->clean();
            if ($update_sql) {
                $sql = 'update ' . $xoopsDB->prefix('tad_player') . " set image='{$_FILES['location']['name']}' where psn='$psn'";
                $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
            }

            return true;
        }
        $sql = 'delete from ' . $xoopsDB->prefix('tad_player') . " where psn='{$psn}'";
        $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        redirect_header($_SERVER['PHP_SELF'], 3, 'Error:' . $flv_handle->error);
    }
}

//上傳圖檔
function upload_pic($psn = '', $update_sql = false)
{
    global $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/tadtools/upload/class.upload.php';
    set_time_limit(0);
    ini_set('memory_limit', '50M');

    if (file_exists(_TAD_PLAYER_IMG_DIR . "/{$psn}.png")) {
        unlink(_TAD_PLAYER_IMG_DIR . "/{$psn}.png");
    }

    if (file_exists(_TAD_PLAYER_IMG_DIR . "/s_{$psn}.png")) {
        unlink(_TAD_PLAYER_IMG_DIR . "/s_{$psn}.png");
    }

    $img_handle = new \Verot\Upload\Upload($_FILES['image'], 'zh_TW');
    if ($img_handle->uploaded) {
        //$name=substr($_FILES['image']['name'],0,-4);
        $img_handle->file_safe_name = false;
        $img_handle->file_new_name_body = (string) ($psn);
        $img_handle->image_convert = 'png';
        $img_handle->image_resize = true;
        $img_handle->image_x = 1024;
        $img_handle->image_ratio_y = true;
        $img_handle->process(_TAD_PLAYER_IMG_DIR);

        //製作縮圖
        $img_handle->file_safe_name = false;
        $img_handle->file_new_name_body = "s_{$psn}";
        $img_handle->image_convert = 'png';
        $img_handle->image_resize = true;
        $img_handle->image_x = 480;
        $img_handle->image_ratio_y = true;
        $img_handle->process(_TAD_PLAYER_IMG_DIR);
        $img_handle->auto_create_dir = true;
        if ($img_handle->processed) {
            if ($update_sql) {
                $sql = 'update ' . $xoopsDB->prefix('tad_player') . " set `image`='{$psn}.png' where `psn`='$psn'";
                $result = $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
            }

            $img_handle->clean();

            return true;
        }
        redirect_header($_SERVER['PHP_SELF'], 3, 'Error:' . $img_handle->error);
    }
}

//上傳logo圖檔
function upload_logo($psn = '')
{
    global $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/tadtools/upload/class.upload.php';
    set_time_limit(0);
    ini_set('memory_limit', '50M');

    if (file_exists(_TAD_PLAYER_UPLOAD_DIR . "/logo/{$psn}.png")) {
        unlink(_TAD_PLAYER_UPLOAD_DIR . "/logo/{$psn}.png");
    }

    $img_handle = new \Verot\Upload\Upload($_FILES['logo'], 'zh_TW');
    if ($img_handle->uploaded) {
        //$name=substr($_FILES['image']['name'],0,-4);
        $img_handle->file_safe_name = false;
        $img_handle->file_new_name_body = (string) ($psn);
        $img_handle->image_convert = 'png';
        $img_handle->process(XOOPS_ROOT_PATH . '/uploads/tad_player/logo');
        $img_handle->auto_create_dir = true;
        if ($img_handle->processed) {
            $sql = 'update ' . $xoopsDB->prefix('tad_player') . " set `logo`='{$psn}.png' where `psn`='$psn'";
            $result = $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

            $img_handle->clean();

            return true;
        }
        redirect_header($_SERVER['PHP_SELF'], 3, 'Error:' . $img_handle->error);
    }
}

// 移除表情圖
function removeEmoji($text)
{

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    return $clean_text;
}

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$psn = Request::getInt('psn');
$pcsn = Request::getInt('pcsn');

switch ($op) {
    //新增資料
    case 'insert_tad_player':
        $psn = insert_tad_player();
        header("location: play.php?psn=$psn");
        exit;
    //輸入表格
    case 'tad_player_form':
        $main = tad_player_form($psn, $pcsn);
        break;
    //更新資料
    case 'update_tad_player':
        update_tad_player($psn);
        header("location: play.php?psn=$psn");
        exit;
    default:
        uploads_tabs($psn, $pcsn);
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
require_once XOOPS_ROOT_PATH . '/footer.php';
