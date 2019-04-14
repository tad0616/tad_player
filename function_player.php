<?php
//引入TadTools的函式庫
if (!file_exists(XOOPS_ROOT_PATH . '/modules/tadtools/tad_function.php')) {
    redirect_header('http://campus-xoops.tn.edu.tw/modules/tad_modules/index.php?module_sn=1', 3, _TAD_NEED_TADTOOLS);
}
require_once XOOPS_ROOT_PATH . '/modules/tadtools/tad_function.php';

define('_TAD_PLAYER_UPLOAD_DIR', XOOPS_ROOT_PATH . '/uploads/tad_player/');
define('_TAD_PLAYER_FLV_DIR', XOOPS_ROOT_PATH . '/uploads/tad_player/flv/');
define('_TAD_PLAYER_IMG_DIR', XOOPS_ROOT_PATH . '/uploads/tad_player/img/');

define('_TAD_PLAYER_UPLOAD_URL', XOOPS_URL . '/uploads/tad_player/');
define('_TAD_PLAYER_FLV_URL', XOOPS_URL . '/uploads/tad_player/flv/');
define('_TAD_PLAYER_IMG_URL', XOOPS_URL . '/uploads/tad_player/img/');
$uid_dir = 0;
if ($xoopsUser) {
    $uid_dir = $xoopsUser->getVar('uid');
}
define('_TAD_PLAYER_BATCH_UPLOAD_DIR', XOOPS_ROOT_PATH . "/uploads/tad_player_batch_uploads/user_{$uid_dir}/");
mk_dir(_TAD_PLAYER_BATCH_UPLOAD_DIR);
define('_TAD_PLAYER_BATCH_UPLOAD_URL', XOOPS_URL . "/uploads/tad_player_batch_uploads/user_{$uid_dir}/");

//以流水號取得某筆tad_player資料
function get_tad_player($psn = '')
{
    global $xoopsDB;
    if (empty($psn)) {
        return;
    }
    $sql = 'select * from ' . $xoopsDB->prefix('tad_player') . " where psn='$psn'";

    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//以流水號取得某筆tad_player_cate資料
function get_tad_player_cate($pcsn = '')
{
    global $xoopsDB;
    if (empty($pcsn)) {
        return;
    }
    $sql = 'select * from ' . $xoopsDB->prefix('tad_player_cate') . " where pcsn='$pcsn'";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//列出所有tad_player資料
function list_tad_player_playlist($pcsn = '')
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $xoopsUser;

    //取得所有分類名稱
    $cate = get_tad_player_cate_all();

    //進行排序
    //$order_by_sort=(empty($pcsn))?"":"a.sort ,";
    $order_by_sort = 'a.sort ,';

    $sql = 'select a.psn,a.pcsn,a.location,a.title,a.image,a.info,a.creator,a.post_date,a.counter,a.enable_group,a.youtube,b.title,b.of_csn from ' . $xoopsDB->prefix('tad_player') . ' as a left join ' . $xoopsDB->prefix('tad_player_cate') . " as b on a.pcsn=b.pcsn where a.pcsn='{$pcsn}' order by $order_by_sort a.post_date desc";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);

    //檢查權限
    $ok_cat = chk_cate_power();

    //目前使用者所屬群組
    $user_group = [];
    if ($xoopsUser) {
        $user_group = $xoopsUser->getGroups();
    }

    $media = '';
    $i = 0;
    while (false !== (list($psn, $new_pcsn, $location, $title, $image, $info, $creator, $post_date, $counter, $enable_group, $youtube, $cate_title, $of_csn) = $xoopsDB->fetchRow($result))) {
        if (!empty($new_pcsn) and !in_array($new_pcsn, $ok_cat, true)) {
            $no_power[] = $psn;
            //continue;
        }

        //查看該分類是否允許目前使用者觀看
        $enable_group_arr = explode(',', $enable_group);
        $same = array_intersect($enable_group_arr, $user_group);
        if (!empty($enable_group) and empty($same)) {
            continue;
        }

        if ('http' === mb_substr($image, 0, 4)) {
            $image = basename($image);
        }

        //整理影片圖檔
        if (empty($image) or !file_exists(_TAD_PLAYER_IMG_DIR . "s_{$psn}.png")) {
            $ext = mb_substr($location, -3);
            if ('mp3' === $ext) {
                $pic = 'mp3.png';
            } else {
                $pic = 'flv.png';
            }
            $pic = "images/$pic";
        } else {
            $pic = _TAD_PLAYER_IMG_URL . "s_{$psn}.png";
        }

        if (empty($location) and !empty($youtube)) {
            $media .= "{0:{src:'{$youtube}', type: 'video/youtube'}, config:{title: '$title', poster: '$creator'}},";
        } elseif ('http' === mb_substr($location, 0, 4)) {
            $mime = mime_type($location);
            $media .= "{0:{src:'{$location}', type: '{$mime}'}, config:{title: '$title', poster: '$creator'}},";
        } else {
            $mime = mime_type($location);
            $media .= "{0:{src:'" . _TAD_PLAYER_FLV_URL . "{$psn}_{$location}', type: '{$mime}'}, config:{title: '$title', poster: '$creator'}},";
        }
        $i++;
    }
    $media = mb_substr($media, 0, -1);

    return $media;
}

function mime_type($filename)
{
    $mime_types = [
        // audio/video
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'flv' => 'video/flv',
        'ogg' => 'video/ogg',
        'ogv' => 'video/ogv',
        'webm' => 'video/webm',
    ];

    $ext = mb_strtolower(array_pop(explode('.', $filename)));
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    } elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);

        return $mimetype;
    }

    return 'video/youtube';
}

//播放語法($mode=single or playlist)
function play_code_jwplayer($id = 'tp', $file = '', $sn = '', $mode = '', $autostart = false, $ModuleConfig = [], $skin = '', $list_width = '', $list_where = 'bottom', $repeat = false)
{
    global $xoopsModuleConfig;

    if (empty($xoopsModuleConfig)) {
        $xoopsModuleConfig = $ModuleConfig;
    }
    $display = $other_code = '';
    if ('playlist' === $mode) {
        $other_code = '';
        // $media      = _TAD_PLAYER_UPLOAD_URL . "{$sn}_list.xml";
        $media = _TAD_PLAYER_UPLOAD_URL . "{$sn}_list.json";
        if (!file_exists(_TAD_PLAYER_UPLOAD_DIR . "{$sn}_list.json")) {
            return;
        }
        $content = file_get_contents($media);
        if ('null' === trim($content)) {
            return;
        }
    } else {
        if (empty($file['location']) and !empty($file['youtube'])) {
            $media = $file['youtube'];
            $youtube_id = getYTid($file['youtube']);
            $url = "https://www.youtube.com/oembed?url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D{$youtube_id}&format=json";
            $contents = file_get_contents($url);
            $contents = utf8_encode($contents);
            $results = json_decode($contents, false);
            // die(var_export($results));
            foreach ($results as $k => $v) {
                $$k = htmlspecialchars($v);
            }
        } elseif ('http' === mb_substr($file['location'], 0, 4)) {
            $media = $file['location'];
        } else {
            $media = _TAD_PLAYER_FLV_URL . "{$sn}_{$file['location']}";
        }
        $pic = (empty($file['image'])) ? '' : "image:'" . _TAD_PLAYER_IMG_URL . "{$sn}.png',";
    }

    //$type=strtolower(substr($file['location'],-3));
    //if($type=="mp3" and empty($file['image'])){
    //  $height=20;
    //}

    if (isset($file['image']) and 'http' === mb_substr($file['image'], 0, 4)) {
        $image = $file['image'];
    } else {
        $image = _TAD_PLAYER_IMG_URL . "{$sn}.png";
    }

    $play_list = '';

    if ('playlist' === $mode) {
        $rate = $list_width;
    } else {
        $rate = (!empty($height) and !empty($width)) ? round($height / $width, 2) : 0.6;
    }
    if (!file_exists(XOOPS_ROOT_PATH . '/modules/tadtools/jwplayer_new.php')) {
        redirect_header('index.php', 3, _MD_NEED_TADTOOLS);
    }
    require_once XOOPS_ROOT_PATH . '/modules/tadtools/jwplayer_new.php';

    if ('' == $mode) {
        $mode = null;
    }
    if ('' == $display) {
        $display = null;
    }
    if ('' == $autostart) {
        $autostart = null;
    }
    if ('' == $repeat) {
        $repeat = null;
    }
    if ('' == $other_code) {
        $other_code = null;
    }
    if ('' == $display) {
        $display = null;
    }
    if ('' == $autostart) {
        $autostart = null;
    }
    if ('' == $repeat) {
        $repeat = null;
    }
    if ('' == $other_code) {
        $other_code = null;
    }

    $jw = new JwPlayer($id . $mode . $sn, $media, $image, '100%', $rate, null, $mode, $display, $autostart, $repeat, $other_code);

    $main = $jw->render();

    return $main;
}

//抓取 Youtube ID
function getYTid($ytURL = '')
{
    if ('https://youtu.be/' === mb_substr($ytURL, 0, 17)) {
        return mb_substr($ytURL, 16);
    }
    parse_str(parse_url($ytURL, PHP_URL_QUERY), $params);

    return $params['v'];
}
