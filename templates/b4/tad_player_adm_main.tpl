<div class="container-fluid">
    <form action="main.php" method="post" role="form">
        <div class="row">
            <div class="col-sm-3">
                <div style="max-height: 300px; overflow: auto;">
                    <{$ztree_code}>
                    <div id="save_msg"></div>
                </div>
                <{if $now_op!="tad_player_cate_form"}>
                    <a href="main.php?op=tad_player_cate_form" class="btn btn-info btn-block"><{$smarty.const._MA_TADPLAYER_ADD_CATE}></a>
                    
                    <{if $data}>
                        <h2><{$smarty.const._MA_TADPLAYER_THE_ACT_IS}></h2>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="op" id="del" value="del">
                                    <label class="form-check-label" for="del"><{$smarty.const._TAD_DEL}></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="op" id="move" value="move">
                                    <label class="form-check-label" for="move"><{$smarty.const._MA_TADPLAYER_MOVE_TO}></label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select name="new_pcsn" onChange="check_one('move',false)" class="form-control"><{$option}></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="op" id="add_title" value="add_title">
                                    <label class="form-check-label" for="add_title"><{$smarty.const._MA_TADPLAYER_ADD_TITLE}></label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" name="add_title" class="form-control" onClick="check_one('add_title',false)" onkeypress="check_one('add_title',false)">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="op" id="add_info" value="add_info">
                                    <label class="form-check-label" for="add_info"><{$smarty.const._MA_TADPLAYER_ADD_INFO}></label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <textarea name="add_info" class="form-control" onClick="check_one('add_info',false)" onkeypress="check_one('add_info',false)"></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <input type="hidden" name="pcsn" value="<{$pcsn}>">
                            <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
                        </div>
                    <{/if}>
                <{/if}>
            </div>

            <div class="col-sm-9">

                <{if $pcsn!="" }>
                    <div class="row">
                        <div class="col-sm-4">
                            <h3>
                                <{$cate.title}>
                            </h3>
                        </div>
                        <div class="col-sm-8 text-right">
                            <div style="margin-top: 10px;">
                                <a href="main.php?op=mk_list_json&pcsn=<{$pcsn}>" class="btn btn-success"><{$smarty.const._MA_TADPLAYER_MK_JSON}></a>
                                <a href="main.php?op=mk_thumb&pcsn=<{$pcsn}>" class="btn btn-info"><{$smarty.const._MA_TADPLAYER_MK_THUMBS}></a>
                                <a href="javascript:delete_tad_player_cate_func(<{$cate.pcsn}>);" class="btn btn-danger <{if $cate_count.$pcsn > 0}>disabled<{/if}>"><{$smarty.const._TAD_DEL}></a>
                                <a href="main.php?op=tad_player_cate_form&pcsn=<{$pcsn}>" class="btn btn-warning"><{$smarty.const._TAD_EDIT}></a>
                            </div>
                        </div>
                    </div>
                <{/if}>

                <{if $now_op=="tad_player_cate_form"}>
                    <style>
                        .csn_menu,.of_csn_menu,.m_csn_menu,.p_csn_menu{
                        width: 130px;
                        padding: 4px;
                        border: 1px solid gray;
                        font-size:12px;
                        }
                    </style>
                    <script>
                        $(document).ready(function(){
                        <{if $path_arr}>
                            <{foreach from=$path_arr key=i item=sn}>
                            make_option('of_csn_menu','<{$i}>','<{$sn.of_csn}>','<{$sn.def_csn}>');
                            <{/foreach}>
                        <{else}>
                            make_option('of_csn_menu',0,0,0);
                        <{/if}>
                        });

                        function make_option(menu_name , num , of_csn , def_csn){
                        $('#'+menu_name+num).show();
                        $.post('../ajax_menu.php',  {'of_csn': of_csn , 'def_csn': def_csn, 'chk_view': 0, 'chk_up': 0} , function(data) {
                            $('#'+menu_name+num).html("<option value=''>/</option>"+data);
                        });

                        $('.'+menu_name).change(function(){
                        var menu_id= $(this).prop('id');
                        var len=menu_id.length-1;
                        var next_num = Number(menu_id.charAt(len))+1
                            var next_menu = menu_name + next_num;
                            $.post('../ajax_menu.php',  {'of_csn': $('#'+menu_id).val(), 'chk_view': 0, 'chk_up': 0} , function(data) {
                            if(data==""){
                                $('#'+next_menu).hide();
                            }else{
                                $('#'+next_menu).show();
                                $('#'+next_menu).html("<option value=''>/</option>"+data);
                            }
                            });
                        });
                        }

                    </script>
                    <div class="card card-body bg-light m-1">
                        <form action="main.php" method="post" id="myForm" enctype="multipart/form-data" role="form">

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label text-sm-right">
                            <{$smarty.const._MA_TADPLAYER_OF_CSN}>
                            </label>
                            <div class="col-sm-10">
                            <select name="of_csn_menu[0]" id="of_csn_menu0" class="of_csn_menu"><option value=''></option></select>
                            <select name="of_csn_menu[1]" id="of_csn_menu1" class="of_csn_menu" style="display: none;"></select>
                            <select name="of_csn_menu[2]" id="of_csn_menu2" class="of_csn_menu" style="display: none;"></select>
                            <select name="of_csn_menu[3]" id="of_csn_menu3" class="of_csn_menu" style="display: none;"></select>
                            <select name="of_csn_menu[4]" id="of_csn_menu4" class="of_csn_menu" style="display: none;"></select>
                            <select name="of_csn_menu[5]" id="of_csn_menu5" class="of_csn_menu" style="display: none;"></select>
                            <select name="of_csn_menu[6]" id="of_csn_menu6" class="of_csn_menu" style="display: none;"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label text-sm-right">
                            <{$smarty.const._MA_TADPLAYER_TITLE}>
                            </label>
                            <div class="col-sm-10">
                            <input type="text" name="title" class="form-control " value="<{$title}>" placeholder="<{$smarty.const._MA_TADPLAYER_TITLE}>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label text-sm-right">
                            <{$smarty.const._MA_TADPLAYER_ENABLE_GROUP}>
                            </label>
                            <div class="col-sm-4">
                            <{$enable_group}>
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-right">
                            <{$smarty.const._MA_TADPLAYER_ENABLE_UPLOAD_GROUP}>
                            </label>
                            <div class="col-sm-4">
                            <{$enable_upload_group}>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12 text-center">
                            <input type="hidden" name="sort" value="<{$sort}>">
                            <input type="hidden" name="pcsn" value="<{$pcsn}>">
                            <input type="hidden" name="op" value="<{$op}>">
                            <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
                            </div>
                        </div>
                        </form>
                    </div>
                <{elseif $data}>
                    <script language="JavaScript">  
                        $(document).ready(function(){
                            $('#all_videos').sortable({ opacity: 0.6, cursor: 'move', update: function() {
                                var order = $(this).sortable('serialize');
                                $.post('save_sort.php?pcsn=<{$pcsn}>', order, function(theResponse){
                                    $('#save_msg').html(theResponse);
                                });
                            }
                            });
                            

                            var hh = $('div.thumb_height').width() * 0.75;
                            $('div.thumb_height').css('height',hh);

                            $("#clickAll").click(function() {
                                var x = document.getElementById("clickAll").checked;
                                if(x){
                                    $(".video").each(function() {
                                        $(this).prop("checked", true);
                                    });
                                }else{
                                    $(".video").each(function() {
                                        $(this).prop("checked", false);
                                    });
                                }
                            });

                        });


                        function check_one(id_name,change){
                            if(document.getElementById(id_name).checked && change){
                                document.getElementById(id_name).checked = false;
                            }else{
                                document.getElementById(id_name).checked = true;
                            }
                        }
                    </script>

                    <div class="row">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="clickAll"> <{$smarty.const._MA_TADPLAYER_SELECT_ALL}>
                        </label>

                        <{if $pcsn!=""}>
                            <a href="../index.php?pcsn=<{$pcsn}>" class="btn btn-sm btn-primary"><{$link_to_cate}></a>
                        <{/if}>
                        <span id="save_msg"></span>
                    </div>

                    <div class="row" id="all_videos">
                        <{foreach item=video from=$data}>
                            <div class="col-sm-2 d-flex align-items-stretch" id="psn_<{$video.psn}>">
                                <div class="card my-1" onClick="check_one('p_<{$video.psn}>',true);" onkeypress="check_one('p_<{$video.psn}>',true);">
                                    <img class="card-img-top" src="<{$video.pic}>" alt="<{$video.title}>">
                                    <div class="card-body">
                                        <div class="card-text">
                                            <div>
                                                <input type="checkbox" id="p_<{$video.psn}>" name="video[]" value="<{$video.psn}>" class="video" onClick="check_one('p_<{$video.psn}>',true);" onkeypress="check_one('p_<{$video.psn}>',true);">
                                                <{$video.uid_name}> / <{$video.post_date}>
                                            </div>
                                            <div>                                                
                                                <{$smarty.const._MA_TADPLAYER_COUNTER}>:<{$video.counter}>
                                            </div>
                                            <div style="height:40px;overflow:auto;"><{$video.g_txt}></div>
                                        </div>
                                        <div class="card-title" id="pt<{$video.psn}>"><{$video.title}></div>
                                    </div>
                                    <div class="card-footer">                                        
                                        <a href="../uploads.php?psn=<{$video.psn}>#fragment-1" target="_blank" class="btn btn-sm btn-warning"><{$smarty.const._TAD_EDIT}></a>
                                        <a href="../play.php?psn=<{$video.psn}>" target="_blank" class="btn btn-sm btn-info"><{$smarty.const._MA_TADPLAYER_VIEW}></a>
                                    </div>
                                </div>
                            </div>
                        <{/foreach}>
                    </div>
                <{/if}>
            </div>
        </div>
    </form>
</div>

