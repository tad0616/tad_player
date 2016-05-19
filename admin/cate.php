<?php
/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "tad_player_adm_cate.html";
include_once __DIR__ . '/header.php';
include_once "../function.php";

/*-----------function區--------------*/
//tad_player_cate編輯表單
function tad_player_cate_form($pcsn = "")
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsTpl;
    include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $xoopsTpl->assign('now_op', 'tad_player_cate_form');

    //抓取預設值
    if (!empty($pcsn)) {
        $DBV = get_tad_player_cate($pcsn);
    } else {
        $DBV = array();
    }

    $span = ($_SESSION['bootstrap'] == '3') ? 'form-control' : 'span12';

    //預設值設定

    $pcsn                = (!isset($DBV['pcsn'])) ? $pcsn : $DBV['pcsn'];
    $of_csn              = (!isset($DBV['of_csn'])) ? "" : $DBV['of_csn'];
    $title               = (!isset($DBV['title'])) ? "" : $DBV['title'];
    $enable_group        = (!isset($DBV['enable_group'])) ? "" : explode(",", $DBV['enable_group']);
    $enable_upload_group = (!isset($DBV['enable_upload_group'])) ? array('1') : explode(",", $DBV['enable_upload_group']);
    $sort                = (!isset($DBV['sort'])) ? auto_get_csn_sort() : $DBV['sort'];

    $op = (empty($pcsn)) ? "insert_tad_player_cate" : "update_tad_player_cate";

    $xoopsTpl->assign('op', $op);
    $xoopsTpl->assign('pcsn', $pcsn);
    $xoopsTpl->assign('of_csn', $of_csn);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('sort', $sort);

    //$cate_select = get_tad_player_cate_option(0, 0, $of_csn, 1, false);

    //可見群組
    $SelectGroup_name = new XoopsFormSelectGroup("", "enable_group", false, $enable_group, 5, true);
    $SelectGroup_name->addOption("", _MA_TADPLAYER_ALL_OK, false);
    $SelectGroup_name->setExtra("class='{$span}'");
    $enable_group = $SelectGroup_name->render();
    $xoopsTpl->assign('enable_group', $enable_group);

    //可上傳群組
    $SelectGroup_name = new XoopsFormSelectGroup("", "enable_upload_group", false, $enable_upload_group, 5, true);
    $SelectGroup_name->setExtra("class='{$span}'");
    $enable_upload_group = $SelectGroup_name->render();
    $xoopsTpl->assign('enable_upload_group', $enable_upload_group);

    $path    = get_tad_player_cate_path($pcsn, false);
    $patharr = array_keys($path);
    $i       = 0;
    foreach ($patharr as $k => $of_csn) {
        $j                       = $k + 1;
        $path_arr[$i]['of_csn']  = $of_csn;
        $path_arr[$i]['def_csn'] = isset($patharr[$j]) ? $patharr[$j] : '';
        $i++;
    }
    $xoopsTpl->assign('path_arr', $path_arr);
}

//新增資料到tad_player_cate中
function insert_tad_player_cate()
{
    global $xoopsDB;
    if (empty($_POST['title'])) {
        return;
    }
    if (empty($_POST['enable_group']) or in_array("", $_POST['enable_group'])) {
        $enable_group = "";
    } else {
        $enable_group = implode(",", $_POST['enable_group']);
    }

    if (empty($_POST['enable_upload_group'])) {
        $enable_upload_group = "1";
    } else {
        $enable_upload_group = implode(",", $_POST['enable_upload_group']);
    }

    $sql = "insert into " . $xoopsDB->prefix("tad_player_cate") . " (of_csn,title,enable_group,enable_upload_group,sort,width,height) values('{$_POST['of_csn']}','{$_POST['title']}','{$enable_group}','{$enable_upload_group}','{$_POST['sort']}','{$_POST['width']}','{$_POST['height']}')";
    $xoopsDB->query($sql) or web_error($sql);
    //取得最後新增資料的流水編號
    $pcsn = $xoopsDB->getInsertId();
    mk_list_xml($pcsn);

    return $pcsn;
}

//列出所有tad_player_cate資料
function list_tad_player_cate($of_csn = 1, $level = 0, $modify_pcsn = '')
{
    global $xoopsDB, $xoopsConfig;
    $old_level = $level;
    $left      = $level * 12 + 4;
    $level++;

    $sql = "select * from " . $xoopsDB->prefix("tad_player_cate") . " where of_csn='{$of_csn}' order by sort";

    $result = $xoopsDB->query($sql) or web_error($sql);

    if ($old_level == 0) {
        $form = tad_player_cate_form($modify_pcsn);

        //加入圖片提示
        if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/bubblepopup.php")) {
            redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
        }
        include_once XOOPS_ROOT_PATH . "/modules/tadtools/bubblepopup.php";
        $bubblepopup = new bubblepopup(false);

        $sql_cover    = "select pcsn from " . $xoopsDB->prefix("tad_player_cate") . " order by sort";
        $result_cover = $xoopsDB->query($sql_cover) or web_error($sql_cover);
        while (list($pcsn) = $xoopsDB->fetchRow($result_cover)) {

            //底下影片數
            $video = count_video_num($pcsn);
            $img   = empty($video['img']) ? "images/empty_cate_{$xoopsConfig['language']}.png" : $video['img'];

            if (!empty($img)) {
                $bubblepopup->add_tip("#cover_{$pcsn}", "<img src=\'" . _TAD_PLAYER_IMG_URL . "s_{$img}.png\'>", 'right');
            }
        }
        $bubblepopup_code = $bubblepopup->render();

        //加入表格樹
        if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/treetable.php")) {
            redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
        }
        include_once XOOPS_ROOT_PATH . "/modules/tadtools/treetable.php";
        $treetable      = new treetable(true, "pcsn", "of_csn", "#treetbl", "save_drag.php", ".folder", "#save_msg", true, ".sort", "save_cate_sort.php", "#save_msg");
        $treetable_code = $treetable->render();

        $data = "
        $treetable_code
        $bubblepopup_code
        <script>
        function delete_tad_player_cate_func(pcsn){
          var sure = window.confirm('" . _TAD_DEL_CONFIRM . "');
          if (!sure)  return;
          location.href=\"{$_SERVER['PHP_SELF']}?op=delete_tad_player_cate&pcsn=\" + pcsn;
        }
        </script>
        <div id='save_msg' style='float:right;'></div>
        <table id='treetbl' class='table table-striped table-bordered'>

        <tr><td colspan=5>$form</td></tr>
        <tr>
        <th>" . _MA_TADPLAYER_TITLE . "</th>
        <th>" . _MA_TADPLAYER_VIDEO_AMOUNT . "</th>
        <th>" . _MA_TADPLAYER_ENABLE_GROUP . "</th>
        <th>" . _MA_TADPLAYER_ENABLE_UPLOAD_GROUP . "</th>
        <th>" . _TAD_FUNCTION . "</th></tr>
        <tbody class='sort'>";
    } else {
        $data = "";
    }

    while ($all = $xoopsDB->fetchArray($result)) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }

        $g_txt  = txt_to_group_name($enable_group, _MA_TADPLAYER_ALL_OK);
        $gu_txt = txt_to_group_name($enable_upload_group, _MA_TADPLAYER_ALL_OK);

        //整理影片圖檔
        $video   = count_video_num($pcsn);
        $img     = empty($video['img']) ? "images/empty_cate_{$xoopsConfig['language']}.png" : $video['img'];
        $counter = $video['rel_num'];
        if (!empty($img) and file_exists(_TAD_PLAYER_IMG_DIR . "s_{$img}.png")) {
            $pic = "<img src='../images/image.png' id='cover_{$pcsn}'>";
        } else {
            $pic = "";
        }

        $class  = (empty($of_csn)) ? "" : "class='child-of-node-_{$of_csn}'";
        $parent = empty($of_csn) ? "" : "data-tt-parent-id='$of_csn'";
        $data .= "
        <tr data-tt-id='{$pcsn}' $parent id='node-_{$pcsn}' $class style='letter-spacing: 0em;'>
        <td nowrap>
        <img src='" . XOOPS_URL . "/modules/tadtools/treeTable/images/move_s.png' class='folder' alt='" . _MA_TREETABLE_MOVE_PIC . "' title='" . _MA_TREETABLE_MOVE_PIC . "'>
        <a href='../index.php?pcsn=$pcsn' target='_blank'>{$title}</a>
        {$pic}
        </td>
        <td>{$counter}</td>
        <td>{$g_txt}</td>
        <td>{$gu_txt}</td>
        <td>

        <img src='" . XOOPS_URL . "/modules/tadtools/treeTable/images/updown_s.png' style='cursor: s-resize;' alt='" . _MA_TREETABLE_SORT_PIC . "' title='" . _MA_TREETABLE_SORT_PIC . "'>

        <a href='{$_SERVER['PHP_SELF']}?op=tad_player_cate_form&pcsn=$pcsn' class='btn btn-mini btn-warning'>" . _TAD_EDIT . "</a>
        <a href=\"javascript:delete_tad_player_cate_func($pcsn);\" class='btn btn-mini btn-danger'>" . _TAD_DEL . "</a>
        <a href='{$_SERVER['PHP_SELF']}?op=mk_thumb&pcsn=$pcsn' class='btn btn-mini btn-info'>thumb</a>
        </td></tr>";
        $data .= list_tad_player_cate($pcsn, $level);
    }

    if ($old_level == 0) {
        $data .= "
        </tbody>
        </table>";
    }

    return $data;
}

//重作縮圖
function mk_thumb($pcsn = "")
{
    global $xoopsDB;
    set_time_limit(0);
    $sql    = "select `psn`,`image` from " . $xoopsDB->prefix("tad_player") . " where pcsn='{$pcsn}' order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while ($all = $xoopsDB->fetchArray($result)) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }
        if (!file_exists(_TAD_PLAYER_IMG_DIR . "s_{$psn}.png")) {
            $filename   = basename($image);
            $type       = getimagesize($image);
            $pic_s_file = _TAD_PLAYER_IMG_DIR . "s_" . $psn . ".png";
            mk_video_thumbnail($image, $pic_s_file, $type['mime'], "120");
        }
    }
}

//更新tad_player_cate某一筆資料
function update_tad_player_cate($pcsn = "")
{
    global $xoopsDB;
    if (empty($_POST['enable_group']) or in_array("", $_POST['enable_group'])) {
        $enable_group = "";
    } else {
        $enable_group = implode(",", $_POST['enable_group']);
    }

    if (empty($_POST['enable_upload_group'])) {
        $enable_upload_group = "1";
    } else {
        $enable_upload_group = implode(",", $_POST['enable_upload_group']);
    }
    krsort($_POST['of_csn_menu']);
    foreach ($_POST['of_csn_menu'] as $sn) {
        if (empty($sn)) {
            continue;
        } else {
            $of_csn = $sn;
            break;
        }
    }
    $sql = "update " . $xoopsDB->prefix("tad_player_cate") . " set  of_csn = '{$of_csn}', title = '{$_POST['title']}', enable_group = '{$enable_group}', enable_upload_group = '{$enable_upload_group}', sort = '{$_POST['sort']}', width = '{$_POST['width']}', height = '{$_POST['height']}' where pcsn='$pcsn'";
    $xoopsDB->queryF($sql) or web_error($sql);
    mk_list_xml($pcsn);
    $log = "update $pcsn OK!";

    return $log;
}

//刪除tad_player_cate某筆資料資料
function delete_tad_player_cate($pcsn = "")
{
    global $xoopsDB;

    //先找出底下所有影片
    $sql = "select psn from " . $xoopsDB->prefix("tad_player") . " where pcsn='$pcsn'";
    $xoopsDB->query($sql) or web_error($sql);
    while (list($psn) = $xoopsDB->fetchRow($result)) {
        delete_tad_player($psn);
    }

    //找出底下分類，並將分類的所屬分類清空
    $sql = "update " . $xoopsDB->prefix("tad_player_cate") . " set  of_csn='' where of_csn='$pcsn'";
    $xoopsDB->queryF($sql) or web_error($sql);

    $sql = "delete from " . $xoopsDB->prefix("tad_player_cate") . " where pcsn='$pcsn'";
    $xoopsDB->queryF($sql) or web_error($sql);

    unlink(_TAD_PLAYER_UPLOAD_DIR . "{$psn}_list.xml");
}

//自動取得某分類下最大的排序
function auto_get_csn_sort($pcsn = "")
{
    global $xoopsDB;
    $sql            = "select max(`sort`) from " . $xoopsDB->prefix("tad_player_cate") . " where of_csn='{$pcsn}' group by of_csn";
    $result         = $xoopsDB->query($sql) or web_error($sql);
    list($max_sort) = $xoopsDB->fetchRow($result);

    return ++$max_sort;
}

//列出所有tad_player_cate資料
function list_tad_player_cate_tree($def_csn = "")
{
    global $xoopsDB, $xoopsTpl;

    $sql    = "select count(*),pcsn from " . $xoopsDB->prefix("tad_player") . " group by pcsn";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$pcsn] = $count;
    }

    $path     = get_tad_player_cate_path($def_csn);
    $path_arr = array_keys($path);

    $sql    = "select pcsn,of_csn,title from " . $xoopsDB->prefix("tad_player_cate") . " order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($pcsn, $of_csn, $title) = $xoopsDB->fetchRow($result)) {

        $font_style      = $def_csn == $pcsn ? ", font:{'background-color':'yellow', 'color':'black'}" : '';
        $open            = in_array($pcsn, $path_arr) ? 'true' : 'false';
        $display_counter = empty($cate_count[$pcsn]) ? "" : " ({$cate_count[$pcsn]})";
        $data[]          = "{ id:{$pcsn}, pId:{$of_csn}, name:'{$title}{$display_counter}', url:'cate.php?pcsn={$pcsn}', open:{$open} {$font_style}}";
    }

    $json = implode(',', $data);

    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ztree.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/ztree.php";
    $ztree      = new ztree("album_tree", $json, "save_drag.php", "save_cate_sort.php", "of_csn", "pcsn");
    $ztree_code = $ztree->render();
    $xoopsTpl->assign('ztree_code', $ztree_code);

    return $data;
}

/*-----------執行動作判斷區----------*/
$op   = (!isset($_REQUEST['op'])) ? "" : $_REQUEST['op'];
$pcsn = (!isset($_REQUEST['pcsn'])) ? 0 : (int) ($_REQUEST['pcsn']);

switch ($op) {

    //新增資料
    case "insert_tad_player_cate":
        insert_tad_player_cate();
        header("location: {$_SERVER['PHP_SELF']}");
        break;

    //刪除資料
    case "delete_tad_player_cate";
        delete_tad_player_cate($pcsn);
        header("location: {$_SERVER['PHP_SELF']}");
        break;

    //更新資料
    case "update_tad_player_cate";
        $log = update_tad_player_cate($pcsn);
        redirect_header($_SERVER['PHP_SELF'], 3, $log);
        break;

    //重作縮圖
    case "mk_thumb";
        mk_thumb($pcsn);
        header("location: {$_SERVER['PHP_SELF']}");
        break;

    //新增資料
    case "tad_player_cate_form";
        list_tad_player_cate_tree($pcsn);
        tad_player_cate_form();
        break;

    //預設動作
    default:
        list_tad_player_cate_tree($pcsn);
        if (!empty($pcsn)) {
            tad_player_cate_form($pcsn);
        }
        break;

}

/*-----------秀出結果區--------------*/
include_once __DIR__ . '/footer.php';
