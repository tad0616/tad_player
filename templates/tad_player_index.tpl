<{$toolbar}>
<h2 class="sr-only visually-hidden">Video List</h2>
<script type="text/javascript">
  $(document).ready(function(){
    $.post("ajax.php",  {op: "get_menu" , pcsn: $("#menu1").val() , psn: '<{$psn}>'} , function(data) {
      if(data){
        $("#menu2").show();
        $("#menu2").html(data);
      }else{
        $("#menu2").hide();
      }
    });

    $("#menu1").change(function(){
      location.href="index.php?pcsn=" +　$("#menu1").val();
    });

    var hh = $('div.thumb_height').width() * 0.75;
    $('div.thumb_height').css('height',hh);
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
    <select id="menu2" style="display: none;" class="form-control" onChange="location.href='play.php?psn='+this.value" title="select sub-category">
    </select>
  </div>
</div>

<{$rating_js}>

<{if $sub_cate}>
  <div class="row">
    <{foreach from=$sub_cate item=cate}>
      <div class="col-sm-3 d-flex align-items-stretch">
        <div class="card" style="width:100%; magrin-bottom:1em;">
          <a href="index.php?pcsn=<{$cate.pcsn}>">
            <img class="card-img-top" src="<{$cate.pic}>" alt="<{$cate.title}> pic" style="width:100%"><span class="sr-only visually-hidden"><{$cate.title}></span>
          </a>
          <div class="card-body">
            <h5 class="card-title text-center"><{$cate.title}></h5>
            <p class="card-text">
              <div style="font-size: 0.8125em; color:#666666; text-align:center;"><{$cate.num}></div>
              <{if $cate.pcsn_num}><div style="font-size: 0.8125em;color:#666666;text-align:center;"><{$cate.pcsn_num}></div><{/if}>
            </p>
          </div>
        </div>
      </div>
    <{/foreach}>
  </div>
<{/if}>

<{if $content}>
  <div class="row">
    <{foreach item=video from=$content}>
      <div class="col-sm-3 d-flex align-items-stretch">
          <div class="card" style="width:100%; magrin-bottom:1em;">
            <a href="<{$video.url}>">
              <img class="card-img-top" src="<{$video.pic}>" alt="<{$video.img_title}> pic" style="width:100%"><span class="sr-only visually-hidden"><{$video.img_title}></span>
            </a>
            <div class="card-body">
              <p class="card-text text-center">
                <{if $video.post_date}>
                  <div style="font-size: 0.75em; color: #666666;">
                    <span class="badge badge-info pull-right"><{$video.counter}></span>
                    <{$video.post_date}>
                  </div>
                <{/if}>
                <{if $rating_js}><div id="rating_psn_<{$video.psn}>"></div><{/if}>
              </p>
              <p class="card-title"><a href="<{$video.url}>"><{$video.img_title}></a></p>
            </div>
          </div>

      </div>
    <{/foreach}>
  </div>
<{/if}>


<div class="text-center" style="margin: 20px auto;">
  <{$bar}>
</div>


<div class="text-center" style="margin: 20px auto;">
  <{$push}>
</div>
