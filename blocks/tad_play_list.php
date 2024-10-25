<?php
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_player\Tools;

//區塊主函式 (影音播放器區塊1說明)
function tad_player_play_list($options)
{

    if (empty($options[0])) {
        return;
    }

    $pcsn = (int) $options[0];
    $cate = Tools::get_tad_player_cate($options[0]);
    $autoplay = $options[1] !== 'true' ? 'false' : 'true';
    $loop = $options[2] !== 'true' ? 'false' : 'true';
    $position = $options[3] !== 'right' ? 'bottom' : 'right';

    $block = Tools::play_code_player("block_cate_{$pcsn}", $cate, $pcsn, 'playlist', $autoplay, $loop, $position);

    return $block;
}

//區塊編輯函式
function tad_player_play_list_edit($options)
{
    global $xoopsDB;
    $select = tp_block_cate_select($options[0]);

    $chked1_0 = ('true' !== $options[1]) ? 'checked' : '';
    $chked1_1 = ('true' === $options[1]) ? 'checked' : '';
    $chked2_0 = ('true' !== $options[2]) ? 'checked' : '';
    $chked2_1 = ('true' === $options[2]) ? 'checked' : '';
    $chked3_r = ('bottom' !== $options[3]) ? 'checked' : '';
    $chked3_b = ('bottom' === $options[3]) ? 'checked' : '';

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
                <input type='radio' $chked1_1 name='options[1]' value='true'>" . _YES . "
                <input type='radio' $chked1_0 name='options[1]' value='false'>" . _NO . "<br>
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_REPEAT . "</lable>
            <div class='my-content'>
                <input type='radio' $chked2_1 name='options[2]' value='true'>" . _YES . "
                <input type='radio' $chked2_0 name='options[2]' value='false'>" . _NO . "
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_LIST_POSITION . "</lable>
            <div class='my-content'>
                <input type='radio' $chked3_r name='options[3]' value='right'>" . _MB_TADPLAYER_LIST_RIGHT . "
                <input type='radio' $chked3_b name='options[3]' value='bottom'>" . _MB_TADPLAYER_LIST_BOTTOM . '
            </div>
        </li>
    </ol>';

    return $form;
}

//分類選單
function tp_block_cate_select($pcsn = 0)
{
    $cate_select = tp_block_get_tad_player_cate_option(0, 0, $pcsn);
    $select = "<select name='options[0]' size='6' class='my-input' title='select category'>
    $cate_select
    </select>";

    return $select;
}

//取得分類下拉選單
function tp_block_get_tad_player_cate_option($of_csn = 0, $level = 0, $v = '', $show_dot = '1', $optgroup = true, $chk_view = '1')
{
    global $xoopsDB;
    $dot = ('1' == $show_dot) ? str_repeat(_MB_TADPLAYER_BLANK, $level) : '';
    $level += 1;

    $sql = 'SELECT COUNT(*), `pcsn` FROM `' . $xoopsDB->prefix('tad_player') . '` GROUP BY `pcsn`';
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$pcsn] = $count;
    }

    $option = ($of_csn) ? '' : "<option value='0'>" . _MB_TADPLAYER_CATE_SELECT . '</option>';
    $sql = 'SELECT `pcsn`, `title` FROM `' . $xoopsDB->prefix('tad_player_cate') . '` WHERE `of_csn`=? ORDER BY `sort`';
    $result = Utility::query($sql, 'i', [$of_csn]) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        $selected = ($v == $pcsn) ? 'selected' : '';
        $counter = (empty($cate_count[$pcsn])) ? 0 : $cate_count[$pcsn];
        $option .= "<option value='{$pcsn}' $selected >{$dot}{$title} ($counter)</option>";
        $option .= tp_block_get_tad_player_cate_option($pcsn, $level, $v);
    }

    return $option;
}
