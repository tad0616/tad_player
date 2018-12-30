<?php

//區塊主函式 (影音播放器區塊1說明)
function tad_player($options)
{
    global $xoopsDB;
    include_once XOOPS_ROOT_PATH . "/modules/tad_player/function_player.php";

    $modhandler        = xoops_getHandler('module');
    $xoopsModule       = $modhandler->getByDirname("tad_player");
    $config_handler    = xoops_getHandler('config');
    $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

    if (empty($options[0])) {
        $sql = "SELECT * FROM " . $xoopsDB->prefix("tad_player") . " ORDER BY rand() LIMIT 0,1";
    } elseif (substr($options[0], 0, 4) == "pcsn") {
        $sn  = explode("_", $options[0]);
        $sql = "select * from " . $xoopsDB->prefix("tad_player") . " where pcsn='{$sn[1]}' order by rand() limit 0,1";
    } else {
        $psn = $options[0];
        $sql = "select * from " . $xoopsDB->prefix("tad_player") . " where psn='$psn'";
    }

    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    $file   = $xoopsDB->fetchArray($result);

    $file  = get_tad_player($file['psn']);
    $block = play_code_jwplayer("block{$file['psn']}", $file, $file['psn'], "single", $options[1], $xoopsModuleConfig, null, null, null, $options[2]);

    //play_code_jwplayer($id='tp' , $file="",$sn="",$mode="",$autostart=false,$ModuleConfig=array(),$skin="",$list_width="",$list_where="bottom",$repeat=false)
    return $block;
}

//區塊編輯函式
function tad_player_edit($options)
{
    global $xoopsDB;
    $seled0_0 = ($options[0] == "") ? "selected" : "";
    $chked3_0 = ($options[1] == "0") ? "checked" : "";
    $chked3_1 = ($options[1] == "1") ? "checked" : "";
    $chked4_0 = ($options[2] == "false") ? "checked" : "";
    $chked4_1 = ($options[2] == "true") ? "checked" : "";

    $sql = "SELECT a.psn,a.pcsn,a.title,b.title FROM " . $xoopsDB->prefix("tad_player") . " AS a LEFT JOIN " . $xoopsDB->prefix("tad_player_cate") . " AS b ON a.pcsn=b.pcsn ORDER BY a.post_date DESC";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);

    $select = "<select name='options[0]'>
  <option value='0'>" . _MB_TADPLAYER_RANDOM_PLAY . "</option>";
    $old_pcsn = 0;
    while (list($psn, $pcsn, $title, $cate_title) = $xoopsDB->fetchRow($result)) {
        $selected = ($options[0] == $psn) ? "selected" : "";

        if ($old_pcsn != $pcsn) {
            $select .= "<option value='pcsn_{$pcsn}' $selected>[{$cate_title}] " . _MB_TADPLAYER_RANDOM_PLAY . "</option>\n";
            $old_pcsn = $pcsn;
        }
        $select .= "<option value='{$psn}' $selected>[{$cate_title}] {$title}</option>\n";
    }
    $select .= "</select>";

    $form = "
  " . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM0 . "
  $select<br>
  " . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM3 . "
  <INPUT type='radio' $chked3_1 name='options[1]' value='1'>" . _MB_TADPLAYER_AUTOPLAY . "
  <INPUT type='radio' $chked3_0 name='options[1]' value='0'>" . _MB_TADPLAYER_DONT_AUTOPLAY . "<br>
  " . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM4 . "
  <INPUT type='radio' $chked4_1 name='options[2]' value='true'>" . _MB_TADPLAYER_REPEAT . "
  <INPUT type='radio' $chked4_0 name='options[2]' value='false'>" . _MB_TADPLAYER_DONT_REPEAT . "
  ";

    return $form;
}
