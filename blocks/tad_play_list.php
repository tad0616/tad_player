<?php

//區塊主函式 (影音播放器區塊1說明)
function tad_player_play_list($options)
{
    global $xoopsDB;
    include_once XOOPS_ROOT_PATH . "/modules/tad_player/function_player.php";

    if (empty($options[0])) {
        retrun;
    }

    $modhandler        = xoops_gethandler('module');
    $xoopsModule       = $modhandler->getByDirname("tad_player");
    $config_handler    = xoops_gethandler('config');
    $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

    $autoplay = ($options[1] == 1) ? "true" : "false";
    $cate     = get_tad_player_cate($options[0]);
    $block    = play_code_jwplayer("block_cate{$options[0]}", $cate, $options[0], "playlist", $autoplay, $xoopsModuleConfig, "", $options[2], "bottom", $options[3]);

    //play_code_jwplayer($id='tp' , $file="",$sn="",$mode="",$autostart=false,$ModuleConfig=array(),$skin="",$list_width="",$list_where="bottom",$repeat=false)

    return $block;
}

//區塊編輯函式
function tad_player_play_list_edit($options)
{
    global $xoopsDB;
    $select = tp_block_cate_select($options[0]);

    $chked3_1 = ($options[1] == '1') ? "checked" : "";
    $chked3_0 = ($options[1] == '0') ? "checked" : "";
    $chked5_0 = ($options[3] == "false") ? "checked" : "";
    $chked5_1 = ($options[3] == "true") ? "checked" : "";

    $form = "
  " . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM0 . "<br>
  $select<br>

  " . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM3 . "
  <INPUT type='radio' $chked3_1 name='options[1]' value='1'>" . _MB_TADPLAYER_AUTOPLAY . "
  <INPUT type='radio' $chked3_0 name='options[1]' value='0'>" . _MB_TADPLAYER_DONT_AUTOPLAY . "<br>
  " . _MB_TADPLAYER_LIST_HEIGHT . "<INPUT type='text' name='options[2]' value='{$options[2]}'><br>
  " . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM4 . "
  <INPUT type='radio' $chked5_1 name='options[3]' value='true'>" . _MB_TADPLAYER_LIST_REPEAT . "
  <INPUT type='radio' $chked5_0 name='options[3]' value='false'>" . _MB_TADPLAYER_DONT_REPEAT . "

  ";

    return $form;
}

//分類選單
function tp_block_cate_select($pcsn = 0)
{
    $cate_select = tp_block_get_tad_player_cate_option(0, 0, $pcsn);
    $select      = "<select name='options[0]' size='6'>
  $cate_select
  </select>";

    return $select;
}

//取得分類下拉選單
function tp_block_get_tad_player_cate_option($of_csn = 0, $level = 0, $v = "", $show_dot = '1', $optgroup = true, $chk_view = '1')
{
    global $xoopsDB;
    $dot = ($show_dot == '1') ? str_repeat(_MB_TADPLAYER_BLANK, $level) : "";
    $level += 1;

    $sql    = "select count(*),pcsn from " . $xoopsDB->prefix("tad_player") . " group by pcsn";
    $result = $xoopsDB->query($sql);
    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$pcsn] = $count;
    }

    $option = ($of_csn) ? "" : "<option value='0'>" . _MB_TADPLAYER_CATE_SELECT . "</option>";
    $sql    = "select pcsn,title from " . $xoopsDB->prefix("tad_player_cate") . " where of_csn='{$of_csn}' order by sort";
    $result = $xoopsDB->query($sql);

    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        $selected = ($v == $pcsn) ? "selected" : "";
        if (empty($cate_count[$pcsn]) and $optgroup) {
            $option .= "<optgroup label='{$title}' style='font-style: normal;color:black;'>" . tp_block_get_tad_player_cate_option($pcsn, $level, $v, "0") . "</optgroup>";
        } else {
            $counter = (empty($cate_count[$pcsn])) ? 0 : $cate_count[$pcsn];
            $option .= "<option value='{$pcsn}' $selected >{$dot}{$title} ($counter)</option>";
            $option .= tp_block_get_tad_player_cate_option($pcsn, $level, $v);
        }
    }

    return $option;
}
