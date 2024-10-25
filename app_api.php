<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;
require_once __DIR__ . '/header.php';

// 關閉除錯訊息
$xoopsLogger->activated = false;

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$psn = Request::getInt('psn');
$pcsn = Request::getInt('pcsn');
$of_csn = Request::getInt('of_csn');
$cate = Request::getInt('cate');

header("Content-Type: application/json; charset=utf-8");
switch ($op) {

    case 'get_data':
        die(json_encode(get_data($cate), 256));

    case 'get_cates':
        die(json_encode(get_cates($cate), 256));

}

//取得影片及子分類
function get_data($pcsn = '')
{
    $data['video'] = get_videos($pcsn);
    $data['cates'] = get_cates($pcsn);
    return $data;
}

function file_count()
{
    global $xoopsDB;

    $file_count = [];
    $sql = 'SELECT COUNT(*), `pcsn` FROM `' . $xoopsDB->prefix('tad_player') . '` GROUP BY `pcsn`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $file_count[$pcsn] = $count;
    }
    return $file_count;
}

function cate_count()
{
    global $xoopsDB;

    $cate_count = [];
    $sql = 'SELECT COUNT(*), `of_csn` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` GROUP BY `of_csn`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($count, $of_csn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$of_csn] = $count;
    }

    return $cate_count;
}
function get_cates($of_csn = 0)
{
    global $xoopsDB;

    $file_count = file_count();
    $cate_count = cate_count();
    $all_cates = [];

    $sql = 'SELECT `pcsn`,`of_csn`,`title`,`sort` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn`=? AND (`enable_group`=\'\' OR `enable_group` LIKE \'%3%\') ORDER BY `sort`';
    $result = Utility::query($sql, 'i', [$of_csn]) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($pcsn, $of_csn, $title, $sort) = $xoopsDB->fetchRow($result)) {
        $cover = '';
        $cate['pcsn'] = (int) $pcsn;
        $cate['title'] = $title;
        $cate['of_csn'] = (int) $of_csn;
        $cate['sort'] = (int) $sort;
        $cate['cover'] = cate_cover($pcsn);
        $cate['dir_count'] = (int) $cate_count[$pcsn];
        $cate['file_count'] = (int) $file_count[$pcsn];
        $cate['url'] = XOOPS_URL . "/modules/tad_player/index.php?pcsn={$pcsn}";
        $all_cates[] = $cate;
    }
    return $all_cates;
}

//隨機相簿封面
function cate_cover($pcsn = '')
{
    global $xoopsDB;
    if (empty($pcsn)) {
        return;
    }

    //找出分類下所有影片
    $sql = 'SELECT `psn`, `image` FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `pcsn` =? ORDER BY RAND() LIMIT 1';
    $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    list($psn, $image) = $xoopsDB->fetchRow($result);

    if (empty($psn)) {
        $sql = 'SELECT `psn`, `image` FROM `' . $xoopsDB->prefix('tad_player') . '` AS a
        JOIN `' . $xoopsDB->prefix('tad_player_cate') . '` AS b ON a.`pcsn`=b.`pcsn`
        WHERE a.`pcsn`=? OR b.`of_csn`=?
        ORDER BY RAND() LIMIT 1';
        $result = Utility::query($sql, 'ii', [$pcsn, $pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        list($psn, $image) = $xoopsDB->fetchRow($result);
    }

    return video_cover($psn, $image);
}

//封面
function video_cover($psn = '', $image = '')
{
    global $xoopsDB;

    if (empty($psn)) {
        return;
    }
    if (empty($image)) {
        $sql = 'SELECT `image` FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `psn` = ?';
        $result = Utility::query($sql, 'i', [$psn]) or Utility::web_error($sql, __FILE__, __LINE__);

        list($image) = $xoopsDB->fetchRow($result);
    }

    if (substr($image, 0, 4) == 'http') {
        $pic = $image;
    } elseif (file_exists(XOOPS_ROOT_PATH . "/uploads/tad_player/img/s_{$psn}.png")) {
        $pic = Tools::_TAD_PLAYER_IMG_URL . "{$psn}.png";
    } else {
        $ext = mb_substr($location, -3);
        if ('mp3' === $ext) {
            $pic = 'mp3.png';
        } else {
            $pic = 'flv.png';
        }
        $pic = XOOPS_URL . '/modules/tad_player/images/' . $pic;
    }
    return $pic;
}

function get_videos($pcsn)
{
    global $xoopsDB;

    $sql = 'SELECT a.`psn`, a.`pcsn`, a.`location`, a.`youtube`, a.`title`, a.`creator`, a.`content`, a.`image`, a.`post_date`, a.`counter`, a.`enable_group`, b.`title`, b.`of_csn` FROM `' . $xoopsDB->prefix('tad_player') . '` AS a LEFT JOIN `' . $xoopsDB->prefix('tad_player_cate') . '` AS b ON a.`pcsn`=b.`pcsn` WHERE a.`pcsn`=? AND (a.`location`!=\'\' OR a.`youtube`!=\'\') ORDER BY a.`sort`, a.`post_date` DESC';
    $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);
    //檢查權限
    $ok_cat = chk_cate_power();

    //目前使用者所屬群組
    $user_group = [];
    if ($xoopsUser) {
        $user_group = $xoopsUser->getGroups();
    }

    $data = $no_power = [];
    $i = 0;
    while (list($psn, $new_pcsn, $location, $youtube, $title, $creator, $content, $image, $post_date, $counter, $enable_group, $cate_title, $of_csn) = $xoopsDB->fetchRow($result)) {
        if (!empty($new_pcsn) and !in_array($new_pcsn, $ok_cat)) {
            $no_power[] = $psn;
        }

        //查看該分類是否允許目前使用者觀看
        $enable_group_arr = explode(',', $enable_group);
        $same = array_intersect($enable_group_arr, $user_group);
        if (!empty($enable_group) and empty($same)) {
            continue;
        }

        //整理影片圖檔
        $pic = video_cover($psn, $image);

        //無權限者，無連結
        $url = (is_array($no_power) and in_array($psn, $no_power)) ? '' : "play.php?psn={$psn}";

        //無權限者，無標題
        $img_title = (is_array($no_power) and in_array($psn, $no_power)) ? sprintf(_MD_TADPLAYER_NO_POWER, $title) : $title;

        //整理日期
        if (0 === mb_strpos($post_date, '20')) {
            $post_date = strtotime($post_date);
        }

        $post_date = date('Y-m-d H:i:s', xoops_getUserTimestamp($post_date));

        $YTid = '';
        if ($youtube) {
            $YTid = Tools::getYTid($youtube);
        }

        if ($location) {
            $location = XOOPS_URL . "/uploads/tad_player/flv/{$psn}_{$location}";
        }

        $data[$i]['psn'] = $psn;
        $data[$i]['title'] = $title;
        $data[$i]['creator'] = $creator;
        $data[$i]['content'] = $content;
        $data[$i]['cover'] = $pic;
        $data[$i]['location'] = $location;
        $data[$i]['youtube'] = $youtube;
        $data[$i]['YTid'] = $YTid;
        $data[$i]['url'] = XOOPS_URL . '/modules/tad_player/play.php?psn=' . $psn;
        $data[$i]['post_date'] = $post_date;
        $data[$i]['counter'] = $counter;
        $i++;
    }
    return $data;
}
