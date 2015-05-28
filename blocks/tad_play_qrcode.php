<?php
//區塊主函式 (QR Code)
function tad_play_qrcode_show($options){
  if(preg_match("/tad_player\/index.php\?pcsn=/i", $_SERVER['REQUEST_URI'])){
    $url=str_replace("index.php","pda.php",$_SERVER['REQUEST_URI']);
  }elseif(preg_match("/tad_player\/play.php\?psn=/i", $_SERVER['REQUEST_URI'])){
    $url=str_replace("play.php","pda.php",$_SERVER['REQUEST_URI']);
  }elseif(preg_match("/tad_player\/$/i", $_SERVER['REQUEST_URI'])){
    $url=$_SERVER['REQUEST_URI']."pda.php";
  }else{
    return ;
  }

  //高亮度語法
  if(!file_exists(TADTOOLS_PATH."/qrcode.php")){
   redirect_header("index.php",3, _MA_NEED_TADTOOLS);
  }
  include_once TADTOOLS_PATH."/qrcode.php";
  $qrcode= new qrcode();
  $block=$qrcode->render($url);
	return $block;
}