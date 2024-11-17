<script type="text/javascript">
  $(document).ready(function(){
    $.post("ajax.php",  {op: "get_menu" , pcsn: $("#menu1").val() , psn: '<{$psn|default:''}>'} , function(data) {
      $("#menu2").html(data);
    });
    $("#menu1").change(function(){
      $.post("ajax.php",  {op: "get_menu" , pcsn: $("#menu1").val() , psn: '<{$psn|default:''}>'} , function(data) {
        $("#menu2").html(data);
      });
    });
  });

</script>

<div class="row" style="margin-bottom:2em;">
  <div class="col-sm-4">
    <select id="menu1" class="form-select" title="select category">
      <{$cate_select|default:''}>
    </select>
  </div>
  <div class="col-sm-8">
    <select id="menu2" class="form-select" onChange="location.href='play.php?psn='+this.value" title="select sub-category">
    </select>
  </div>
</div>


<{if $title|default:false}>
  <h1><{$title|default:''}></h1>
<{else}>
  <h2 class="sr-only visually-hidden">Play Video</h2>
<{/if}>


<{if $content|default:false}>
  <div class="alert alert-info">
    <{$content|default:''}>
  </div>
<{/if}>

<div class="row">
  <div class="col-sm-12">
    <{$media|default:''}>
  </div>
</div>


<div class="row" style="margin: 20px auto;">
  <div class="col-sm-7">
    <{$push|default:''}>
  </div>

  <div class="col-sm-2">
    <{$star_rating|default:''}>
  </div>

  <div class="col-sm-3 text-right text-end">
    <{if $tad_player_adm or $isUploader}>
      <a href="javascript:delete_tad_player_file_func(<{$psn|default:''}>);" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> <{$smarty.const._TAD_DEL}></a>

      <a href="<{$xoops_url}>/modules/tad_player/uploads.php?psn=<{$psn|default:''}>#fragment-1" class="btn btn-sm btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>  <{$smarty.const._TAD_EDIT}></a>
    <{/if}>
  </div>

</div>


<{if $code|default:false}>
  <div class="alert alert-info"><{$code|default:''}></div>
<{/if}>
