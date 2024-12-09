<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;

require_once __DIR__ . '/header.php';
xoops_loadLanguage('batch', 'tad_player');

// 關閉除錯訊息
$xoopsLogger->activated = false;

$op = Request::getString('op');
$pcsn = Request::getInt('pcsn');

switch ($op) {
    case 'import':
        $pcsn = tad_player_batch_import();
        Tools::mk_list_json($pcsn);
        header("location:index.php?pcsn=$pcsn");
        break;

    default:
        echo tad_player_batch_upload_form();
        break;
}

//tad_player批次上傳表單
function tad_player_batch_upload_form()
{
    global $xoopsUser;

    $cate_select = get_tad_player_cate_option(0, 0, 0, 1, false);

    $uid_dir = 0;
    if ($xoopsUser) {
        $uid_dir = $xoopsUser->uid();
    }
    define('_TAD_PLAYER_BATCH_UPLOAD_DIR', XOOPS_ROOT_PATH . "/uploads/tad_player_batch_uploads/user_{$uid_dir}/");
    Utility::mk_dir(_TAD_PLAYER_BATCH_UPLOAD_DIR);
    $flv_arr = [];
    if ($dh = opendir(_TAD_PLAYER_BATCH_UPLOAD_DIR)) {
        while (false !== ($file = readdir($dh))) {
            if (0 === mb_strpos($file, '.')) {
                continue;
            }

            $file = Utility::auto_charset($file, true);

            $f = explode('.', $file);
            //$filename=$f[0];
            foreach ($f as $ff) {
                $ext = mb_strtolower($ff);
            }
            $end = (mb_strlen($ext) + 1) * -1;
            $filename = mb_substr($file, 0, $end);

            if (in_array($ext, Tools::$ok_video_ext)) {
                $flv_arr['flv'][$filename] = $file;
            } elseif (in_array($ext, Tools::$ok_image_ext)) {
                $flv_arr['img'][$filename] = $file;
            }
        }
        closedir($dh);
        $tr = '';
        if (isset($flv_arr['flv'])) {
            foreach ($flv_arr['flv'] as $filename => $file) {
                if (!empty($flv_arr['img'][$filename])) {
                    $image = $flv_arr['img'][$filename];
                    $image_form = "<input type='hidden' name='img[$filename]' value='{$image}'>";
                } else {
                    $image = $image_form = '';
                }

                $tr .= "<tr>
                <td class='title'><input type='checkbox' name='flv[$filename]' value='{$file}' checked>{$title}</td>
                <td class='col'>$file</td>
                <td class='col'>{$image}{$image_form}</td>
                </tr>\n";
            }
        }
    }

    if ($tr) {
        $main = "
        <form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
        <input type='hidden' name='op' value='import'>
        <table class='table table-striped table-bordered table-hover'>
        <tr>
            <td class='title' nowrap>" . _MD_TADPLAYER_BATCH_OF_CSN . "</td>
            <td class='col' colspan=2><select name='pcsn' size=1 title='select category'>
            $cate_select
            </select>" . _MD_TADPLAYER_BATCH_NEW_PCSN . "<input type='text' name='new_pcsn' size='10'></td></tr>
            $tr
        <tr><td colspan=3 class='bar'><button type='submit' class='btn btn-primary'>" . _MD_BATCH_SAVE . '</button></td></tr>
        </table>
        </form>';
    } else {
        $main = "
        <div class='alert alert-info'>
        " . _MD_TADPLAYER_BATCH_UPLOAD_TO . "<span style='color:red;'>" . _TAD_PLAYER_BATCH_UPLOAD_DIR . '</span>
        </div>';
    }

    return $main;
}

//批次匯入
function tad_player_batch_import()
{
    global $xoopsDB, $xoopsUser, $xoopsModuleConfig;

    if (!empty($_POST['new_pcsn'])) {
        $pcsn = add_tad_player_cate();
    } else {
        $pcsn = (int) $_POST['pcsn'];
    }

    $uid = $xoopsUser->uid();
    $uid_name = \XoopsUser::getUnameFromId($uid, 1);

    $now = date('Y-m-d H:i:s', xoops_getUserTimestamp(time()));
    foreach ($_POST['flv'] as $filename => $flv) {
        if (empty($flv)) {
            continue;
        }
        $sql = 'INSERT INTO `' . $xoopsDB->prefix('tad_player') . '` (`pcsn`, `title`, `creator`, `location`, `image`, `info`, `uid`, `post_date`, `enable_group`, `counter`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        Utility::query($sql, 'isssssissi', [$pcsn, $flv, $uid_name, $flv, '', $flv, $uid, $now, '', 0]) or Utility::web_error($sql, __FILE__, __LINE__);

        //取得最後新增資料的流水編號
        $psn = $xoopsDB->getInsertId();

        $sql = 'UPDATE `' . $xoopsDB->prefix('tad_player') . '` SET `image`=? WHERE `psn`=?';
        Utility::query($sql, 'si', [$psn . '.png', $psn]) or Utility::web_error($sql, __FILE__, __LINE__);

        set_time_limit(0);
        ini_set('memory_limit', '50M');

        $flv = Utility::auto_charset($flv, false);

        if (rename(_TAD_PLAYER_BATCH_UPLOAD_DIR . $flv, Tools::_TAD_PLAYER_FLV_DIR . $psn . '_' . $flv)) {
            if (!empty($_POST['img'][$filename])) {
                $pic_file = _TAD_PLAYER_BATCH_UPLOAD_DIR . $_POST['img'][$filename];
                $pic_s_file = Tools::_TAD_PLAYER_IMG_DIR . 's_' . $psn . '.png';

                Utility::generateThumbnail($pic_file, $pic_s_file, 480);
                unlink($pic_file);
            }
        }
    }

    //刪除其他多餘檔案
    Utility::rrmdir(_TAD_PLAYER_BATCH_UPLOAD_DIR);

    return $pcsn;
}
