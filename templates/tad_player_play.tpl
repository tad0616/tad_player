<{$toolbar}>

<script type="text/javascript">
  $(document).ready(function(){
    $.post("ajax.php",  {op: "get_menu" , pcsn: $("#menu1").val() , psn: '<{$psn}>'} , function(data) {
      $("#menu2").html(data);
    });
    $("#menu1").change(function(){
      $.post("ajax.php",  {op: "get_menu" , pcsn: $("#menu1").val() , psn: '<{$psn}>'} , function(data) {
        $("#menu2").html(data);
      });
    });
  });

  function delete_tad_player_file_func(psn){
    var sure = window.confirm("<{$smarty.const._TAD_DEL_CONFIRM}>");
    if (!sure)  return;
    location.href="play.php?op=delete_tad_player_file&pcsn=<{$pcsn}>&psn=" + psn;
  }
</script>

<div class="row" style="margin-bottom:2em;">
  <div class="col-sm-4">
    <select id="menu1" class="form-control" title="select category">
      <{$cate_select}>
    </select>
  </div>
  <div class="col-sm-8">
    <select id="menu2" class="form-control" onChange="location.href='play.php?psn='+this.value" title="select sub-category">
    </select>
  </div>
</div>


<{if $title}>
  <h1><{$title}></h1>
<{else}>
  <h2 class="sr-only visually-hidden">Play Video</h2>
<{/if}>


<{if $content}>
  <div class="alert alert-info">
    <{$content}>
  </div>
<{/if}>

<div class="row">
  <div class="col-sm-12">
    <{$media}>
  </div>
</div>


<div class="row" style="margin: 20px auto;">
  <div class="col-sm-7">
    <{$push}>
  </div>

  <div class="col-sm-2">
    <{$star_rating}>
  </div>

  <div class="col-sm-3 text-right text-end">
    <{if $isAdmin or $isUploader}>
      <a href="javascript:delete_tad_player_file_func(<{$psn}>);" class="btn btn-sm btn-danger"><{$smarty.const._TAD_DEL}></a>

      <a href="<{$xoops_url}>/modules/tad_player/uploads.php?psn=<{$psn}>#fragment-1" class="btn btn-sm btn-warning"><{$smarty.const._TAD_EDIT}></a>
    <{/if}>
  </div>

</div>


<{if $code}>
  <div class="alert alert-info"><{$code}></div>
<{/if}>
