<?php
/*-----------引入檔案區--------------*/
include "header.php";
$xoopsOption['template_main'] = "tp_play_tpl.html";
include XOOPS_ROOT_PATH."/header.php";
/*-----------function區--------------*/

//播放
function play($get_psn=""){
  global $xoopsDB,$xoopsModuleConfig,$xoopsUser,$xoopsTpl,$xoTheme;

  $file=get_tad_player($get_psn);
  $ok_cat=chk_cate_power();

  $user_group=array();
  if($xoopsUser){
    $user_group=$xoopsUser->getGroups();
  }

  $enable_group_arr=explode(",",$file['enable_group']);
  $same=array_intersect($enable_group_arr,$user_group);
  if((!empty($file['pcsn']) and !in_array($file['pcsn'],$ok_cat)) or (!empty($file['enable_group']) and empty($same))){
    redirect_header("index.php",3,sprintf(_MD_TADPLAYER_NO_POWER,$file['title']));
  }

  add_counter($get_psn);

  $h1_title="<a href='index.php?pcsn={$file['pcsn']}&op=playlist' style='color: rgb(51,102,255);'><img src='images/playlist.png' alt='playlist.png, 5.6kB' title='Playlist' border='0' height='48' width='48' align='absmiddle' hspace=3></a>";


  $play_code=play_code_jwplayer("file{$get_psn}",$file,$get_psn,"single");


  $all['pcsn']=$file['pcsn'];

  $info=$file['info'];

  if(empty($info)){
    $info=xoops_substr(strip_tags($file['$description']), 0, 100);
  }

  if(empty($info)){
    $info=$file['creator']." ".$file['post_date'];
  }


  $arr=get_pcsn_path($file['pcsn']);
  if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/jBreadCrumb.php")){
    redirect_header("index.php",3, _MD_NEED_TADTOOLS);
  }
  include_once XOOPS_ROOT_PATH."/modules/tadtools/jBreadCrumb.php";
  $jBreadCrumb=new jBreadCrumb($arr,false);
  $path=$jBreadCrumb->render();


  $jquery_path=get_jquery(true);

  $xoops_module_header="
  $jquery_path
  <meta proprery=\"og:title\" content=\"{$file['title']}\" />
  <meta proprery=\"og:description\" content=\"{$info}\" />
  <meta property=\"og:image\" content=\""._TAD_PLAYER_IMG_URL."s_{$file['image']}\" />
  <meta property=\"og:video\" content=\"".XOOPS_URL."/modules/tad_player/play.php?psn=$get_psn\"/>
  <meta name=\"video_height\" content=\"{$file['width']}\" />
  <meta name=\"video_width\" content=\"{$file['height']}\" />
  <meta name=\"video_type\" content=\"application/x-shockwave-flash\" />
  ";

  include_once XOOPS_ROOT_PATH."/modules/tadtools/star_rating.php";
  $rating=new rating("tad_player","10",'','simple');
  $rating->add_rating("psn",$get_psn);
  $star_rating=$rating->render();
  $star_rating.="<div id='rating_psn_{$get_psn}'></div>";




  $xoopsTpl->assign( "media" , $play_code) ;
  $xoopsTpl->assign( "content" , $file['content']) ;
  $xoopsTpl->assign( "path_bar" , $path);
  if (is_object($xoTheme)) {
    $xoTheme->addMeta( 'meta', 'keywords', $file['title']);
    $xoTheme->addMeta( 'meta', 'description', $info) ;
  } else {
    $xoopsTpl->assign('xoops_meta_keywords','keywords',$file['title']);
    $xoopsTpl->assign('xoops_meta_description', $info);
  }

  $xoopsTpl->assign( "xoops_module_header" , $xoops_module_header);
  $xoopsTpl->assign( "xoops_pagetitle",$file['title']);
  $xoopsTpl->assign( "star_rating" , $star_rating) ;
$xoopsTpl->assign( "pcsn" , $file['pcsn']) ;
}


//找出選單
function get_cate_play($get_psn="",$size=1){
  global $xoopsDB,$xoopsModuleConfig;
  $file=get_tad_player($get_psn);

  $sql = "select a.psn,a.title,b.title from ".$xoopsDB->prefix("tad_player")." as a left join ".$xoopsDB->prefix("tad_player_cate")." as b on a.pcsn=b.pcsn where a.pcsn='{$file['pcsn']}' order by a.sort, a.post_date desc";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

  $option="";
  while(list($psn,$title,$cate_title)=$xoopsDB->fetchRow($result)){
    $selected=($psn==$get_psn)?"selected":"";
    $option.="<option value='{$psn}' $selected>$title</option>\n";
  }

  $cate_select=get_tad_player_cate_option(0,0,$file['pcsn']);
  $select="
  <form action='' method='post'>
  <select id='main_opt' name='main_opt' onchange='getList(this)' style='width:150px;'>
  $cate_select
  </select>
  <select id='sub_opt' name='sub_opt' size=1 onChange=\"window.location.href='{$_SERVER['PHP_SELF']}?psn=' + this.value\" >
  $option
  </select>
  </form>";


  return $select;
}

/*-----------執行動作判斷區----------*/
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$psn=(empty($_REQUEST['psn']))?"":intval($_REQUEST['psn']);
$pcsn=(empty($_REQUEST['pcsn']))?"":intval($_REQUEST['pcsn']);

$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "bootstrap" , get_bootstrap()) ;
$xoopsTpl->assign( "jquery" , get_jquery(true)) ;
$xoopsTpl->assign( "isAdmin" , $isAdmin) ;
$xoopsTpl->assign( "psn" , $psn) ;

switch($op){

  case "delete_tad_player_file":
  delete_tad_player($psn);
  header("location:index.php?pcsn=$pcsn");
  break;

  default:
  if(empty($psn))header("location:index.php");
  play($psn);
  break;
}

/*-----------秀出結果區--------------*/

$xoopsTpl->assign( "select" , get_cate_play($psn)) ;
$xoopsTpl->assign( "push" , push_url($xoopsModuleConfig['use_social_tools']));

$facebook_comments=facebook_comments($xoopsModuleConfig['facebook_comments_width'],'tad_player','play.php','psn',$psn);
$xoopsTpl->assign( "facebook_comments" , $facebook_comments);


include_once XOOPS_ROOT_PATH.'/include/comment_view.php';
include_once XOOPS_ROOT_PATH.'/footer.php';

?>
