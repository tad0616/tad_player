<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: tad_hot_media.php,v 1.1 2008/05/05 03:24:03 tad Exp $
// ------------------------------------------------------------------------- //

//區塊主函式 (依人氣值挑出熱門影片)
function tad_player_b_show_3($options){
	global $xoopsDB;

	$sql = "SELECT `psn`, `title`, `creator`, `location`, `image`, `info`, `uid`, `post_date`, `enable_group`, `counter` FROM ".$xoopsDB->prefix("tad_player")." order by counter desc limit 0,{$options[0]}";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


  $i=0;

  while(list($psn,$title,$creator,$location,$image,$info,$uid,$post_date,$enable_group,$counter)=$xoopsDB->fetchRow($result)){
    $block[$i]['psn']=$psn;
    $block[$i]['title']=$title;
    $block[$i]['location']=$location;
    $block[$i]['creator']=$creator;
    $block[$i]['image']=$options[1]?$image:"";
    $block[$i]['info']=$info;
    $block[$i]['uid']=$uid;
    $block[$i]['post_date']=$post_date;
    $block[$i]['enable_group']=$enable_group;
    $block[$i]['counter']=$counter;
    $i++;
  }


	return $block;

}

//區塊編輯函式
function tad_tad_hot_media_edit($options){
  $checked1=$options[1]=='1'?"checked":"";
  $checked0=$options[1]=='0'?"checked":"";

	$form="
	"._MB_TADPLAYER_TAD_HOT_MEDIA_EDIT_BITEM0."
	<INPUT type='text' name='options[0]' value='{$options[0]}'><br>

  "._MB_TADPLAYER_TAD_HOT_MEDIA_EDIT_BITEM1."
  <INPUT type='radio' name='options[1]' value='1' $checked1>"._YES."
  <INPUT type='radio' name='options[1]' value='0' $checked0>"._NO."";
	return $form;
}

?>