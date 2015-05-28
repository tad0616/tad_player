<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-03-23
// $Id: index.php,v 1.5 2008/05/10 11:46:50 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
if(file_exists("mainfile.php")){
  include_once "mainfile.php";
}elseif("../../mainfile.php"){
  include_once "../../mainfile.php";
}
include_once "function.php";
/*-----------function區--------------*/


function show_cate($pcsn,$passwd){
	global $xoopsDB,$xoopsUser,$xoopsModule,$xoopsModuleConfig,$xoopsTpl,$xoopsOption;



  $jquery=get_jquery();

  //以流水號取得某筆tad_player_cate資料
  $cate=get_tad_player_cate($pcsn);

  //可觀看相簿
  $ok_cat=chk_cate_power();

  //呈現資料預設值
  $data="";

  //找出分類下所有影音檔
  $sql = "select * from ".$xoopsDB->prefix("tad_player")." where pcsn='{$pcsn}' order by sort , post_date";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

  while($all=$xoopsDB->fetchArray($result)){
    foreach($all as $k=>$v){
      $$k=$v;
    }

    if(substr($image,0,4)=='http'){
    	$image = basename($image);
		}

		//整理影片圖檔
		if(empty($image) or !file_exists(_TAD_PLAYER_IMG_DIR."s_{$psn}.png")){
			$ext=substr($location,-3);
	    if($ext=="mp3"){
	      $pic="mp3.png";
			}else{
	      $pic="flv.png";
			}
			$pic="images/$pic";
		}else{
      $pic=_TAD_PLAYER_IMG_URL."s_{$psn}.png";
		}

    $data.="
    <div class='PhotoCate' style='background-image:url($pic);background-repeat:no-repeat;background-position:center center;background-size:cover' onClick=\"location.href='{$_SERVER['PHP_SELF']}?psn={$psn}';\" onfocus=\"location.href='{$_SERVER['PHP_SELF']}?psn={$psn}';\">
    <div style='color:#D0D0D0;' class='text_shadow'>{$title}</div>
    </div>
    ";
  }

  $main="
  <div id='main'>
    <p>{$data}</p>
  </div>
  <p style='clear:both;'></p>";
  return $main;
}



//觀看某多媒體檔案
function view_media($psn=""){
	global $xoopsDB,$xoopsUser,$xoopsModule,$xoopsModuleConfig,$isAdmin;

	//所有分類名稱
	$cate_all=get_tad_player_cate_all();

	$all=get_tad_player($psn);
  foreach($all as $k=>$v){
    $$k=$v;
  }

	if(!empty($pcsn)){
		$ok_cat=chk_cate_power();
		if(!in_array($pcsn,$ok_cat)){
		 	header("location:{$_SERVER['PHP_SELF']}");
		}
	}

	//找出上一張或下一張
  $pnp=get_pre_next($pcsn,$psn);

  //計數器
	add_counter($psn);

	$play_code=play_code_jwplayer("pda{$psn}",$all,$psn,"pda");

	$back_news="";
	if(!empty($pnp['back']['psn'])){
	 //支援xlanguage
    if(function_exists('xlanguage_ml')){
      $pnp['back']['title']=xlanguage_ml($pnp['back']['title']);
    }
    $title=xoops_substr($pnp['back']['title'], 0, 30);
    $back_news="<a href='{$_SERVER['PHP_SELF']}?psn={$pnp['back']['psn']}' class='nav'>&#x21E6; {$title}</a>";
  }

	$next_news="";
	if(!empty($pnp['next']['psn'])){
	      //支援xlanguage
      if(function_exists('xlanguage_ml')){
        $pnp['next']['title']=xlanguage_ml($pnp['next']['title']);
      }

    $title=xoops_substr($pnp['next']['title'], 0, 30);
    $next_news="<a href='{$_SERVER['PHP_SELF']}?psn={$pnp['next']['psn']}' class='nav'>&#x21E8; {$title}</a>";
  }



  $home="<a href='{$_SERVER['PHP_SELF']}?pcsn=$pcsn' class='nav'>&#x21E7;"._TAD_BACK_PAGE."</a>";

	$nav="<p style='width:100%;'>
   <div>$home</div>
   <div>$back_news</div>
   <div>$next_news</div>
   </p>
   <div style='clear:both;'></div>";

  $data="
  <table id='main' style='width:100%;'>
  <tr><td align='center'><a name='video_top'>{$play_code}</a></td></tr>
  <tr><td style='color:#E0E0E0;text-align:center'>{$title}</td></tr>
  <tr><td style='color:#E0E0E0'>{$content}</td></tr>
  <tr><td style='color:#E0E0E0'>{$nav}</td></tr>
  </table>
  ";


	return $data;
}



//找出上一張或下一張
function get_pre_next($pcsn="",$now_sn=""){
  global $xoopsDB;
  $sql = "select psn,title from ".$xoopsDB->prefix("tad_player")." where pcsn='{$pcsn}' order by sort , post_date";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $stop=false;
  $pre=0;
  while(list($psn,$title)=$xoopsDB->fetchRow($result)){
    if($stop){
      $next=$psn;
      $next_title=$title;
      break;
    }
    if($psn==$now_sn){
      $now=$psn;
      $stop=true;
    }else{
      $pre=$psn;
      $pre_title=$title;
    }
  }
  $main['back']['psn']=$pre;
  $main['back']['title']=$pre_title;
  $main['next']['psn']=$next;
  $main['next']['title']=$next_title;

  return $main;
}


/*-----------執行動作判斷區----------*/
$_REQUEST['op']=(empty($_REQUEST['op']))?"":$_REQUEST['op'];

$psn=(isset($_REQUEST['psn']))?intval($_REQUEST['psn']) : 0;
$pcsn=(isset($_REQUEST['pcsn']))?intval($_REQUEST['pcsn']) : 0;


$jquery=get_jquery();


switch($_REQUEST['op']){

	default:
	if(!empty($psn)){
		$main=view_media($psn);
    $file=get_tad_player($psn);
    $pcsn=$file['pcsn'];
	}else{
		$main=show_cate($pcsn);
	}
	break;
}

//分類下拉選單
$cate_option=get_tad_player_cate_option(0,0,$pcsn);

$jquery=get_jquery();
/*-----------秀出結果區--------------*/
echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
  <head>
  <meta http-equiv='content-type' content='text/html; charset="._CHARSET."'>
  <title></title>
  $jquery
  <script type='text/javascript'>
    $(document).ready(function(){
      var w=$(document.body).width();
      var thumb_w=(w/2)+30;
      var pic_w=thumb_w-90;
      var pic_h=pic_w+40;
      $('.thumb').css('width',thumb_w);
      $('.PhotoCate').css('width',pic_w).css('height',pic_w);
    });
  </script>
  <style>
    body,td,div,#cate_menu option,a{
      font-size:56px;
      text-decoration:none;
      border:none;
    }

    .nav{
      color:#FFFF99;
    }

    .PhotoCate{
    	margin: 5px;
    	float: left;
    	width: 350px;
    	height: 350px;
    	overflow:hidden;
    	position: relative;
    	cursor: pointer;
    	background-color: rgb(0,0,0);
    	border:10px solid #202020;
    }

    .text_shadow {
      text-shadow: #333 1px 1px 1px; filter:shadow(Color=#333333, Direction=135, Strength=1);
    }

  </style>
  </head>
  <body style='background-color:black;'>

  <select style='width:100%;font-size:56px;' onChange=\"window.location.href='{$_SERVER['PHP_SELF']}?pcsn=' + this.value\" id='cate_menu'>$cate_option</select>
  $main

  </body>
</html>
";

?>