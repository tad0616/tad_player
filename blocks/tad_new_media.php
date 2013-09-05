<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: tad_new_media.php,v 1.1 2008/05/05 03:24:03 tad Exp $
// ------------------------------------------------------------------------- //

//區塊主函式 (影音播放器區塊2說明)
function tad_new_media($options){
	global $xoopsDB;

	$sql = "SELECT `psn`, `title`, `creator`, `location`, `image`, `info`, `uid`, `post_date`, `enable_group`, `counter` FROM ".$xoopsDB->prefix("tad_player")." order by post_date desc limit 0,{$options[0]}";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

  $i=0;

  while(list($psn,$title,$creator,$location,$image,$info,$uid,$post_date,$enable_group,$counter)=$xoopsDB->fetchRow($result)){
    $block[$i]['psn']=$psn;
    $block[$i]['title']=$title;
    $block[$i]['location']=$location;
    $block[$i]['creator']=$creator;
    $block[$i]['image']=$image;
    $block[$i]['info']=$info;
    $block[$i]['uid']=$uid;
    $block[$i]['post_date']=substr($post_date,5,5);
    $block[$i]['enable_group']=$enable_group;
    $block[$i]['counter']=$counter;
    $i++;
  }
  
  
	return $block;
}

//區塊編輯函式
function tad_new_media_edit($options){

	$form="
	"._MB_TADPLAYER_TAD_NEW_MEDIA_EDIT_BITEM0."
	<INPUT type='text' name='options[0]' value='{$options[0]}'>
	";
	return $form;
}

?>