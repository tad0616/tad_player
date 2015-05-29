<?php
include_once "header.php";
include_once "language/{$xoopsConfig['language']}/batch.php";


include_once $GLOBALS['xoops']->path( '/modules/system/include/functions.php' );
$op=system_CleanVars( $_REQUEST, 'op', '', 'string' );
$pcsn=system_CleanVars( $_REQUEST, 'pcsn', 0, 'int' );

switch($op){
	case "import":
	$pcsn=tad_player_batch_import();
	mk_list_xml($pcsn);
	header("location:index.php?pcsn=$pcsn");
	break;

	default:
  echo tad_player_batch_upload_form();
	break;
}



//tad_player批次上傳表單
function tad_player_batch_upload_form(){
	global $xoopsDB,$xoopsModuleConfig,$ok_video_ext,$ok_image_ext;

	$cate_select=get_tad_player_cate_option(0,0,$pcsn,1,false);
	$i=0;

  if ($dh = opendir(_TAD_PLAYER_BATCH_UPLOAD_DIR)) {
    while (($file = readdir($dh)) !== false) {
      if(substr($file,0,1)=='.')continue;

      $file=auto_charset($file,true);

			$f=explode(".",$file);
			//$filename=$f[0];
      foreach($f as $ff){
        $ext=strtolower($ff);
      }
      $end=(strlen($ext)+1)*-1;
      $filename=substr($file,0,$end);

      if(in_array($ext,$ok_video_ext)){
        $flv_arr['flv'][$filename]=$file;
			}elseif(in_array($ext,$ok_image_ext)){
        $flv_arr['img'][$filename]=$file;
			}
    }
    closedir($dh);

    foreach($flv_arr['flv'] as $filename=>$file ){
      if(!empty($flv_arr['img'][$filename])){
				$image=$flv_arr['img'][$filename];
				$image_form="<input type='hidden' name='img[$filename]' value='{$image}'>";
			}else{
        $image=$image_form="";
			}

			$tr.="<tr>
			<td class='title'><input type='checkbox' name='flv[$filename]' value='{$file}' checked>{$title}</td>
			<td class='col'>$file</td>
			<td class='col'>{$image}{$image_form}</td>
			</tr>\n";
		}
  }

  //$cate_select=to_utf8($cate_select);

	$main="
	<p>"._MD_TADPLAYER_BATCH_UPLOAD_TO."<span style='color:red;'>"._TAD_PLAYER_BATCH_UPLOAD_DIR."</span></p>
  <form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
  <input type='hidden' name='op' value='import'>
  <table class='table table-striped table-bordered table-hover'>
  <tr><td class='title'>"._MD_TADPLAYER_BATCH_OF_CSN."</td>
	<td class='col' colspan=2><select name='pcsn' size=1>
	$cate_select
	</select>"._MD_TADPLAYER_BATCH_NEW_PCSN."<input type='text' name='new_pcsn' size='10'></td></tr>
	$tr
  <tr><td colspan=3 class='bar'><button type='submit' class='btn btn-primary'>"._MD_BATCH_SAVE."</button></td></tr>
  </table>
  </form>";

	//$main=div_3d(_MD_INPUT_FORM,$main);

	return $main;
}


//批次匯入
function tad_player_batch_import(){
	global $xoopsDB,$xoopsUser,$xoopsModuleConfig;

	if(!empty($_POST['new_pcsn'])){
    $pcsn=add_tad_player_cate();
	}else{
		$pcsn=$_POST['pcsn'];
	}

	$uid=$xoopsUser->getVar('uid');
  $uid_name=XoopsUser::getUnameFromId($uid,1);
  //$now=xoops_getUserTimestamp(time());

	$now=date("Y-m-d H:i:s",xoops_getUserTimestamp(time()));
	foreach($_POST['flv'] as $filename => $flv){
	  if(empty($flv))continue;
		$sql = "insert into ".$xoopsDB->prefix("tad_player")." (pcsn,title,creator,location,image,info,uid,post_date,enable_group,counter) values('{$pcsn}','{$flv}','{$uid_name}','{$flv}','','{$flv}','{$uid}','{$now}','','0')";
		$xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
		//取得最後新增資料的流水編號
		$psn=$xoopsDB->getInsertId();

		$sql = "update ".$xoopsDB->prefix("tad_player")." set image='{$psn}.png' where psn='$psn'";
		$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

		set_time_limit(0);
		ini_set('memory_limit', '50M');

    $flv=auto_charset($flv,false);

		if(rename(_TAD_PLAYER_BATCH_UPLOAD_DIR.$flv,_TAD_PLAYER_FLV_DIR.$psn."_".$flv)){
		  if(!empty($_POST['img'][$filename])){
				$pic_file=_TAD_PLAYER_BATCH_UPLOAD_DIR.$_POST['img'][$filename];
				$pic_b_file=_TAD_PLAYER_IMG_DIR.$psn.".png";
				$pic_s_file=_TAD_PLAYER_IMG_DIR."s_".$psn.".png";

				$sub=strtolower(substr($_POST['img'][$filename],-3));
				if($sub=="gif"){
          $type="image/gif";
				}elseif($sub=="png"){
          $type="image/png";
				}elseif($sub=="jpg" or $sub=="peg" ){
          $type="image/jpeg";
				}
        mk_video_thumbnail($pic_file,$pic_b_file,$type,$xoopsModuleConfig['width']);
        mk_video_thumbnail($pic_file,$pic_s_file,$type,"120");

		  	unlink($pic_file);
		  }
		}
	}

	//刪除其他多餘檔案
	rrmdir(_TAD_PLAYER_BATCH_UPLOAD_DIR);

	return $pcsn;
}