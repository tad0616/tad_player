<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-02-28
// $Id: get_list.php,v 1.1 2008/05/05 03:23:39 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "header.php";
/*-----------function區--------------*/

if(isset($_GET['pcsn'])){

	$pcsn=intval($_GET['pcsn']);
	
	$sql = "select a.psn,a.title,b.title from ".$xoopsDB->prefix("tad_player")." as a left join ".$xoopsDB->prefix("tad_player_cate")." as b on a.pcsn=b.pcsn where a.pcsn='{$pcsn}' order by a.post_date desc";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

	while(list($psn,$title,$cate_title)=$xoopsDB->fetchRow($result)){
	  $title=to_utf8($title);
	  echo "obj.options[obj.options.length] = new Option('$title','$psn');\n";
	}
	
}

?>