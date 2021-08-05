<?php

use XoopsModules\Tadtools\Utility;

//區塊主函式 (依人氣值挑出熱門影片)
function tad_hot_media($options)
{
    global $xoopsDB;

    $sql = 'SELECT `psn`, `title`, `creator`, `location`, `image`, `info`, `uid`, `post_date`, `enable_group`, `counter` FROM ' . $xoopsDB->prefix('tad_player') . " order by counter desc limit 0,{$options[0]}";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $i = 0;

    while (list($psn, $title, $creator, $location, $image, $info, $uid, $post_date, $enable_group, $counter) = $xoopsDB->fetchRow($result)) {
        $block[$i]['psn'] = $psn;
        $block[$i]['title'] = $title;
        $block[$i]['location'] = $location;
        $block[$i]['creator'] = $creator;
        $block[$i]['mode'] = $options[1];
        $block[$i]['info'] = $info;
        $block[$i]['uid'] = $uid;
        $block[$i]['post_date'] = $post_date;
        $block[$i]['enable_group'] = $enable_group;
        $block[$i]['counter'] = $counter;
        $i++;
    }

    return $block;
}

//區塊編輯函式
function tad_tad_hot_media_edit($options)
{
    $selected0 = '0' == $options[1] ? 'selected' : '';
    $selected1 = '1' == $options[1] ? 'selected' : '';
    $selected2 = '2' == $options[1] ? 'selected' : '';

    $form = "
    <ol class='my-form'>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_DISPLAY_AMOUNT . "</lable>
            <div class='my-content'>
                <input type='text' class='my-input' name='options[0]' value='{$options[0]}' size=6>
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADPLAYER_DISPLAY_MODE . "</lable>
            <div class='my-content'>
                <select name='options[1]' class='my-input' title='select mode'>
                    <option value='0' $selected0>" . _MB_TADPLAYER_DISPLAY_MODE_0 . "</option>
                    <option value='1' $selected1>" . _MB_TADPLAYER_DISPLAY_MODE_1 . "</option>
                    <option value='2' $selected2>" . _MB_TADPLAYER_DISPLAY_MODE_2 . '</option>
                </select>
            </div>
        </li>
    </ol>';

    return $form;
}
