<?php
/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "tad_player_adm_main.tpl";
include_once __DIR__ . '/header.php';
include_once "../function.php";

/*-----------function區--------------*/
//列出所有tad_player資料
function list_tad_player($pcsn = "")
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $xoopsTpl;

    $cate_select = cate_select($pcsn);
    $xoopsTpl->assign('cate_select', $cate_select);

    $cate = get_tad_player_cate($pcsn);
    $xoopsTpl->assign('link_to_cate', sprintf(_MA_TADPLAYER_LINK_TO_CATE, $cate["title"]));

    // $where_pcsn = !empty($pcsn) ? "" : "order by pcsn,sort";

    $sql = "select `psn` , `title` , `location` , `image` , `info` , `width` , `height` , `counter` , `enable_group` , `uid` , `post_date` from " . $xoopsDB->prefix("tad_player") . " where pcsn='{$pcsn}' order by sort ";

    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);

    $i = 0;

    $data = array();
    while ($all = $xoopsDB->fetchArray($result)) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }

        $g_txt = txt_to_group_name($enable_group, _MA_TADPLAYER_ALL_OK, ', ');

        if (substr($image, 0, 4) == 'http') {
            $pic = $image;
        } elseif (empty($image) or !file_exists(_TAD_PLAYER_IMG_DIR . "{$psn}.png")) {
            $ext = substr($location, -3);
            if ($ext == "mp3") {
                $pic = "mp3.png";
            } else {
                $pic = "flv.png";
            }
            $pic = "../images/$pic";
        } else {
            $pic = _TAD_PLAYER_IMG_URL . "{$psn}.png";
        }

        $uid_name = XoopsUser::getUnameFromId($uid, 1);
        $uid_name = (empty($uid_name)) ? XoopsUser::getUnameFromId($uid, 0) : $uid_name;

        $post_date = substr($post_date, 0, 10);

        $data[$i]['psn']       = $psn;
        $data[$i]['pic']       = $pic;
        $data[$i]['title']     = $title;
        $data[$i]['pcsn']      = $pcsn;
        $data[$i]['uid_name']  = $uid_name;
        $data[$i]['counter']   = $counter;
        $data[$i]['width']     = $height;
        $data[$i]['post_date'] = $post_date;
        $data[$i]['g_txt']     = $g_txt;
        $data[$i]['info']      = $info;

        $i++;
    }
    get_jquery(true);
    $option = get_tad_player_cate_option(0, 0, $pcsn, 1, false);

    $xoopsTpl->assign('option', $option);
    $xoopsTpl->assign('pcsn', $pcsn);
    $xoopsTpl->assign('data', $data);
    $xoopsTpl->assign('cate_width', $cate["width"]);
    $xoopsTpl->assign('cate_height', $cate["height"]);
    $xoopsTpl->assign('cate', $cate);
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/sweet_alert.php";
    $sweet_alert      = new sweet_alert();
    $sweet_alert_code = $sweet_alert->render("delete_tad_player_cate_func", "main.php?op=delete_tad_player_cate&pcsn=", 'pcsn');
}

//列出所有tad_player_cate資料
function list_tad_player_cate_tree($def_pcsn = "")
{
    global $xoopsDB, $xoopsTpl;

    $cate_count = array();
    $sql        = "select count(*),pcsn from " . $xoopsDB->prefix("tad_player") . " group by pcsn";
    $result     = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    while (list($count, $pcsn) = $xoopsDB->fetchRow($result)) {
        $cate_count[$pcsn] = $count;
    }

    $path     = get_tad_player_cate_path($def_pcsn);
    $path_arr = array_keys($path);
    $data[]   = "{ id:0, pId:0, name:'" . _MA_TADPLAYER_CATE_SELECT . "', url:'main.php', target:'_self', open:true}";

    $sql    = "select pcsn,of_csn,title from " . $xoopsDB->prefix("tad_player_cate") . " order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    while (list($pcsn, $of_csn, $title) = $xoopsDB->fetchRow($result)) {
        $font_style      = $def_pcsn == $pcsn ? ", font:{'background-color':'yellow', 'color':'black'}" : '';
        $open            = in_array($pcsn, $path_arr) ? 'true' : 'false';
        $display_counter = empty($cate_count[$pcsn]) ? "" : " ({$cate_count[$pcsn]})";
        $data[]          = "{ id:{$pcsn}, pId:{$of_csn}, name:'{$title}{$display_counter}', url:'main.php?pcsn={$pcsn}', open: {$open} ,target:'_self' {$font_style}}";
    }

    $json = implode(",\n", $data);

    if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ztree.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/ztree.php";
    $ztree      = new ztree("cate_tree", $json, "save_drag.php", "save_cate_sort.php", "of_csn", "pcsn");
    $ztree_code = $ztree->render();
    $xoopsTpl->assign('ztree_code', $ztree_code);
    $xoopsTpl->assign('cate_count', $cate_count);

    return $data;
}
//分類選單
function cate_select($pcsn = 0, $size = 20)
{
    $cate_select = get_tad_player_cate_option(0, 0, $pcsn);

    $PHP_SELF = basename($_SERVER['PHP_SELF']);
    $select   = "
  <select name='pcsn' class='span12' size='{$size}' onChange=\"window.location.href='{$PHP_SELF}?pcsn=' + this.value\">
  $cate_select
  </select>";

    return $select;
}

//重新產生所有的XML
function mk_all_xml($the_pcsn = "")
{
    global $xoopsDB;
    $sql    = "select pcsn,title from " . $xoopsDB->prefix("tad_player_cate");
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);

    $log = "";
    while (list($pcsn, $title) = $xoopsDB->fetchRow($result)) {
        mk_list_json($pcsn);
        $log .= sprintf(_MA_TADPLAYER_XML_OK, $title) . "<br>";
    }
    $and_pcsn = (empty($the_pcsn)) ? "" : "?pcsn=$the_pcsn";
    redirect_header($_SERVER['PHP_SELF'] . $and_pcsn, 3, $log);

    return;
}

//儲存排序
function save_sort()
{
    global $xoopsDB;
    foreach ($_POST['sort'] as $psn => $sort) {
        $sql = "update  " . $xoopsDB->prefix("tad_player") . " set sort='{$sort}' where psn='{$psn}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);
    }

    return;
}

//批次刪除
function batch_del()
{
    foreach ($_POST['video'] as $psn) {
        delete_tad_player($psn);
    }
}

//批次搬移
function batch_move($new_pcsn = "")
{
    global $xoopsDB;
    $videos = implode(",", $_POST['video']);
    $sql    = "update " . $xoopsDB->prefix("tad_player") . " set `pcsn` = '{$new_pcsn}' where psn in($videos)";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    return $sn;
}

//批次新增標題
function batch_add_title()
{
    global $xoopsDB;
    $videos = implode(",", $_POST['video']);
    $sql    = "update " . $xoopsDB->prefix("tad_player") . " set  `title` = '{$_POST['add_title']}' where psn in($videos)";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);
}

//批次新增說明
function batch_add_info()
{
    global $xoopsDB;
    $videos = implode(",", $_POST['video']);
    $sql    = "update " . $xoopsDB->prefix("tad_player") . " set `info` = '{$_POST['add_info']}' where psn in($videos)";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    return $sn;
}

//批次更新寬與高
function update_wh()
{
    global $xoopsDB;
    $videos = implode(",", $_POST['video']);
    $sql    = "update " . $xoopsDB->prefix("tad_player") . " set `width` = '{$_POST['width']}' , `height` = '{$_POST['height']}' where psn in($videos)";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    return $sn;
}

//tad_player_cate編輯表單
function tad_player_cate_form($pcsn = "")
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsTpl;
    include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $xoopsTpl->assign('now_op', 'tad_player_cate_form');

    //抓取預設值
    if (!empty($pcsn)) {
        $DBV = get_tad_player_cate($pcsn);
        $xoopsTpl->assign('cate', $DBV);
    } else {
        $DBV = array();
    }

    //預設值設定

    $pcsn                = (!isset($DBV['pcsn'])) ? $pcsn : $DBV['pcsn'];
    $of_csn              = (!isset($DBV['of_csn'])) ? "" : $DBV['of_csn'];
    $title               = (!isset($DBV['title'])) ? "" : $DBV['title'];
    $enable_group        = (!isset($DBV['enable_group'])) ? array() : explode(",", $DBV['enable_group']);
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
    $SelectGroup_name->setExtra("class='form-control'");
    $enable_group = $SelectGroup_name->render();
    $xoopsTpl->assign('enable_group', $enable_group);

    //可上傳群組
    $SelectGroup_name = new XoopsFormSelectGroup("", "enable_upload_group", false, $enable_upload_group, 5, true);
    $SelectGroup_name->setExtra("class='form-control'");
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

//自動取得某分類下最大的排序
function auto_get_csn_sort($pcsn = "")
{
    global $xoopsDB;
    $sql            = "select max(`sort`) from " . $xoopsDB->prefix("tad_player_cate") . " where of_csn='{$pcsn}' group by of_csn";
    $result         = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    list($max_sort) = $xoopsDB->fetchRow($result);

    return ++$max_sort;
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
    $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    //取得最後新增資料的流水編號
    $pcsn = $xoopsDB->getInsertId();
    mk_list_json($pcsn);

    return $pcsn;
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
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);
    mk_list_json($pcsn);
    $log = "update $pcsn OK!";

    return $log;
}

//刪除tad_player_cate某筆資料資料
function delete_tad_player_cate($pcsn = "")
{
    global $xoopsDB;

    //先找出底下所有影片
    $sql = "select psn from " . $xoopsDB->prefix("tad_player") . " where pcsn='$pcsn'";
    $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    while (list($psn) = $xoopsDB->fetchRow($result)) {
        delete_tad_player($psn);
    }

    //找出底下分類，並將分類的所屬分類清空
    $sql = "update " . $xoopsDB->prefix("tad_player_cate") . " set  of_csn='' where of_csn='$pcsn'";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    $sql = "delete from " . $xoopsDB->prefix("tad_player_cate") . " where pcsn='$pcsn'";
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    unlink(_TAD_PLAYER_UPLOAD_DIR . "{$psn}_list.xml");
}

//重作縮圖
function mk_thumb($pcsn = "")
{
    global $xoopsDB;
    set_time_limit(0);
    $sql    = "select `psn`,`image` from " . $xoopsDB->prefix("tad_player") . " where pcsn='{$pcsn}' order by sort";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    while ($all = $xoopsDB->fetchArray($result)) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }

        $filename   = basename($image);
        $type       = getimagesize($image);
        $pic_s_file = _TAD_PLAYER_IMG_DIR . "s_" . $psn . ".png";
        // die("{$image}, {$pic_s_file}, {$type['mime']}");
        mk_video_thumbnail($image, $pic_s_file, $type['mime'], "480");

    }
}

/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op       = system_CleanVars($_REQUEST, 'op', '', 'string');
$psn      = system_CleanVars($_REQUEST, 'psn', 0, 'int');
$pcsn     = system_CleanVars($_REQUEST, 'pcsn', 0, 'int');
$new_pcsn = system_CleanVars($_REQUEST, 'new_pcsn', 0, 'int');

switch ($op) {

    //重作清單
    case "mk_list_json":
        mk_list_json($pcsn);
        redirect_header($_SERVER['PHP_SELF'] . "?pcsn=$pcsn", 3, _MA_TADPLAYER_JSON_OK);
        break;

    //儲存排序
    case "save_sort":
        save_sort();
        header("location: {$_SERVER['PHP_SELF']}?pcsn=$pcsn");
        exit;

    //重新產生所有的XML
    case "mk_all_xml":
        $main = mk_all_xml();
        break;

    case "del":
        batch_del();
        header("location: {$_SERVER['PHP_SELF']}?pcsn=$new_pcsn");
        exit;

    case "move":
        batch_move($new_pcsn);
        mk_list_json($pcsn);
        mk_list_json($new_pcsn);
        header("location: {$_SERVER['PHP_SELF']}?pcsn=$new_pcsn");
        exit;

    case "add_title":
        batch_add_title();
        mk_list_json($pcsn);
        header("location: {$_SERVER['PHP_SELF']}?pcsn={$pcsn}");
        exit;

    case "add_info":
        batch_add_info();
        mk_list_json($pcsn);
        header("location: {$_SERVER['PHP_SELF']}?pcsn={$pcsn}");
        exit;

    case "update_wh":
        update_wh();
        mk_list_json($pcsn);
        header("location: {$_SERVER['PHP_SELF']}?pcsn={$pcsn}");
        exit;

    //新增資料
    case "tad_player_cate_form";
        list_tad_player_cate_tree($pcsn);
        tad_player_cate_form($pcsn);
        break;

    //新增資料
    case "insert_tad_player_cate":
        insert_tad_player_cate();
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //刪除資料
    case "delete_tad_player_cate";
        delete_tad_player_cate($pcsn);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //更新資料
    case "update_tad_player_cate";
        update_tad_player_cate($pcsn);
        header("location: {$_SERVER['PHP_SELF']}?pcsn={$pcsn}");
        exit;

    //重作縮圖
    case "mk_thumb";
        mk_thumb($pcsn);
        header("location: {$_SERVER['PHP_SELF']}?pcsn={$pcsn}");
        exit;

    //預設動作
    default:

        list_tad_player_cate_tree($pcsn);
        list_tad_player($pcsn);
        break;

}

/*-----------秀出結果區--------------*/
include_once __DIR__ . '/footer.php';
