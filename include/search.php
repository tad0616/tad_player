<?php
use XoopsModules\Tadtools\Utility;

function tad_player_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;

    if (is_array($queryarray)) {
        foreach ($queryarray as $k => $v) {
            $arr[$k] = $xoopsDB->escape($v);
        }
        $queryarray = $arr;
    } else {
        $queryarray = [];
    }

    $sql = 'SELECT psn,title,post_date,uid FROM ' . $xoopsDB->prefix('tad_player') . ' WHERE 1';
    if (0 != $userid) {
        $sql .= ' AND uid=' . $userid . ' ';
    }
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((title LIKE '%$queryarray[0]%' OR creator LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";
            $sql .= "( title LIKE '%$queryarray[$i]%' OR creator LIKE '%$queryarray[$i]%')";
        }
        $sql .= ') ';
    }
    $sql .= 'ORDER BY post_date DESC';
    $result = $xoopsDB->query($sql, $limit, $offset) or Utility::web_error($sql, __FILE__, __LINE__);
    $ret = [];
    $i = 0;
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        if ($myrow['title']) {
            $ret[$i]['image'] = 'images/video.png';
            $ret[$i]['link'] = 'play.php?psn=' . $myrow['psn'];
            $ret[$i]['title'] = $myrow['title'];
            $ret[$i]['time'] = tadplayer_tnsday2ts($myrow['post_date']);
            $ret[$i]['uid'] = $myrow['uid'];
            $i++;
        }
    }

    return $ret;
}

//轉換成時間戳記
function tadplayer_tnsday2ts($day = '')
{
    $dd = explode(' ', $day);
    $d = explode('-', $dd[0]);
    $t = explode(':', $dd[1]);
    $ts = mktime($t[0], $t[1], $t[2], $d['1'], $d['2'], $d['0']);

    return $ts;
}
