<?php
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\VideoJs;

define('_TAD_PLAYER_UPLOAD_DIR', XOOPS_ROOT_PATH . '/uploads/tad_player/');
define('_TAD_PLAYER_FLV_DIR', XOOPS_ROOT_PATH . '/uploads/tad_player/flv/');
define('_TAD_PLAYER_IMG_DIR', XOOPS_ROOT_PATH . '/uploads/tad_player/img/');

define('_TAD_PLAYER_FLV_URL', XOOPS_URL . '/uploads/tad_player/flv/');
define('_TAD_PLAYER_IMG_URL', XOOPS_URL . '/uploads/tad_player/img/');

//以流水號取得某筆tad_player資料
function get_tad_player($psn = '')
{
    global $xoopsDB;
    if (empty($psn)) {
        return;
    }
    $sql = 'select * from ' . $xoopsDB->prefix('tad_player') . " where psn='$psn'";

    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
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
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//列出所有tad_player資料
function list_tad_player_playlist($pcsn = '')
{
    global $xoopsDB, $xoopsModule, $xoopsUser;

    //取得所有分類名稱
    $cate = get_tad_player_cate_all();

    //進行排序
    //$order_by_sort=(empty($pcsn))?"":"a.sort ,";
    $order_by_sort = 'a.sort ,';

    $sql = 'select a.psn,a.pcsn,a.location,a.title,a.image,a.info,a.creator,a.post_date,a.counter,a.enable_group,a.youtube,b.title,b.of_csn from ' . $xoopsDB->prefix('tad_player') . ' as a left join ' . $xoopsDB->prefix('tad_player_cate') . " as b on a.pcsn=b.pcsn where a.pcsn='{$pcsn}' order by $order_by_sort a.post_date desc";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    //檢查權限
    $ok_cat = chk_cate_power();

    //目前使用者所屬群組
    $user_group = [];
    if ($xoopsUser) {
        $user_group = $xoopsUser->getGroups();
    }

    $media = '';
    $i = 0;
    while (list($psn, $new_pcsn, $location, $title, $image, $info, $creator, $post_date, $counter, $enable_group, $youtube, $cate_title, $of_csn) = $xoopsDB->fetchRow($result)) {
        if (!empty($new_pcsn) and !in_array($new_pcsn, $ok_cat)) {
            $no_power[] = $psn;
            //continue;
        }

        //查看該分類是否允許目前使用者觀看
        $enable_group_arr = explode(',', $enable_group);
        $same = array_intersect($enable_group_arr, $user_group);
        if (!empty($enable_group) and empty($same)) {
            continue;
        }

        if (0 === mb_strpos($image, 'http')) {
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
        } elseif (0 === mb_strpos($location, 'http')) {
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
        'flv' => 'video/x-flv',
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
function play_code_player($id = 'tp', $file = '', $sn = '', $mode = '', $autostart = false, $repeat = false, $position = 'bottom')
{

    if ('playlist' === $mode) {
        $json = XOOPS_URL . "/uploads/tad_player/{$sn}_list.json";
        if (!is_file(_TAD_PLAYER_UPLOAD_DIR . "{$sn}_list.json")) {
            return;
        }
        $media = file_get_contents($json);
        if ('null' === trim($media)) {
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
        } elseif (0 === mb_strpos($file['location'], 'http')) {
            $media = $file['location'];
        } else {
            $media = _TAD_PLAYER_FLV_URL . "{$sn}_{$file['location']}";
        }
        $pic = (empty($file['image'])) ? '' : "image:'" . _TAD_PLAYER_IMG_URL . "{$sn}.png',";
    }

    if (isset($file['image']) and 0 === mb_strpos($file['image'], 'http')) {
        $image = $file['image'];
    } else {
        $image = _TAD_PLAYER_IMG_URL . "{$sn}.png";
    }

    if ('' == $mode) {
        $mode = 'single';
    }

    if ('' == $autostart) {
        $autostart = 'false';
    }
    if ('' == $repeat) {
        $repeat = 'false';
    }
    if ('' == $position) {
        $position = 'bottom';
    }

    $id_name = "{$id}{$mode}{$sn}";

    $VideoJs = new VideoJs($id_name, $media, $image, $mode, $autostart, $repeat, $position);

    $main = $VideoJs->render();

    return $main;
}

//抓取 Youtube ID
function getYTid($ytURL = '')
{
    if (0 === mb_strpos($ytURL, 'https://youtu.be/')) {
        return mb_substr($ytURL, 16);
    }
    if (0 === mb_strpos($ytURL, 'http://youtu.be/')) {
        return mb_substr($ytURL, 15);
    }
    parse_str(parse_url($ytURL, PHP_URL_QUERY), $params);

    return $params['v'];
}
