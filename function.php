<?php
//引入TadTools的函式庫
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
 redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";



define("_TAD_PLAYER_UPLOAD_DIR",XOOPS_ROOT_PATH."/uploads/tad_player/");
define("_TAD_PLAYER_FLV_DIR",XOOPS_ROOT_PATH."/uploads/tad_player/flv/");
define("_TAD_PLAYER_IMG_DIR",XOOPS_ROOT_PATH."/uploads/tad_player/img/");

$uid_dir=0;
if($xoopsUser){
  $uid_dir=$xoopsUser->getVar('uid');
}

define("_TAD_PLAYER_BATCH_UPLOAD_DIR",XOOPS_ROOT_PATH."/uploads/tad_player_batch_uploads/user_{$uid_dir}/");
mk_dir(_TAD_PLAYER_BATCH_UPLOAD_DIR);
define("_TAD_PLAYER_BATCH_UPLOAD_URL",XOOPS_URL."/uploads/tad_player_batch_uploads/user_{$uid_dir}/");

$ok_video_ext=array("flv","mp4","m4v","f4v","mov","mp3","webm","ogv","ogg","swf","3gp","3g2","aac","m4a");
$ok_image_ext=array("jpg","png","gif");

include_once "function_player.php";


//底下影片數
function count_video_num($pcsn="0"){
  global $xoopsDB,$xoopsModule;
  //其底下所有子目錄的影片數
  $sql = "select pcsn from ".$xoopsDB->prefix("tad_player_cate")." where of_csn='{$pcsn}'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $sub_count=0;
  while(list($sub_pcsn)=$xoopsDB->fetchRow($result)){
    $sub=count_video_num($sub_pcsn);
    $sub_count+=$sub['num'];
  }

  $pic="";

  //該目錄影片數
  $sql = "select psn,image,location from ".$xoopsDB->prefix("tad_player")." where pcsn = '$pcsn' order by rand()";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $count=$xoopsDB->getRowsNum($result);
  while(list($psn,$image,$location)=$xoopsDB->fetchRow($result)){
    if(substr($image,0,4)=='http'){
      $pic=$image;
      break;
    }elseif(!empty($image) and file_exists(_TAD_PLAYER_IMG_DIR."{$psn}.png")){
      $pic=_TAD_PLAYER_IMG_URL."{$psn}.png";
      break;
    }else{
      $ext=substr($location,-3);
      if($ext=="mp3"){
        $pic="mp3.png";
      }else{
        $pic="flv.png";
      }
      $pic="images/$pic";
      break;
    }
  }
  $counter['num']=$count + $sub_count;
  $counter['rel_num']=$count;
  $counter['img']=$pic;
  return $counter;
}



//取得路徑
function get_pcsn_path($pcsn="",$sub=false){
  global $xoopsDB;

  if(!$sub){
    $home[_TAD_TO_MOD]=XOOPS_URL."/modules/tad_player/index.php";
  }else{
    $home=array();
  }

  $sql = "select title,of_csn from ".$xoopsDB->prefix("tad_player_cate")." where pcsn='{$pcsn}'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($title,$of_csn)=$xoopsDB->fetchRow($result);

  $opt_sub=(!empty($of_csn))?get_pcsn_path($of_csn,true):"";
  $opt='';
  if(!empty($title)){
    $opt[$title]=XOOPS_URL."/modules/tad_player/index.php?pcsn=$pcsn";
  }
  if(is_array($opt_sub)){
    $path=array_merge($home,$opt_sub,$opt);
  }elseif(is_array($opt)){
    $path=array_merge($home,$opt);
  }else{
    $path=$home;
  }
  return $path;
}

//熱門影片
function hot_media(){
  global $xoopsDB,$xoopsModule,$xoopsModuleConfig;

  $sql = "select a.psn,a.pcsn,a.title,a.counter,b.title from ".$xoopsDB->prefix("tad_player")." as a left join ".$xoopsDB->prefix("tad_player_cate")." as b on a.pcsn=b.pcsn order by a.counter desc limit 0,10";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $i=0;
  while(list($psn,$pcsn,$title,$counter,$cate_title)=$xoopsDB->fetchRow($result)){
    $hot_media[$i]['psn']=$psn;
    $hot_media[$i]['title']=$title;
    $hot_media[$i]['counter']=$counter;
    $i++;
  }
  return $hot_media;
}



//新增資料到tad_player_cate中
function add_tad_player_cate(){
  global $xoopsDB,$xoopsModuleConfig;
  if(empty($_POST['new_pcsn']))return;

  $enable_group=implode(",",$_POST['enable_group']);
  $sql = "insert into ".$xoopsDB->prefix("tad_player_cate")." (of_csn,title,enable_group,sort) values('{$_POST['pcsn']}','{$_POST['new_pcsn']}','','0')";
  $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  //取得最後新增資料的流水編號
  $pcsn=$xoopsDB->getInsertId();
  return $pcsn;
}

//分類選單
function cate_select($pcsn=0,$size=20){
  $cate_select=get_tad_player_cate_option(0,0,$pcsn);

  $PHP_SELF=basename($_SERVER['PHP_SELF']);
  $select="
  <select name='pcsn' class='span12' size='{$size}' onChange=\"window.location.href='{$PHP_SELF}?pcsn=' + this.value\">
  $cate_select
  </select>";

  return $select;
}



//取得所有類別標題
function tad_player_get_all_news_cate($of_csn=0,$code="big5"){
  global $xoopsDB;
  $sql = "select pcsn,title,enable_group from ".$xoopsDB->prefix("tad_player_cate")." where of_csn='{$of_csn}' order by sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


  $option="";
  while(list($pcsn,$title,$enable_group)=$xoopsDB->fetchRow($result)){
    $have_sub=tad_player_chk_cate_have_sub($pcsn);
    if($code=="utf8")$title=to_utf8($title);
    $option.="<li><a href='index.php?pcsn=$pcsn'>$title</a>";
    if($have_sub){
        $option.="\n<ul>\n";
        $option.="<li parentId='$pcsn'><a href='#'>Loading</a></li>";
        $option.="\n</ul>\n";
      }
    $option.="</li>";

  }
  return $option;
}


//檢查有無子選項
function tad_player_chk_cate_have_sub($pcsn=0){
  global $xoopsDB;
  $sql = "select pcsn from ".$xoopsDB->prefix("tad_player_cate")." where of_csn='{$pcsn}'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, _MD_TADNEW_DB_SELECT_ERROR1);
  while(list($sub_pcsn)=$xoopsDB->fetchRow($result)){
    if(!empty($sub_pcsn))return true;
  }
  return false;
}


//刪除tad_player某筆資料資料
function delete_tad_player($psn=""){
  global $xoopsDB,$isAdmin,$xoopsUser,$xoopsModule;

  if(!isset($isAdmin)){
    if ($xoopsUser) {
      $module_id = $xoopsModule->getVar('mid');
      $isAdmin=$xoopsUser->isAdmin($module_id);
    }else{
      return;
    }
  }

  if(!$isAdmin)return;
  //刪除檔案
  $file=get_tad_player($psn);
  $file['location']=auto_charset($file['location'],false);
  $file['image']=auto_charset($file['location'],image);
  unlink(_TAD_PLAYER_FLV_DIR."{$psn}_{$file['location']}");
  unlink(_TAD_PLAYER_IMG_DIR."s_{$psn}.png");
  unlink(_TAD_PLAYER_IMG_DIR."{$psn}_{$file['image']}");
  mk_list_xml($file['pcsn']);
  $sql = "delete from ".$xoopsDB->prefix("tad_player")." where psn='$psn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

}


//做縮圖
function mk_video_thumbnail($filename="",$thumb_name="",$type="image/jpeg",$width="100"){

  ini_set('memory_limit', '50M');
  // Get new sizes
  list($old_width, $old_height) = getimagesize($filename);

  $percent=($old_width>$old_height)?round($width/$old_width,2):round($width/$old_height,2);

  $newwidth = ($old_width>$old_height)?$width:$old_width * $percent;
  $newheight = ($old_width>$old_height)?$old_height * $percent:$width;

  // Load
  $thumb = imagecreatetruecolor($newwidth, $newheight);
  if($type=="image/jpeg" or $type=="image/jpg" or $type=="image/pjpg" or $type=="image/pjpeg"){
    $source = imagecreatefromjpeg($filename);
    $type="image/jpeg";
  }elseif($type=="image/png"){
    $source = imagecreatefrompng($filename);
    $type="image/png";
  }elseif($type=="image/gif"){
    $source = imagecreatefromgif($filename);
    $type="image/gif";
  }

  // Resize
  imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $old_width, $old_height);

  header("Content-type: image/png");
  imagepng($thumb,$thumb_name);
/*
  // Content type
  header("Content-type: ".$type);
  // Output
  if($type=="image/jpeg" or $type=="image/jpg" or $type=="image/pjpg" or $type=="image/pjpeg"){
    imagejpeg($thumb,$thumb_name,90);
  }elseif($type=="image/png"){
    imagepng($thumb,$thumb_name);
  }elseif($type=="image/gif"){
    imagegif($thumb,$thumb_name);
  }
*/
  return;
  exit;
}



//判斷某人在哪些類別中有觀看或發表(upload)的權利
function chk_cate_power($kind=""){
  global $xoopsDB,$xoopsUser,$xoopsModule;
  if(!empty($xoopsUser)){
    $module_id = $xoopsModule->getVar('mid');
    $isAdmin=$xoopsUser->isAdmin($module_id);
    if($isAdmin){
      $ok_cat[]="0";
    }
    $user_array=$xoopsUser->getGroups();
  }else{
    $user_array=array(3);
    $isAdmin=0;
  }

  $col=($kind=="upload")?"enable_upload_group":"enable_group";

  $sql = "select pcsn,$col from ".$xoopsDB->prefix("tad_player_cate")."";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

  while(list($pcsn,$power)=$xoopsDB->fetchRow($result)){
    if($isAdmin or empty($power)){
      $ok_cat[]=$pcsn;
    }else{
      $power_array=explode(",",$power);
      foreach($power_array as $gid){
        if(in_array($gid,$user_array)){
          $ok_cat[]=$pcsn;
          break;
        }
      }
    }
  }

  return $ok_cat;
}


//取得分類下拉選單
function get_tad_player_cate_option($of_csn=0,$level=0,$v="",$show_dot='1',$optgroup=true,$kind='view'){
  global $xoopsDB;
  $dot=($show_dot=='1')?str_repeat(_MD_TADPLAYER_BLANK,$level):"";
  $level+=1;

  $sql = "select count(*),pcsn from ".$xoopsDB->prefix("tad_player")." group by pcsn";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while(list($count,$pcsn)=$xoopsDB->fetchRow($result)){
    $cate_count[$pcsn]=$count;
  }

  $option=($of_csn)?"":"<option value='0'>"._MD_TADPLAYER_CATE_SELECT."</option>";
  $sql = "select pcsn,title from ".$xoopsDB->prefix("tad_player_cate")." where of_csn='{$of_csn}' order by sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


  if($kind)$ok_cat=chk_cate_power($kind);
  while(list($pcsn,$title)=$xoopsDB->fetchRow($result)){

    if($kind){
      if(!in_array($pcsn,$ok_cat)){
        continue;
      }
    }

    $selected=($v==$pcsn)?"selected":"";
    if(empty($cate_count[$pcsn]) and $optgroup){
      $option.="<optgroup label='{$title}' style='font-style: normal;color:black;'>".get_tad_player_cate_option($pcsn,$level,$v,"0")."</optgroup>";
    }else{
      $counter=(empty($cate_count[$pcsn]))?0:$cate_count[$pcsn];
      $option.="<option value='{$pcsn}' $selected >{$dot}{$title} ($counter)</option>";
      $option.=get_tad_player_cate_option($pcsn,$level,$v,$show_dot,$optgroup,$kind);
    }



  }
  return $option;
}



//取得tad_player_cate所有資料陣列
function get_tad_player_cate_all(){
  global $xoopsDB;
  $sql = "select pcsn,title from ".$xoopsDB->prefix("tad_player_cate");
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $data="";
  while(list($pcsn,$title)=$xoopsDB->fetchRow($result)){
    $data[$pcsn]=$title;
  }
  return $data;
}



//計數器
function add_counter($psn=""){
  global $xoopsDB;
  $sql = "update ".$xoopsDB->prefix("tad_player")." set `counter` = `counter` + 1 where psn='{$psn}'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}



//製作播放清單
function mk_list_xml($pcsn=""){
  global $xoopsDB,$xoopsModule,$upload_dir;


  $cate=get_tad_player_cate($pcsn);

  $sql = "SELECT * FROM ".$xoopsDB->prefix("tad_player")." WHERE `pcsn`='{$pcsn}' and `enable_group`='' order by sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


  $main="<rss version=\"2.0\" xmlns:jwplayer=\"http://rss.jwpcdn.com/\">
  <channel>\n";

  while($midia=$xoopsDB->fetchArray($result)){
    foreach($midia as $k=>$v){
      $$k=$v;
    }

    $title=htmlspecialchars($title);
    $creator=htmlspecialchars($creator);

    //$location=urlencode($location);
    if(substr($image,0,4)=="http"){
      $image=$image;
    }else{
      $image=_TAD_PLAYER_IMG_URL.$image;
    }

    $image=(empty($image))?"":"<jwplayer:image>{$image}</jwplayer:image>";


    if(empty($location) and !empty($youtube)){
      $YTid=getYTid($youtube);
      $media="http://youtu.be/{$YTid}";
    }elseif(substr($location,0,4)=='http'){
      $media=$location;
    }else{
      $media=_TAD_PLAYER_FLV_URL."{$psn}_{$location}";
    }

    //$media=str_replace("&feature=youtu.be","",$media);
    //$media=str_replace("=","%3D",$media);
    //$media=str_replace("?","%3F",$media);
    //$media=str_replace("&","%26",$media);


    if(substr($post_date,0,2)=='20')$post_date=strtotime($post_date);
    $post_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($post_date));

    if(empty($info)){
      $info=xoops_substr(strip_tags($description), 0, 100);
    }
    if(empty($info)){
      $info=$creator." ".$post_date;
    }

    $main.="
    <item>
      <title>{$title}</title>
      <description>{$info}</description>
      $image
      <jwplayer:source file=\"{$media}\" />
    </item>\n\n";

  }
  $main.="
  </channel>\n</rss>";

  $main=to_utf8($main);

  $filename =_TAD_PLAYER_UPLOAD_DIR."{$pcsn}_list.xml";

  if (!$handle = fopen($filename, 'w')) {
    redirect_header($_SERVER['PHP_SELF'],3, sprintf(_MD_TADPLAYER_CANT_OPEN,$filename));
  }

  if (fwrite($handle, $main) === FALSE) {
    redirect_header($_SERVER['PHP_SELF'],3, sprintf(_MD_TADPLAYER_CANT_WRITE,$filename));
  }
  fclose($handle);
}


/********************* 預設函數 *********************/


?>
