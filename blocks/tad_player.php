<?php
use XoopsModules\Tadtools\Utility;

//區塊主函式 (影音播放器區塊1說明)
function tad_player($options)
{
    global $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/tad_player/function_player.php';

    $moduleHandler = xoops_getHandler('module');
    $xoopsModule = $moduleHandler->getByDirname('tad_player');
    $configHandler = xoops_getHandler('config');
    $xoopsModuleConfig = $configHandler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

    if (empty($options[0])) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('tad_player') . ' ORDER BY rand() LIMIT 0,1';
    } elseif (0 === mb_strpos($options[0], 'pcsn')) {
        $sn = explode('_', $options[0]);
        $sql = 'select * from ' . $xoopsDB->prefix('tad_player') . " where pcsn='{$sn[1]}' order by rand() limit 0,1";
    } else {
        $psn = $options[0];
        $sql = 'select * from ' . $xoopsDB->prefix('tad_player') . " where psn='$psn'";
    }

    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $file = $xoopsDB->fetchArray($result);

    $file = get_tad_player($file['psn']);
    $block = play_code_jwplayer("block{$file['psn']}", $file, $file['psn'], 'single', $options[1], $xoopsModuleConfig, null, null, null, $options[2]);

    //play_code_jwplayer($id='tp' , $file="",$sn="",$mode="",$autostart=false,$ModuleConfig=array(),$skin="",$list_width="",$list_where="bottom",$repeat=false)
    return $block;
}

//區塊編輯函式
function tad_player_edit($options)
{
    global $xoopsDB;
    $seled0_0 = ('' == $options[0]) ? 'selected' : '';
    $chked3_0 = ('0' == $options[1]) ? 'checked' : '';
    $chked3_1 = ('1' == $options[1]) ? 'checked' : '';
    $chked4_0 = ('false' === $options[2]) ? 'checked' : '';
    $chked4_1 = ('true' === $options[2]) ? 'checked' : '';

    $sql = 'SELECT a.psn,a.pcsn,a.title,b.title FROM ' . $xoopsDB->prefix('tad_player') . ' AS a LEFT JOIN ' . $xoopsDB->prefix('tad_player_cate') . ' AS b ON a.pcsn=b.pcsn ORDER BY a.post_date DESC';
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

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
            <lable class='my-label'>" . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM0 . "</lable>
            <div class='my-content'>
                $select
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM3 . "</lable>
            <div class='my-content'>
                <input type='radio' $chked3_1 name='options[1]' value='1'>" . _MB_TADPLAYER_AUTOPLAY . "
                <input type='radio' $chked3_0 name='options[1]' value='0'>" . _MB_TADPLAYER_DONT_AUTOPLAY . "
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_TAD_PLAYER_EDIT_BITEM4 . "</lable>
            <div class='my-content'>
                <input type='radio' $chked4_1 name='options[2]' value='true'>" . _MB_TADPLAYER_REPEAT . "
                <input type='radio' $chked4_0 name='options[2]' value='false'>" . _MB_TADPLAYER_DONT_REPEAT . '
            </div>
        </li>
    </ol> ';

    return $form;
}
