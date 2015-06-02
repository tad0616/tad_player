<?php

function xoops_module_update_tad_player(&$module, $old_version)
{
    global $xoopsDB;

    if (!chk_chk1()) {
        go_update1();
    }
    if (!chk_chk2()) {
        go_update2();
    }
    if (!chk_chk3()) {
        go_update3();
    }
    if (!chk_chk4()) {
        go_update4();
    }
    if (chk_uid()) {
        go_update_uid();
    }

    chk_tad_player_block();

    $old_fckeditor = XOOPS_ROOT_PATH . "/modules/tad_player/fckeditor";
    if (is_dir($old_fckeditor)) {
        delete_directory($old_fckeditor);
    }

    return true;
}

function chk_chk1()
{
    if (is_dir(XOOPS_ROOT_PATH . "/uploads/tad_player/img")) {
        return true;
    }

    return false;
}

function go_update1()
{
    global $xoopsDB;
    set_time_limit(0);

    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_player");
    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_player/img");
    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_player/flv");
    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_player_batch_uploads");

    $sql = "select psn,location,image,post_date from " . $xoopsDB->prefix("tad_player") . " order by psn";
    $result = $xoopsDB->query($sql) or die($sql);

    while (list($psn, $location, $image, $post_date) = $xoopsDB->fetchRow($result)) {
        //修正時間格式
        if (substr($post_date, 0, 2) == '20') {
            //$now=xoops_getUserTimestamp(strtotime($post_date));

            $now = date("Y-m-d H:i:s", xoops_getUserTimestamp(time()));
            $pdate = "`post_date`='{$now}'";
        } else {
            $pdate = "`post_date`=`post_date`";
        }
        //搬移圖檔
        $newimg = "";
        if (!empty($image)) {
            $filename = XOOPS_ROOT_PATH . "/uploads/tad_player/{$psn}_{$image}";
            if (file_exists($filename)) {
                $type = getimagesize($filename);
                $thumb_b_name = XOOPS_ROOT_PATH . "/uploads/tad_player/img/{$psn}.png";
                $thumb_s_name = XOOPS_ROOT_PATH . "/uploads/tad_player/img/s_{$psn}.png";
                mk_video_thumbnail($filename, $thumb_b_name, $type['mime'], "480");
                mk_video_thumbnail($filename, $thumb_s_name, $type['mime'], "120");
                //unlink($filename);
                $newimg = ",`image`='{$psn}.png'";
            }
        }

        $sql2 = "update " . $xoopsDB->prefix("tad_player") . " set $pdate $newimg where psn='$psn'";
        $xoopsDB->queryF($sql2) or die($sql2);

        //搬移影片檔
        if (!empty($location)) {
            rename_win(XOOPS_ROOT_PATH . "/uploads/tad_player/{$psn}_{$location}", XOOPS_ROOT_PATH . "/uploads/tad_player/flv/{$psn}_{$location}");
        }
    }

    return true;
}

//新增排序欄位
function chk_chk2()
{
    global $xoopsDB;
    $sql = "select count(`enable_upload_group`) from " . $xoopsDB->prefix("tad_player_cate");
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update2()
{
    global $xoopsDB;
    $sql = "ALTER TABLE " . $xoopsDB->prefix("tad_player_cate") . " ADD `enable_upload_group` varchar(255) NOT NULL  default '' after `enable_group`";
    $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . "/modules/system/admin.php?fct=modulesadmin", 30, mysql_error());
}

//新增logo欄位
function chk_chk3()
{
    global $xoopsDB;
    $sql = "select count(`logo`) from " . $xoopsDB->prefix("tad_player");
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update3()
{
    global $xoopsDB;
    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_player/logo");
    $sql = "ALTER TABLE " . $xoopsDB->prefix("tad_player") . " ADD `logo` varchar(255) NOT NULL  default ''";
    $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . "/modules/system/admin.php?fct=modulesadmin", 30, mysql_error());
}

//新增評分表格
function chk_chk4()
{
    global $xoopsDB;
    $sql = "select count(*) from " . $xoopsDB->prefix("tad_player_rank");
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update4()
{
    global $xoopsDB;
    $sql = "CREATE TABLE " . $xoopsDB->prefix("tad_player_rank") . " (
    `col_name` varchar(255) NOT NULL,
    `col_sn` smallint(5) unsigned NOT NULL,
    `rank` tinyint(3) unsigned NOT NULL,
    `uid` smallint(5) unsigned NOT NULL,
    `rank_date` datetime NOT NULL,
    PRIMARY KEY (`col_name`,`col_sn`,`uid`)
    )";
    $xoopsDB->queryF($sql);
}

//修正uid欄位
function chk_uid()
{
    global $xoopsDB;
    $sql = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE table_name = '" . $xoopsDB->prefix("tad_player") . "' AND COLUMN_NAME = 'uid'";
    $result = $xoopsDB->query($sql);
    list($type) = $xoopsDB->fetchRow($result);
    if ($type == 'smallint') {
        return true;
    }

    return false;
}

//執行更新
function go_update_uid()
{
    global $xoopsDB;
    $sql = "ALTER TABLE `" . $xoopsDB->prefix("tad_player") . "` CHANGE `uid` `uid` mediumint(8) unsigned NOT NULL default 0";
    $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, mysql_error());

    $sql = "ALTER TABLE `" . $xoopsDB->prefix("tad_player_rank") . "` CHANGE `uid` `uid` mediumint(8) unsigned NOT NULL default 0";
    $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, mysql_error());

    return true;
}

//刪除錯誤的重複欄位及樣板檔
function chk_tad_player_block()
{
    global $xoopsDB;
    //die(var_export($xoopsConfig));
    include XOOPS_ROOT_PATH . '/modules/tad_player/xoops_version.php';

    //先找出該有的區塊以及對應樣板
    foreach ($modversion['blocks'] as $i => $block) {
        $show_func = $block['show_func'];
        $tpl_file_arr[$show_func] = $block['template'];
        $tpl_desc_arr[$show_func] = $block['description'];
    }

    //找出目前所有的樣板檔
    $sql = "SELECT bid,name,visible,show_func,template FROM `" . $xoopsDB->prefix("newblocks") . "`
    WHERE `dirname` = 'tad_player' ORDER BY `func_num`";
    $result = $xoopsDB->query($sql);
    while (list($bid, $name, $visible, $show_func, $template) = $xoopsDB->fetchRow($result)) {
        //假如現有的區塊和樣板對不上就刪掉
        if ($template != $tpl_file_arr[$show_func]) {
            $sql = "delete from " . $xoopsDB->prefix("newblocks") . " where bid='{$bid}'";
            $xoopsDB->queryF($sql);

            //連同樣板以及樣板實體檔案也要刪掉
            $sql = "delete from " . $xoopsDB->prefix("tplfile") . " as a
            left join " . $xoopsDB->prefix("tplsource") . "  as b on a.tpl_id=b.tpl_id
            where a.tpl_refid='$bid' and a.tpl_module='tad_player' and a.tpl_type='block'";
            $xoopsDB->queryF($sql);
        } else {
            $sql = "update " . $xoopsDB->prefix("tplfile") . "
            set tpl_file='{$template}' , tpl_desc='{$tpl_desc_arr[$show_func]}'
            where tpl_refid='{$bid}'";
            $xoopsDB->queryF($sql);
        }
    }

}

//建立目錄
function mk_dir($dir = "")
{
    //若無目錄名稱秀出警告訊息
    if (empty($dir)) {
        return;
    }
    //若目錄不存在的話建立目錄
    if (!is_dir($dir)) {
        umask(000);
        //若建立失敗秀出警告訊息
        mkdir($dir, 0777);
    }
}

//拷貝目錄
function full_copy($source = "", $target = "")
{
    if (is_dir($source)) {
        @mkdir($target);
        $d = dir($source);
        while (false !== ($entry = $d->read())) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $Entry = $source . '/' . $entry;
            if (is_dir($Entry)) {
                full_copy($Entry, $target . '/' . $entry);
                continue;
            }
            copy($Entry, $target . '/' . $entry);
        }
        $d->close();
    } else {
        copy($source, $target);
    }
}

function rename_win($oldfile, $newfile)
{
    if (!rename($oldfile, $newfile)) {
        if (copy($oldfile, $newfile)) {
            unlink($oldfile);

            return true;
        }

        return false;
    }

    return true;
}

//做縮圖
function mk_video_thumbnail($filename = "", $thumb_name = "", $type = "image/jpeg", $width = "120")
{
    ini_set('memory_limit', '50M');
    // Get new sizes
    list($old_width, $old_height) = getimagesize($filename);

    $percent = ($old_width > $old_height) ? round($width / $old_width, 2) : round($width / $old_height, 2);

    $newwidth = ($old_width > $old_height) ? $width : $old_width * $percent;
    $newheight = ($old_width > $old_height) ? $old_height * $percent : $width;

    // Load
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    if ($type == "image/jpeg" or $type == "image/jpg" or $type == "image/pjpg" or $type == "image/pjpeg") {
        $source = imagecreatefromjpeg($filename);
        $type = "image/jpeg";
    } elseif ($type == "image/png") {
        $source = imagecreatefrompng($filename);
        $type = "image/png";
    } elseif ($type == "image/gif") {
        $source = imagecreatefromgif($filename);
        $type = "image/gif";
    }

    // Resize
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $old_width, $old_height);

    header("Content-type: image/png");
    imagepng($thumb, $thumb_name);

    return;
    exit;
}

function delete_directory($dirname)
{
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    }
    if (!$dir_handle) {
        return false;
    }
    while ($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname . "/" . $file)) {
                unlink($dirname . "/" . $file);
            } else {
                delete_directory($dirname . '/' . $file);
            }
        }
    }
    closedir($dir_handle);
    rmdir($dirname);

    return true;
}
