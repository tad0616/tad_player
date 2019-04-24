<?php
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
include __DIR__ . '/header.php';
/*-----------function區--------------*/

if (isset($_GET['pcsn'])) {
    $pcsn = (int) ($_GET['pcsn']);

    $sql = 'select a.psn,a.title,b.title from ' . $xoopsDB->prefix('tad_player') . ' as a left join ' . $xoopsDB->prefix('tad_player_cate') . " as b on a.pcsn=b.pcsn where a.pcsn='{$pcsn}' order by a.post_date desc";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($psn, $title, $cate_title) = $xoopsDB->fetchRow($result)) {
        $title = to_utf8($title);
        echo "obj.options[obj.options.length] = new Option('$title','$psn');\n";
    }
}
