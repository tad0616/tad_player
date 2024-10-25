<?php
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;

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

//底下影片數
function count_video_num($pcsn = '0')
{
    global $xoopsDB;
    //其底下所有子目錄的影片數
    $sql = 'SELECT `pcsn` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn` = ?';
    $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $sub_count = 0;
    while (list($sub_pcsn) = $xoopsDB->fetchRow($result)) {
        $sub = count_video_num($sub_pcsn);
        $sub_count += $sub['num'];
    }

    $pic = '';

    //該目錄影片數
    $sql = 'SELECT `psn`, `image`, `location` FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `pcsn` = ? ORDER BY RAND()';
    $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $count = $xoopsDB->getRowsNum($result);
    while (list($psn, $image, $location) = $xoopsDB->fetchRow($result)) {
        if (0 === mb_strpos($image, 'http')) {
            $pic = $image;
            break;
        } elseif (!empty($image) and file_exists(Tools::_TAD_PLAYER_IMG_DIR . "{$psn}.png")) {
            $pic = Tools::_TAD_PLAYER_IMG_URL . "{$psn}.png";
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
    $sql = 'SELECT `psn`, `image` FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `pcsn` = ? AND `image` != "" ORDER BY RAND() LIMIT 0,1';
    $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    list($psn, $image) = $xoopsDB->fetchRow($result);
    if (empty($image)) {
        $sql = 'SELECT `pcsn` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn` =? ORDER BY RAND()';
        $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

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
    global $xoopsDB;

    $sql = 'SELECT a.`psn`, a.`pcsn`, a.`title`, a.`counter`, b.`title` FROM `' . $xoopsDB->prefix('tad_player') . '` AS a LEFT JOIN `' . $xoopsDB->prefix('tad_player_cate') . '` AS b ON a.`pcsn`=b.`pcsn` ORDER BY a.`counter` DESC LIMIT 0,10';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

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
    global $xoopsDB;
    if (empty($_POST['new_pcsn'])) {
        return;
    }

    $sql = 'INSERT INTO `' . $xoopsDB->prefix('tad_player_cate') . '` (`of_csn`, `title`, `enable_group`, `sort`) VALUES (?, ?, ?, ?)';
    Utility::query($sql, 'iisi', [$_POST['pcsn'], $_POST['new_pcsn'], '', 0]) or Utility::web_error($sql, __FILE__, __LINE__);

    //取得最後新增資料的流水編號
    $pcsn = $xoopsDB->getInsertId();

    return $pcsn;
}

//取得所有類別標題
function tad_player_get_all_news_cate($of_csn = 0, $code = 'big5')
{
    global $xoopsDB;
    $sql = 'SELECT `pcsn`, `title`, `enable_group` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn`=? ORDER BY `sort`';
    $result = Utility::query($sql, 'i', [$of_csn]) or Utility::web_error($sql, __FILE__, __LINE__);

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
    $sql = 'SELECT `pcsn` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn`=?';
    $result = Utility::query($sql, 'i', [$pcsn]) or redirect_header($_SERVER['PHP_SELF'], 3, _MD_TADPLAYER_DB_SELECT_ERROR1);

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
    global $xoopsDB, $isUploader;

    if (!$_SESSION['tad_player_adm'] and !$isUploader) {
        redirect_header('index.php', 3, _TAD_PERMISSION_DENIED);
    }

    //刪除檔案
    $file = Tools::get_tad_player($psn);
    $file['location'] = Utility::auto_charset($file['location'], false);
    $file['image'] = Utility::auto_charset($file['location'], image);
    unlink(Tools::_TAD_PLAYER_FLV_DIR . "{$psn}_{$file['location']}");
    unlink(Tools::_TAD_PLAYER_IMG_DIR . "s_{$psn}.png");
    unlink(Tools::_TAD_PLAYER_IMG_DIR . "{$psn}_{$file['image']}");
    Tools::mk_list_json($file['pcsn']);

    $sql = 'DELETE FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `psn`=?';
    Utility::query($sql, 'i', [$psn]) or Utility::web_error($sql, __FILE__, __LINE__);
}

//判斷某人在哪些類別中有觀看或發表(upload)的權利
function chk_cate_power($kind = '')
{
    global $xoopsDB, $xoopsUser, $xoopsModule;
    $ok_cat = [];
    if (!empty($xoopsUser)) {
        $module_id = $xoopsModule->getVar('mid');
        $_SESSION['tad_player_adm'] = $xoopsUser->isAdmin($module_id);
        if ($_SESSION['tad_player_adm']) {
            $ok_cat[] = '0';
        }
        $user_array = $xoopsUser->getGroups();
    } else {
        $user_array = [3];
        $_SESSION['tad_player_adm'] = 0;
    }

    $col = ('upload' === $kind) ? 'enable_upload_group' : 'enable_group';

    $sql = 'SELECT `pcsn`, `' . $col . '` FROM `' . $xoopsDB->prefix('tad_player_cate') . '`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($pcsn, $power) = $xoopsDB->fetchRow($result)) {
        if ($_SESSION['tad_player_adm'] or empty($power)) {
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

    $sql = 'SELECT COUNT(*), `pcsn` FROM `' . $xoopsDB->prefix('tad_player') . '` GROUP BY `pcsn`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$pcsn] = $count;
    }

    $option = ($of_csn) ? '' : "<option value='0'>" . _MD_TADPLAYER_CATE_SELECT . '</option>';

    $sql = 'SELECT `pcsn`, `title` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn`=? ORDER BY `sort`';
    $result = Utility::query($sql, 'i', [$of_csn]) or Utility::web_error($sql, __FILE__, __LINE__);

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
        $counter = (empty($cate_count[$pcsn])) ? 0 : $cate_count[$pcsn];
        $option .= "<option value='{$pcsn}' $selected >{$dot}{$title} ($counter)</option>";
        $option .= get_tad_player_cate_option($pcsn, $level, $v, $show_dot, $optgroup, $kind);

    }

    return $option;
}

//取得tad_player_cate所有資料陣列
function get_tad_player_cate_all()
{
    global $xoopsDB;
    $sql = 'SELECT `pcsn`, `title` FROM `' . $xoopsDB->prefix('tad_player_cate') . '`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

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
    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_player') . '` SET `counter` = `counter` + 1 WHERE `psn` = ?';
    Utility::query($sql, 'i', [$psn]) or Utility::web_error($sql, __FILE__, __LINE__);

}
