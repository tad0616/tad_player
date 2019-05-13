<?php
use XoopsModules\Tadtools\Utility;

$ok_video_ext = ['flv', 'mp4', 'm4v', 'f4v', 'mov', 'mp3', 'webm', 'ogv', 'ogg', 'swf', '3gp', '3g2', 'aac', 'm4a'];
$ok_image_ext = ['jpg', 'png', 'gif'];

require_once __DIR__ . '/function_player.php';

//路徑導覽
function tad_player_breadcrumb($pcsn = '0', $array = [])
{
    $item = '';
    if (is_array($array)) {
        foreach ($array as $cate) {
            $url = ($pcsn == $cate['pcsn']) ? "<a href='index.php?pcsn={$cate['pcsn']}' style='color: gray;'>{$cate['title']}</a>" : "<a href='index.php?pcsn={$cate['pcsn']}'>{$cate['title']}</a>";
            $active = ($pcsn == $cate['pcsn']) ? " class='active'" : '';

            if (!empty($cate['sub']) and is_array($cate['sub']) and ($pcsn != $cate['pcsn'] or 0 == $pcsn)) {
                $item .= "
        <li class='dropdown'>
          <a class='dropdown-toggle' data-toggle='dropdown' href='index.php?pcsn={$cate['pcsn']}'>
            {$cate['title']} <span class='caret'></span>
          </a>
          <ul class='dropdown-menu' role='menu'>";
                foreach ($cate['sub'] as $sub_pcsn => $sub_title) {
                    $item .= "<li><a href='index.php?pcsn={$sub_pcsn}'>{$sub_title}</a></li>\n";
                }
                $item .= '
          </ul>
        </li>';
            } else {
                $item .= "<li{$active}>{$url} </li>";
            }
        }
    }

    $main = "
      <ul class='breadcrumb'>
        $item
      </ul>
      ";

    return $main;
}

//取得路徑
function get_tad_player_cate_path($the_pcsn = '', $include_self = true)
{
    global $xoopsDB;

    $arr[0]['pcsn'] = '0';
    $arr[0]['title'] = "<i class='fa fa-home'></i>";
    $arr[0]['sub'] = get_tad_player_sub_cate(0);

    if (!empty($the_pcsn)) {
        $tbl = $xoopsDB->prefix('tad_player_cate');
        $sql = "SELECT t1.pcsn AS lev1, t2.pcsn as lev2, t3.pcsn as lev3, t4.pcsn as lev4, t5.pcsn as lev5, t6.pcsn as lev6, t7.pcsn as lev7
            FROM `{$tbl}` t1
            LEFT JOIN `{$tbl}` t2 ON t2.of_csn = t1.pcsn
            LEFT JOIN `{$tbl}` t3 ON t3.of_csn = t2.pcsn
            LEFT JOIN `{$tbl}` t4 ON t4.of_csn = t3.pcsn
            LEFT JOIN `{$tbl}` t5 ON t5.of_csn = t4.pcsn
            LEFT JOIN `{$tbl}` t6 ON t6.of_csn = t5.pcsn
            LEFT JOIN `{$tbl}` t7 ON t7.of_csn = t6.pcsn
            WHERE t1.of_csn = '0'";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        while (false !== ($all = $xoopsDB->fetchArray($result))) {
            if (in_array($the_pcsn, $all)) {
                //$main.="-";
                foreach ($all as $pcsn) {
                    if (!empty($pcsn)) {
                        if (!$include_self and $pcsn == $the_pcsn) {
                            break;
                        }
                        $arr[$pcsn] = get_tad_player_cate($pcsn);
                        $arr[$pcsn]['sub'] = get_tad_player_sub_cate($pcsn);
                        if ($pcsn == $the_pcsn) {
                            break;
                        }
                    }
                }
                //$main.="<br>";
                break;
            }
        }
    }

    return $arr;
}

function get_tad_player_sub_cate($pcsn = '0')
{
    global $xoopsDB;
    $sql = 'select pcsn,title from ' . $xoopsDB->prefix('tad_player_cate') . " where of_csn='{$pcsn}'";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $pcsn_arr = [];
    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        $pcsn_arr[$pcsn] = $title;
    }

    return $pcsn_arr;
}

//底下影片數
function count_video_num($pcsn = '0')
{
    global $xoopsDB, $xoopsModule;
    //其底下所有子目錄的影片數
    $sql = 'select pcsn from ' . $xoopsDB->prefix('tad_player_cate') . " where of_csn='{$pcsn}'";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $sub_count = 0;
    while (list($sub_pcsn) = $xoopsDB->fetchRow($result)) {
        $sub = count_video_num($sub_pcsn);
        $sub_count += $sub['num'];
    }

    $pic = '';

    //該目錄影片數
    $sql = 'select psn,image,location from ' . $xoopsDB->prefix('tad_player') . " where pcsn = '$pcsn' order by rand()";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $count = $xoopsDB->getRowsNum($result);
    while (list($psn, $image, $location) = $xoopsDB->fetchRow($result)) {
        if (0 === mb_strpos($image, 'http')) {
            $pic = $image;
            break;
        } elseif (!empty($image) and file_exists(_TAD_PLAYER_IMG_DIR . "{$psn}.png")) {
            $pic = _TAD_PLAYER_IMG_URL . "{$psn}.png";
            break;
        }
        $ext = mb_substr($location, -3);
        if ('mp3' === $ext) {
            $pic = 'mp3.png';
        } else {
            $pic = 'flv.png';
        }
        $pic = "images/$pic";
        break;
    }
    $counter['num'] = $count + $sub_count;
    $counter['rel_num'] = $count;
    $counter['img'] = empty($pic) ? get_cate_image($pcsn) : $pic;

    return $counter;
}

//隨機取得底下影片的縮圖
function get_cate_image($pcsn = '0')
{
    global $xoopsDB;
    $sql = 'select psn,image from ' . $xoopsDB->prefix('tad_player') . " where pcsn = '$pcsn' and image!='' order by rand() limit 0,1";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    list($psn, $image) = $xoopsDB->fetchRow($result);
    if (empty($image)) {
        $sql = 'select pcsn from ' . $xoopsDB->prefix('tad_player_cate') . " where of_csn = '$pcsn' order by rand()";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        while (list($pcsn) = $xoopsDB->fetchRow($result)) {
            $image = get_cate_image($pcsn);
            if ($image) {
                return $image;
            }
        }
    } else {
        return $image;
    }
}

//熱門影片
function hot_media()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig;

    $sql = 'select a.psn,a.pcsn,a.title,a.counter,b.title from ' . $xoopsDB->prefix('tad_player') . ' as a left join ' . $xoopsDB->prefix('tad_player_cate') . ' as b on a.pcsn=b.pcsn order by a.counter desc limit 0,10';
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $i = 0;
    while (list($psn, $pcsn, $title, $counter, $cate_title) = $xoopsDB->fetchRow($result)) {
        $hot_media[$i]['psn'] = $psn;
        $hot_media[$i]['title'] = $title;
        $hot_media[$i]['counter'] = $counter;
        $i++;
    }

    return $hot_media;
}

//新增資料到tad_player_cate中
function add_tad_player_cate()
{
    global $xoopsDB, $xoopsModuleConfig;
    if (empty($_POST['new_pcsn'])) {
        return;
    }

    $enable_group = implode(',', $_POST['enable_group']);
    $sql = 'insert into ' . $xoopsDB->prefix('tad_player_cate') . " (of_csn,title,enable_group,sort) values('{$_POST['pcsn']}','{$_POST['new_pcsn']}','','0')";
    $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    //取得最後新增資料的流水編號
    $pcsn = $xoopsDB->getInsertId();

    return $pcsn;
}

//取得所有類別標題
function tad_player_get_all_news_cate($of_csn = 0, $code = 'big5')
{
    global $xoopsDB;
    $sql = 'select pcsn,title,enable_group from ' . $xoopsDB->prefix('tad_player_cate') . " where of_csn='{$of_csn}' order by sort";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $option = '';
    while (list($pcsn, $title, $enable_group) = $xoopsDB->fetchRow($result)) {
        $have_sub = tad_player_chk_cate_have_sub($pcsn);
        if ('utf8' === $code) {
            $title = Utility::to_utf8($title);
        }
        $option .= "<li><a href='index.php?pcsn=$pcsn'>$title</a>";
        if ($have_sub) {
            $option .= "\n<ul>\n";
            $option .= "<li parentId='$pcsn'><a href='#'>Loading</a></li>";
            $option .= "\n</ul>\n";
        }
        $option .= '</li>';
    }

    return $option;
}

//檢查有無子選項
function tad_player_chk_cate_have_sub($pcsn = 0)
{
    global $xoopsDB;
    $sql = 'select pcsn from ' . $xoopsDB->prefix('tad_player_cate') . " where of_csn='{$pcsn}'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, _MD_TADPLAYER_DB_SELECT_ERROR1);
    while (list($sub_pcsn) = $xoopsDB->fetchRow($result)) {
        if (!empty($sub_pcsn)) {
            return true;
        }
    }

    return false;
}

//刪除tad_player某筆資料資料
function delete_tad_player($psn = '')
{
    global $xoopsDB, $isAdmin, $isUploader, $xoopsUser, $xoopsModule;

    if (!$isAdmin and !$isUploader) {
        // die("{$isAdmin} - {$isUploader}");
        redirect_header('index.php', 3, _TAD_PERMISSION_DENIED);
    }

    //刪除檔案
    $file = get_tad_player($psn);
    $file['location'] = Utility::auto_charset($file['location'], false);
    $file['image'] = Utility::auto_charset($file['location'], image);
    unlink(_TAD_PLAYER_FLV_DIR . "{$psn}_{$file['location']}");
    unlink(_TAD_PLAYER_IMG_DIR . "s_{$psn}.png");
    unlink(_TAD_PLAYER_IMG_DIR . "{$psn}_{$file['image']}");
    mk_list_json($file['pcsn']);
    $sql = 'delete from ' . $xoopsDB->prefix('tad_player') . " where psn='$psn'";
    // die($sql);
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
}

//做縮圖
function mk_video_thumbnail($filename = '', $thumb_name = '', $type = 'image/jpeg', $width = '100')
{
    ini_set('memory_limit', '50M');
    // Get new sizes
    list($old_width, $old_height) = getimagesize($filename);

    $percent = ($old_width > $old_height) ? round($width / $old_width, 2) : round($width / $old_height, 2);

    $newwidth = ($old_width > $old_height) ? $width : $old_width * $percent;
    $newheight = ($old_width > $old_height) ? $old_height * $percent : $width;

    // Load
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    if ('image/jpeg' === $type or 'image/jpg' === $type or 'image/pjpg' === $type or 'image/pjpeg' === $type) {
        $source = imagecreatefromjpeg($filename);
        $type = 'image/jpeg';
    } elseif ('image/png' === $type) {
        $source = imagecreatefrompng($filename);
        $type = 'image/png';
    } elseif ('image/gif' === $type) {
        $source = imagecreatefromgif($filename);
        $type = 'image/gif';
    }

    // Resize
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $old_width, $old_height);

    header('Content-type: image/png');

    imagepng($thumb, $thumb_name);

    imagedestroy($thumb);
}

//判斷某人在哪些類別中有觀看或發表(upload)的權利
function chk_cate_power($kind = '')
{
    global $xoopsDB, $xoopsUser, $xoopsModule;
    $ok_cat = [];
    if (!empty($xoopsUser)) {
        $module_id = $xoopsModule->getVar('mid');
        $isAdmin = $xoopsUser->isAdmin($module_id);
        if ($isAdmin) {
            $ok_cat[] = '0';
        }
        $user_array = $xoopsUser->getGroups();
    } else {
        $user_array = [3];
        $isAdmin = 0;
    }

    $col = ('upload' === $kind) ? 'enable_upload_group' : 'enable_group';

    $sql = "select pcsn,$col from " . $xoopsDB->prefix('tad_player_cate') . '';
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($pcsn, $power) = $xoopsDB->fetchRow($result)) {
        if ($isAdmin or empty($power)) {
            $ok_cat[] = $pcsn;
        } else {
            $power_array = explode(',', $power);
            foreach ($power_array as $gid) {
                if (in_array($gid, $user_array)) {
                    $ok_cat[] = $pcsn;
                    break;
                }
            }
        }
    }

    return $ok_cat;
}

//取得分類下拉選單
function get_tad_player_cate_option($of_csn = 0, $level = 0, $v = '', $show_dot = '1', $optgroup = true, $kind = 'view')
{
    global $xoopsDB;
    $dot = ('1' == $show_dot) ? str_repeat(_MD_TADPLAYER_BLANK, $level) : '';
    $level += 1;

    $sql = 'select count(*),pcsn from ' . $xoopsDB->prefix('tad_player') . ' group by pcsn';
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$pcsn] = $count;
    }

    $option = ($of_csn) ? '' : "<option value='0'>" . _MD_TADPLAYER_CATE_SELECT . '</option>';
    $sql = 'select pcsn,title from ' . $xoopsDB->prefix('tad_player_cate') . " where of_csn='{$of_csn}' order by sort";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    if ($kind) {
        $ok_cat = chk_cate_power($kind);
    }
    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        if ($kind) {
            if (!in_array($pcsn, $ok_cat)) {
                continue;
            }
        }

        $selected = ($v == $pcsn) ? 'selected' : '';
        if (empty($cate_count[$pcsn]) and $optgroup) {
            $option .= "<optgroup label='{$title}' style='font-style: normal;color:black;'>" . get_tad_player_cate_option($pcsn, $level, $v, '0') . '</optgroup>';
        } else {
            $counter = (empty($cate_count[$pcsn])) ? 0 : $cate_count[$pcsn];
            $option .= "<option value='{$pcsn}' $selected >{$dot}{$title} ($counter)</option>";
            $option .= get_tad_player_cate_option($pcsn, $level, $v, $show_dot, $optgroup, $kind);
        }
    }

    return $option;
}

//取得tad_player_cate所有資料陣列
function get_tad_player_cate_all()
{
    global $xoopsDB;
    $sql = 'select pcsn,title from ' . $xoopsDB->prefix('tad_player_cate');
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $data = [];
    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        $data[$pcsn] = $title;
    }

    return $data;
}

//計數器
function add_counter($psn = '')
{
    global $xoopsDB;
    $sql = 'update ' . $xoopsDB->prefix('tad_player') . " set `counter` = `counter` + 1 where psn='{$psn}'";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
}

//製作播放清單
function mk_list_json($pcsn = '')
{
    global $xoopsDB, $xoopsModule, $upload_dir;

    $cate = get_tad_player_cate($pcsn);

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('tad_player') . " WHERE `pcsn`='{$pcsn}' and `enable_group`='' order by sort";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $i = 0;
    while (false !== ($midia = $xoopsDB->fetchArray($result))) {
        foreach ($midia as $k => $v) {
            $$k = $v;
        }

        $title = htmlspecialchars($title);

        //$location=urlencode($location);
        if (0 === mb_strpos($image, 'http')) {
            $image = $image;
        } else {
            $image = _TAD_PLAYER_IMG_URL . $image;
        }

        if (empty($location) and !empty($youtube)) {
            $YTid = getYTid($youtube);
            $media = "https://youtu.be/{$YTid}";
        } elseif (0 === mb_strpos($location, 'http')) {
            $media = $location;
        } else {
            $media = _TAD_PLAYER_FLV_URL . "{$psn}_{$location}";
        }

        $json[$i]['file'] = $media;
        $json[$i]['image'] = $image;
        $json[$i]['title'] = $title;
        $json[$i]['mediaid'] = $psn;
        $i++;
    }

    // if (PHP_VERSION_ID >= 50400) {
    //     $content = json_encode($json, JSON_UNESCAPED_UNICODE);
    // } else {
    $content = json_encode($json);
    // }
    $main = Utility::to_utf8($content);

    $main = str_replace('\\/', '/', $main);

    // $main = str_replace('"', '\\\\"', $main);

    $filename = _TAD_PLAYER_UPLOAD_DIR . "{$pcsn}_list.json";

    if (!$handle = fopen($filename, 'wb')) {
        redirect_header($_SERVER['PHP_SELF'], 3, sprintf(_MD_TADPLAYER_CANT_OPEN, $filename));
    }

    if (false === fwrite($handle, $main)) {
        redirect_header($_SERVER['PHP_SELF'], 3, sprintf(_MD_TADPLAYER_CANT_WRITE, $filename));
    }
    fclose($handle);
}

//製作播放清單
function mk_list_xml($pcsn = '')
{
    global $xoopsDB, $xoopsModule, $upload_dir;

    $cate = get_tad_player_cate($pcsn);

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('tad_player') . " WHERE `pcsn`='{$pcsn}' and `enable_group`='' order by sort";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $main = "<rss version=\"2.0\" xmlns:app=\"http://www.w3.org/2007/app\">
  <channel>\n";

    while (false !== ($midia = $xoopsDB->fetchArray($result))) {
        foreach ($midia as $k => $v) {
            $$k = $v;
        }

        $title = htmlspecialchars($title);
        $creator = htmlspecialchars($creator);

        //$location=urlencode($location);
        if (0 === mb_strpos($image, 'http')) {
            $image = $image;
        } else {
            $image = _TAD_PLAYER_IMG_URL . $image;
        }

        $image = (empty($image)) ? '' : "<jwplayer:image file=\"{$image}\">";

        if (empty($location) and !empty($youtube)) {
            $YTid = getYTid($youtube);
            $media = "https://youtu.be/{$YTid}";
        } elseif (0 === mb_strpos($location, 'http')) {
            $media = $location;
        } else {
            $media = _TAD_PLAYER_FLV_URL . "{$psn}_{$location}";
        }

        //$media=str_replace("&feature=youtu.be","",$media);
        //$media=str_replace("=","%3D",$media);
        //$media=str_replace("?","%3F",$media);
        //$media=str_replace("&","%26",$media);

        if (0 === mb_strpos($post_date, '20')) {
            $post_date = strtotime($post_date);
        }
        $post_date = date('Y-m-d H:i:s', xoops_getUserTimestamp($post_date));

        if (empty($info)) {
            $info = xoops_substr(strip_tags($description), 0, 100);
        }
        if (empty($info)) {
            $info = $creator . ' ' . $post_date;
        }

        $main .= '
        <item>
            <guid isPermaLink="true">' . XOOPS_URL . "/modules/tad_player/playlist.php?pcsn={$pcsn}</guid>
            <title>{$title}</title>
            <description>{$content}</description>
            <jwplayer:source file=\"{$media}\">
        </item>\n\n";
    }
    $main .= "
  </channel>\n</rss>";

    $main = Utility::to_utf8($main);

    $filename = _TAD_PLAYER_UPLOAD_DIR . "{$pcsn}_list.xml";

    if (!$handle = fopen($filename, 'wb')) {
        redirect_header($_SERVER['PHP_SELF'], 3, sprintf(_MD_TADPLAYER_CANT_OPEN, $filename));
    }

    if (false === fwrite($handle, $main)) {
        redirect_header($_SERVER['PHP_SELF'], 3, sprintf(_MD_TADPLAYER_CANT_WRITE, $filename));
    }
    fclose($handle);
}
