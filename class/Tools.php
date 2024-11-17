<?php
namespace XoopsModules\Tad_player;

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\VideoJs;

class Tools
{
    const _TAD_PLAYER_UPLOAD_DIR = XOOPS_ROOT_PATH . '/uploads/tad_player/';
    const _TAD_PLAYER_FLV_DIR = XOOPS_ROOT_PATH . '/uploads/tad_player/flv/';
    const _TAD_PLAYER_IMG_DIR = XOOPS_ROOT_PATH . '/uploads/tad_player/img/';
    const _TAD_PLAYER_FLV_URL = XOOPS_URL . '/uploads/tad_player/flv/';
    const _TAD_PLAYER_IMG_URL = XOOPS_URL . '/uploads/tad_player/img/';
    public static $ok_video_ext = ['flv', 'mp4', 'm4v', 'f4v', 'mov', 'mp3', 'webm', 'ogv', 'ogg', 'swf', '3gp', '3g2', 'aac', 'm4a'];
    public static $ok_image_ext = ['jpg', 'png', 'gif'];

    //以流水號取得某筆tad_player資料
    public static function get_tad_player($psn = '')
    {
        global $xoopsDB;
        if (empty($psn)) {
            return;
        }
        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `psn`=' . $psn;
        $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        $data = $xoopsDB->fetchArray($result);

        return $data;
    }

//以流水號取得某筆tad_player_cate資料
    public static function get_tad_player_cate($pcsn = '')
    {
        global $xoopsDB;
        if (empty($pcsn)) {
            return;
        }
        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `pcsn`=?';
        $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);
        $data = $xoopsDB->fetchArray($result);
        return $data;
    }

//播放語法($mode=single or playlist)
    public static function play_code_player($id = 'tp', $file = '', $sn = '', $mode = '', $autostart = false, $repeat = false, $position = 'bottom')
    {

        if ('playlist' === $mode) {
            $json = XOOPS_URL . "/uploads/tad_player/{$sn}_list.json";
            if (!is_file(self::_TAD_PLAYER_UPLOAD_DIR . "{$sn}_list.json")) {
                return;
            }

            $media = Utility::vita_get_url_content($json);
            if ('null' === trim($media)) {
                return;
            }
        } else {
            if (empty($file['location']) and !empty($file['youtube'])) {
                $media = $file['youtube'];
                $youtube_id = self::getYTid($file['youtube']);
                $url = "https://www.youtube.com/oembed?url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D{$youtube_id}&format=json";
                $contents = Utility::vita_get_url_content($url);
                $contents = utf8_encode($contents);
                $results = json_decode($contents, false);
                // die(var_export($results));
                foreach ($results as $k => $v) {
                    $$k = htmlspecialchars($v);
                }
            } elseif (0 === mb_strpos($file['location'], 'http')) {
                $media = $file['location'];
            } else {
                $media = self::_TAD_PLAYER_FLV_URL . "{$sn}_{$file['location']}";
            }
        }

        if (isset($file['image']) and 0 === mb_strpos($file['image'], 'http')) {
            $image = $file['image'];
        } else {
            $image = self::_TAD_PLAYER_IMG_URL . "{$sn}.png";
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
    public static function getYTid($ytURL = '')
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

    //製作播放清單
    public static function mk_list_json($pcsn = '')
    {
        global $xoopsDB;

        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE `pcsn`=? AND `enable_group`=\'\' ORDER BY `sort`';
        $result = Utility::query($sql, 'i', [$pcsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        $i = 0;
        while (false !== ($midia = $xoopsDB->fetchArray($result))) {
            foreach ($midia as $k => $v) {
                $$k = $v;
            }

            $title = htmlspecialchars($title);

            if (0 === mb_strpos($image, 'http')) {
                $image = basename($image);
            }

            //整理影片圖檔
            if (empty($image) or !file_exists(self::_TAD_PLAYER_IMG_DIR . "s_{$psn}.png")) {
                $ext = mb_substr($location, -3);
                if ('mp3' === $ext) {
                    $pic = 'mp3.png';
                } else {
                    $pic = 'flv.png';
                }
                $pic = "images/$pic";
            } else {
                $pic = self::_TAD_PLAYER_IMG_URL . "s_{$psn}.png";
            }

            if (empty($location) and !empty($youtube)) {
                $YTid = self::getYTid($youtube);
                $media = "https://youtu.be/{$YTid}";
                $type = 'video/youtube';
            } elseif (0 === mb_strpos($location, 'http')) {
                $media = $location;
                $ext = substr($media, -3);
                if ('mp4' === $ext) {
                    $type = 'video/mp4';
                } elseif ('ebm' === $ext) {
                    $type = 'video/webm';
                } elseif ('mp3' === $ext) {
                    $type = 'audio/mp3';
                } elseif ('ogg' === $ext) {
                    $type = 'video/ogg';
                } elseif ('flv' === $ext) {
                    $type = 'video/x-flv';
                }
            } else {
                $media = self::_TAD_PLAYER_FLV_URL . "{$psn}_{$location}";
                $ext = substr($media, -3);
                if ('mp4' === $ext) {
                    $type = 'video/mp4';
                } elseif ('ebm' === $ext) {
                    $type = 'video/webm';
                } elseif ('mp3' === $ext) {
                    $type = 'audio/mp3';
                } elseif ('ogg' === $ext) {
                    $type = 'video/ogg';
                } elseif ('flv' === $ext) {
                    $type = 'video/x-flv';
                }
            }

            $json[$i]['name'] = $title;
            $json[$i]['description'] = strip_tags($content);
            $json[$i]['sources'][] = array('src' => $media, 'type' => $type);
            $json[$i]['poster'] = $pic;
            $json[$i]['thumbnail'][] = array('src' => $pic);
            $i++;
        }

        $content = json_encode($json, 256);

        $main = Utility::to_utf8($content);

        $main = str_replace('\\/', '/', $main);

        $filename = self::_TAD_PLAYER_UPLOAD_DIR . "{$pcsn}_list.json";

        if (!$handle = fopen($filename, 'wb')) {
            redirect_header($_SERVER['PHP_SELF'], 3, sprintf(_MD_TADPLAYER_CANT_OPEN, $filename));
        }

        if (false === fwrite($handle, $main)) {
            redirect_header($_SERVER['PHP_SELF'], 3, sprintf(_MD_TADPLAYER_CANT_WRITE, $filename));
        }
        fclose($handle);
    }

    //判斷某人在哪些類別中有觀看或發表(upload)的權利
    public static function chk_cate_power($kind = '')
    {
        global $xoopsDB, $xoopsUser, $xoopsModule, $tad_player_adm;
        $ok_cat = [];
        if (!$xoopsModule) {
            $modhandler = xoops_gethandler('module');
            $xoopsModule = $modhandler->getByDirname('tad_player');
        }
        if (!empty($xoopsUser)) {
            $module_id = $xoopsModule->mid();
            $tad_player_adm = $xoopsUser->isAdmin($module_id);
            if ($tad_player_adm) {
                $ok_cat[] = '0';
            }
            $user_array = $xoopsUser->getGroups();
        } else {
            $user_array = [3];
            $tad_player_adm = 0;
        }

        $col = ('upload' === $kind) ? 'enable_upload_group' : 'enable_group';

        $sql = 'SELECT `pcsn`, `' . $col . '` FROM `' . $xoopsDB->prefix('tad_player_cate') . '`';
        $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        while (list($pcsn, $power) = $xoopsDB->fetchRow($result)) {
            if ($tad_player_adm or empty($power)) {
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
}
