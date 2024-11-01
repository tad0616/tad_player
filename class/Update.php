<?php

namespace XoopsModules\Tad_player;

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;

/*
Update Class Definition

You may not change or alter any portion of this comment or credits of
supporting developers from this source code or any supporting source code
which is considered copyrighted (c) material of the original comment or credit
authors.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @copyright    https://xoops.org 2001-2017 &copy; XOOPS Project
 * @author       Mamba <mambax7@gmail.com>
 */

/**
 * Class Update
 */
class Update
{

    //重新產生所有的 json
    public static function mk_all_json()
    {
        global $xoopsDB;
        $sql = 'select pcsn from ' . $xoopsDB->prefix('tad_player_cate');
        $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        $log = '';
        while (list($pcsn) = $xoopsDB->fetchRow($result)) {
            Tools::mk_list_json($pcsn);
        }
    }

    public static function chk_chk1()
    {
        if (is_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/img')) {
            return true;
        }

        return false;
    }

    public static function go_update1()
    {
        global $xoopsDB;
        set_time_limit(0);

        Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player');
        Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/img');
        Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/flv');
        Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player_batch_uploads');

        $sql = 'SELECT psn,location,image,post_date FROM ' . $xoopsDB->prefix('tad_player') . ' ORDER BY psn';
        $result = $xoopsDB->query($sql) or die($sql);

        while (list($psn, $location, $image, $post_date) = $xoopsDB->fetchRow($result)) {
            //修正時間格式
            if (0 === mb_strpos($post_date, '20')) {
                //$now=xoops_getUserTimestamp(strtotime($post_date));

                $now = date('Y-m-d H:i:s', xoops_getUserTimestamp(time()));
                $pdate = "`post_date`='{$now}'";
            } else {
                $pdate = '`post_date`=`post_date`';
            }
            //搬移圖檔
            $newimg = '';
            if (!empty($image)) {
                $filename = XOOPS_ROOT_PATH . "/uploads/tad_player/{$psn}_{$image}";
                if (file_exists($filename)) {
                    $thumb_s_name = XOOPS_ROOT_PATH . "/uploads/tad_player/img/s_{$psn}.png";
                    Utility::generateThumbnail($filename, $thumb_s_name, 480);
                    $newimg = ",`image`='{$psn}.png'";
                }
            }

            $sql2 = 'update ' . $xoopsDB->prefix('tad_player') . " set $pdate $newimg where psn='$psn'";
            $xoopsDB->queryF($sql2) or die($sql2);

            //搬移影片檔
            if (!empty($location)) {
                Utility::rename_win(XOOPS_ROOT_PATH . "/uploads/tad_player/{$psn}_{$location}", XOOPS_ROOT_PATH . "/uploads/tad_player/flv/{$psn}_{$location}");
            }
        }

        return true;
    }

    //新增排序欄位
    public static function chk_chk2()
    {
        global $xoopsDB;
        $sql = 'SELECT count(`enable_upload_group`) FROM ' . $xoopsDB->prefix('tad_player_cate');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return false;
        }

        return true;
    }

    public static function go_update2()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tad_player_cate') . " ADD `enable_upload_group` VARCHAR(255) NOT NULL  DEFAULT '' AFTER `enable_group`";
        $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin', 30, $GLOBALS['xoopsDB']->error());
    }

    //新增logo欄位
    public static function chk_chk3()
    {
        global $xoopsDB;
        $sql = 'SELECT count(`logo`) FROM ' . $xoopsDB->prefix('tad_player');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return false;
        }

        return true;
    }

    public static function go_update3()
    {
        global $xoopsDB;
        Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/tad_player/logo');
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tad_player') . " ADD `logo` VARCHAR(255) NOT NULL  DEFAULT ''";
        $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin', 30, $GLOBALS['xoopsDB']->error());
    }

    //新增評分表格
    public static function chk_chk4()
    {
        global $xoopsDB;
        $sql = 'SELECT count(*) FROM ' . $xoopsDB->prefix('tad_player_rank');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return false;
        }

        return true;
    }

    public static function go_update4()
    {
        global $xoopsDB;
        $sql = 'CREATE TABLE ' . $xoopsDB->prefix('tad_player_rank') . ' (
        `col_name` VARCHAR(255) NOT NULL,
        `col_sn` SMALLINT(5) UNSIGNED NOT NULL,
        `rank` TINYINT(3) UNSIGNED NOT NULL,
        `uid` SMALLINT(5) UNSIGNED NOT NULL,
        `rank_date` DATETIME NOT NULL,
        PRIMARY KEY (`col_name`,`col_sn`,`uid`)
        )';
        $xoopsDB->queryF($sql);
    }

    //修正uid欄位
    public static function chk_uid()
    {
        global $xoopsDB;
        $sql = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '" . $xoopsDB->prefix('tad_player') . "' AND COLUMN_NAME = 'uid'";
        $result = $xoopsDB->query($sql);
        list($type) = $xoopsDB->fetchRow($result);
        if ('smallint' === $type) {
            return true;
        }

        return false;
    }

    //執行更新
    public static function go_update_uid()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE `' . $xoopsDB->prefix('tad_player') . '` CHANGE `uid` `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0';
        $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, $GLOBALS['xoopsDB']->error());

        $sql = 'ALTER TABLE `' . $xoopsDB->prefix('tad_player_rank') . '` CHANGE `uid` `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0';
        $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, $GLOBALS['xoopsDB']->error());

        return true;
    }

    //刪除錯誤的重複欄位及樣板檔
    public static function chk_tad_player_block()
    {
        global $xoopsDB;
        //die(var_export($xoopsConfig));
        require XOOPS_ROOT_PATH . '/modules/tad_player/xoops_version.php';

        //先找出該有的區塊以及對應樣板
        foreach ($modversion['blocks'] as $i => $block) {
            $show_func = $block['show_func'];
            $tpl_file_arr[$show_func] = $block['template'];
            $tpl_desc_arr[$show_func] = $block['description'];
        }

        //找出目前所有的樣板檔
        $sql = 'SELECT bid,name,visible,show_func,template FROM `' . $xoopsDB->prefix('newblocks') . "`
        WHERE `dirname` = 'tad_player' ORDER BY `func_num`";
        $result = $xoopsDB->query($sql);
        while (list($bid, $name, $visible, $show_func, $template) = $xoopsDB->fetchRow($result)) {
            //假如現有的區塊和樣板對不上就刪掉
            if ($template != $tpl_file_arr[$show_func]) {
                $sql = 'delete from ' . $xoopsDB->prefix('newblocks') . " where bid='{$bid}'";
                $xoopsDB->queryF($sql);

                //連同樣板以及樣板實體檔案也要刪掉
                $sql = 'delete from ' . $xoopsDB->prefix('tplfile') . ' as a
            left join ' . $xoopsDB->prefix('tplsource') . "  as b on a.tpl_id=b.tpl_id
            where a.tpl_refid='$bid' and a.tpl_module='tad_player' and a.tpl_type='block'";
                $xoopsDB->queryF($sql);
            } else {
                $sql = 'update ' . $xoopsDB->prefix('tplfile') . "
            set tpl_file='{$template}' , tpl_desc='{$tpl_desc_arr[$show_func]}'
            where tpl_refid='{$bid}'";
                $xoopsDB->queryF($sql);
            }
        }
    }

}
