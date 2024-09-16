<{$toolbar}>

<h2 class="sr-only visually-hidden">Video Upload</h2>

<script type="text/javascript">
  $(document).ready(function() {
    var $tabs = $("#tad_player_upload_tabs").tabs();
    var last_tab=$tabs.size()-1;
    <{if $show_to_batch_upload|default:false}>$tabs.tabs("select", last_tab);<{/if}>
  });
</script>

<div id="tad_player_upload_tabs">

  <ul>
    <li><a href="#fragment-1"><span><{$smarty.const._MD_TADPLAYER_UPLOAD}></span></a></li>
    <li><a href="batch_upload.php"><span><{$smarty.const._MD_TADPLAYER_BATCH_UPLOAD}></span></a></li>
  </ul>

  <div id="fragment-1">
    <script type="text/javascript">
      $(document).ready(function() {
        <{$hide}>
        <{$hide_img}>

        $("#flv_where").change(function() {
          if ($("#flv_where").val()=="youtube") {
            $("#flv_youtube").slideDown();
            $("#flv_local").slideUp();
            $("#flv_link").slideUp();
            $("#thumb_config").hide();
          } else if ($("#flv_where").val()=="link"){
            $("#flv_link").slideDown();
            $("#flv_local").slideUp();
            $("#flv_youtube").slideUp();
            $("#thumb_config").show();
          }else{
            $("#flv_local").fadeIn();
            $("#flv_link").slideUp();
            $("#flv_youtube").slideUp();
            $("#thumb_config").show();
          }
        });


        $("#img_where").change(function() {
          if ($("#img_where").val()=="link"){
            $("#img_link").slideDown();
            $("#img_local").hide();
          }else{
            $("#img_local").slideDown();
            $("#img_link").hide();
          }
        });


        $('#youtube').change(function() {
          $('#title').val($('#youtube').val());
          $.post("link_ajax.php", { url: $('#youtube').val()},
           function(data) {
            var obj = $.parseJSON(data);
              $('#title').val(obj.title);
              $('#creator').val(obj.author);
              CKEDITOR.instances.editor_content.setData(obj.description);
           });
        });


        $('#LinkGet').click(function() {
          $.post("link_ajax.php", { url: $('#youtube').val()},
           function(data) {
            var obj = $.parseJSON(data);
              $('#title').val(obj.title);
              $('#creator').val(obj.author);
              CKEDITOR.instances.editor_content.setData(obj.description);
           });
        });

      });
    </script>

    <form action="uploads.php" method="post" id="myForm" enctype="multipart/form-data" class="form-horizontal" role="form">

      <div class="alert alert-success">
        <div class="form-group row mb-3">
          <div class="col-sm-4">
            <select id="flv_where" class="form-control" title="select type">
              <option value="youtube" <{$selected_youtube}>><{$smarty.const._MD_TADPLAYER_YOUTUBE_FLV}></option>
              <option value="local" <{$selected_local}>><{$smarty.const._MD_TADPLAYER_UPLOAD_FLV}></option>
              <option value="link" <{$selected_link}>><{$smarty.const._MD_TADPLAYER_LINK_FLV}></option>
            </select>
          </div>
          <div class="col-sm-8">
            <div id="flv_local">
              <input type="file" name="location" id="location" class="form-control" value="<{$location}>">
            </div>

            <div id="flv_link">
              <input type="text" name="location" id="location" class="form-control" value="<{$location}>" placeholder="<{$smarty.const._MD_TADPLAYER_FLV_LINK}>">
            </div>

            <div id="flv_youtube">
              <div class="row">
                <div class="col-sm-9">
                  <input type="text" name="youtube" id="youtube" class="form-control" value="<{$youtube}>" placeholder="<{$smarty.const._MD_TADPLAYER_YOUTUBE_LINK}>">
                </div>
                <div class="col-sm-3">
                  <button type="button" class="btn btn-info" id="LinkGet"><{$smarty.const._MD_TADPLAYER_AUTO_GET}></button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group row mb-3" id="thumb_config">
          <div class="col-sm-4">
            <select id="img_where" class="form-control" title="select method">
              <option value="local" <{$selected_img_local}>><{$smarty.const._MD_TADPLAYER_UPLOAD_IMG}></option>
              <option value="link" <{$selected_img_link}>><{$smarty.const._MD_TADPLAYER_LINK_IMG}></option>
            </select>
          </div>
          <div class="col-sm-8">
            <div id="img_local">
              <input type="file" name="image" class="form-control" value="<{$image}>">
            </div>
            <div id="img_link">
              <input type="text" name="image" class="form-control" value="<{$image}>" placeholder="<{$smarty.const._MD_TADPLAYER_IMG_LINK}>">
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row mb-3">
        <label class="col-sm-1 col-form-label text-sm-right control-label">
          <{$smarty.const._MD_TADPLAYER_OF_CSN}>
        </label>
        <div class="col-sm-3">
          <select name="pcsn" size=1 class="form-control" title="select category">
            <{$cate_select}>
          </select>
        </div>
        <div class="col-sm-8">
          <input type="text" name="new_pcsn" class="form-control" placeholder="<{$smarty.const._MD_TADPLAYER_NEW_PCSN}>">
        </div>
      </div>



      <div class="form-group row mb-3">
        <label class="col-sm-1 col-form-label text-sm-right control-label">
          <{$smarty.const._MD_TADPLAYER_TITLE}>
        </label>
        <div class="col-sm-7">
          <input type="text" name="title" id="title" class="form-control" value="<{$title}>" placeholder="<{$smarty.const._MD_TADPLAYER_TITLE}>">
        </div>
        <div class="col-sm-4">
          <input type="text" name="creator" id="creator" class="form-control" value="<{$creator}>" placeholder="<{$smarty.const._MD_TADPLAYER_CREATOR}>">
        </div>
      </div>


      <div class="form-group row mb-3">
        <div class="col-sm-12">
          <{$editor}>
        </div>
      </div>


      <div class="form-group row mb-3">
        <label class="col-sm-2 col-form-label text-sm-right control-label">
          <{$smarty.const._MD_TADPLAYER_ENABLE_GROUP}>
        </label>

        <div class="col-sm-10">
          <{foreach from=$group_arr key=group_id item=group_name}>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="enable_group[]" id="enable_group<{$group_id}>" value="<{$group_id}>" <{if $group_id|in_array:$enable_group}>checked<{/if}>>
              <label class="form-check-label" for="enable_group<{$group_id}>"><{$group_name}></label>
            </div>
          <{/foreach}>
        </div>
      </div>

      <div class="alert alert-info">
        <{$smarty.const._MD_TADPLAYER_ENABLE_GROUP_DESC}>
      </div>

      <div class="text-center">
        <input type="hidden" name="op" value="<{$next_op}>">
        <input type="hidden" name="psn" value="<{$psn}>">
        <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
      </div>

    </form>
  </div>
</div>