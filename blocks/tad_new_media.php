<?php

//區塊主函式 (影音播放器區塊2說明)
function tad_new_media($options)
{
    global $xoopsDB;

    $sql    = "SELECT `psn`, `title`, `creator`, `location`, `image`, `info`, `uid`, `post_date`, `enable_group`, `counter` FROM " . $xoopsDB->prefix("tad_player") . " order by post_date desc limit 0,{$options[0]}";
    $result = $xoopsDB->query($sql) or web_error($sql);

    $i = 0;

    while (list($psn, $title, $creator, $location, $image, $info, $uid, $post_date, $enable_group, $counter) = $xoopsDB->fetchRow($result)) {
        $block[$i]['psn']          = $psn;
        $block[$i]['title']        = $title;
        $block[$i]['location']     = $location;
        $block[$i]['creator']      = $creator;
        $block[$i]['image']        = $options[1] ? $image : "";
        $block[$i]['info']         = $info;
        $block[$i]['uid']          = $uid;
        $block[$i]['post_date']    = substr($post_date, 5, 5);
        $block[$i]['enable_group'] = $enable_group;
        $block[$i]['counter']      = $counter;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_new_media_edit($options)
{
    $checked1 = $options[1] == '1' ? "checked" : "";
    $checked0 = $options[1] == '0' ? "checked" : "";
    $form     = "
    " . _MB_TADPLAYER_TAD_NEW_MEDIA_EDIT_BITEM0 . "
    <INPUT type='text' name='options[0]' value='{$options[0]}'><br>

  " . _MB_TADPLAYER_TAD_NEW_MEDIA_EDIT_BITEM1 . "
  <INPUT type='radio' name='options[1]' value='1' $checked1>" . _YES . "
  <INPUT type='radio' name='options[1]' value='0' $checked0>" . _NO . "";

    return $form;
}
