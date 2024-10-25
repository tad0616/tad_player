<?php
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;

//區塊主函式 (影音播放器區塊1說明)
function tad_player($options)
{
    global $xoopsDB;

    if (empty($options[0])) {
        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_player') . '` ORDER BY RAND() LIMIT 1';
    } elseif (0 === mb_strpos($options[0], 'pcsn')) {
        $sn = explode('_', $options[0]);
        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('tad_player') . '` WHERE pcsn = ? ORDER BY RAND() LIMIT 1';
        $params = [$sn[1]];
    } else {
        $psn = $options[0];
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('tad_player') . ' WHERE psn = ?';
        $params = [$psn];
    }

    $result = Utility::query($sql, isset($params) ? str_repeat('i', count($params)) : '', $params ?? []) or Utility::web_error($sql, __FILE__, __LINE__);

    $file = $xoopsDB->fetchArray($result);

    $file = Tools::get_tad_player($file['psn']);

    $autoplay = $options[1] !== 'true' ? 'false' : 'true';
    $loop = $options[2] !== 'true' ? 'false' : 'true';

    $block = Tools::play_code_player("block{$file['psn']}", $file, $file['psn'], 'single', $autoplay, $loop);
    return $block;
}

//區塊編輯函式
function tad_player_edit($options)
{
    global $xoopsDB;
    $seled0_0 = ('' == $options[0]) ? 'selected' : '';
    $chked1_0 = ('false' == $options[1]) ? 'checked' : '';
    $chked1_1 = ('true' == $options[1]) ? 'checked' : '';
    $chked2_0 = ('false' === $options[2]) ? 'checked' : '';
    $chked2_1 = ('true' === $options[2]) ? 'checked' : '';

    $sql = 'SELECT `a`.`psn`, `a`.`pcsn`, `a`.`title`, `b`.`title` FROM `' . $xoopsDB->prefix('tad_player') . '` AS `a` LEFT JOIN `' . $xoopsDB->prefix('tad_player_cate') . '` AS `b` ON `a`.`pcsn`=`b`.`pcsn` ORDER BY `a`.`post_date` DESC';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $select = "<select name='options[0]' class='my-input'>
    <option value='0'>" . _MB_TADPLAYER_RANDOM_PLAY . '</option>';
    $old_pcsn = 0;
    while (list($psn, $pcsn, $title, $cate_title) = $xoopsDB->fetchRow($result)) {
        $selected = ($options[0] == $psn) ? 'selected' : '';

        if ($old_pcsn != $pcsn) {
            $select .= "<option value='pcsn_{$pcsn}' $selected>[{$cate_title}] " . _MB_TADPLAYER_RANDOM_PLAY . "</option>\n";
            $old_pcsn = $pcsn;
        }
        $select .= "<option value='{$psn}' $selected>[{$cate_title}] {$title}</option>\n";
    }
    $select .= '</select>';

    $form = "
    <ol class='my-form'>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_CATE . "</lable>
            <div class='my-content'>
                $select
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_AUTOPLAY . "</lable>
            <div class='my-content'>
                <input type='radio' $chked1_1 name='options[1]' value='true'>" . _MB_TADPLAYER_AUTOPLAY . "
                <input type='radio' $chked1_0 name='options[1]' value='false'>" . _MB_TADPLAYER_DONT_AUTOPLAY . "
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_REPEAT . "</lable>
            <div class='my-content'>
                <input type='radio' $chked2_1 name='options[2]' value='true'>" . _MB_TADPLAYER_REPEAT . "
                <input type='radio' $chked2_0 name='options[2]' value='false'>" . _MB_TADPLAYER_DONT_REPEAT . '
            </div>
        </li>
    </ol> ';

    return $form;
}
