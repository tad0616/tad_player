<?php

//區塊主函式 (依人氣值挑出熱門影片)
function tad_player_b_show_3($options)
{
    global $xoopsDB;

    $sql    = "SELECT `psn`, `title`, `creator`, `location`, `image`, `info`, `uid`, `post_date`, `enable_group`, `counter` FROM " . $xoopsDB->prefix("tad_player") . " order by counter desc limit 0,{$options[0]}";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, _LINE__);

    $i = 0;

    while (list($psn, $title, $creator, $location, $image, $info, $uid, $post_date, $enable_group, $counter) = $xoopsDB->fetchRow($result)) {
        $block[$i]['psn']          = $psn;
        $block[$i]['title']        = $title;
        $block[$i]['location']     = $location;
        $block[$i]['creator']      = $creator;
        $block[$i]['mode']         = $options[1];
        $block[$i]['info']         = $info;
        $block[$i]['uid']          = $uid;
        $block[$i]['post_date']    = $post_date;
        $block[$i]['enable_group'] = $enable_group;
        $block[$i]['counter']      = $counter;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_tad_hot_media_edit($options)
{
    $selected0 = $options[1] == '0' ? "selected" : "";
    $selected1 = $options[1] == '1' ? "selected" : "";
    $selected2 = $options[1] == '2' ? "selected" : "";

    $form = "
    " . _MB_TADPLAYER_DISPLAY_AMOUNT . "
    <INPUT type='text' name='options[0]' value='{$options[0]}'><br>

    " . _MB_TADPLAYER_DISPLAY_MODE . "
    <select name='options[1]'>
        <option value='0' $selected0>" . _MB_TADPLAYER_DISPLAY_MODE_0 . "</option>
        <option value='1' $selected1>" . _MB_TADPLAYER_DISPLAY_MODE_1 . "</option>
        <option value='2' $selected2>" . _MB_TADPLAYER_DISPLAY_MODE_2 . "</option>
    </select>";

    return $form;
}
