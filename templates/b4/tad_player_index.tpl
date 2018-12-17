<{$toolbar}>

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

<div class="alert alert-success">
  <div class="row">
    <div class="col-sm-4">
      <select id="menu1" class="form-control">
        <{$cate_select}>
      </select>
    </div>
    <div class="col-sm-8">
      <select id="menu2" style="display: none;" class="form-control" onChange="location.href='play.php?psn='+this.value">
      </select>
    </div>
  </div>
</div>


<{$rating_js}>

<{assign var="i" value=0}>
<{assign var="total" value=0}>


<{if $sub_cate}>
  <{foreach item=sub_cate from=$sub_cate}>
    <{if $i==0}>
    <div class="row">
    <{/if}>

    <div class="col-sm-3 d-flex align-items-stretch">

      <div class="card my-1">
        <a href="index.php?pcsn=<{$sub_cate.pcsn}>">
          <img class="card-img-top" src="<{$sub_cate.pic}>" alt="<{$sub_cate.title}>">
        </a>
        <div class="card-body">
          <h5 class="card-title"><{$sub_cate.title}></h5>
          <p class="card-text">
            <div style="font-size:13px; color:#666666; text-align:center;"><{$sub_cate.num}></div>
            <{if $sub_cate.pcsn_num}><div style="font-size:13px;color:#666666;text-align:center;"><{$sub_cate.pcsn_num}></div><{/if}>
          </p>
        </div>
      </div>
        

    </div>
    <{assign var="i" value=$i+1}>
    <{assign var="total" value=$total+1}>
    <{if $i == 4 || $total==$count}>
        </div>
    <{/if}>
    <{if $i == 4}><{assign var="i" value=0}><{/if}>
  <{/foreach}>
<{/if}>

<{if $content}>
<div class="row">
  <{foreach item=video from=$content}>

    <div class="col-sm-3 d-flex align-items-stretch">      
        <div class="card my-1">
          <a href="<{$video.url}>">
            <img class="card-img-top" src="<{$video.pic}>" alt="<{$video.img_title}>">
          </a>
          <div class="card-body">
            <p class="card-text">
              <{if $video.post_date}>
              <div style="font-size: 12px; color: #666666;">
                <span class="badge badge-info pull-right"><{$video.counter}></span>
                <{$video.post_date}>
              </div>
              <{/if}>
              <{if $rating_js}><div id="rating_psn_<{$video.psn}>"></div><{/if}>
            </p>
            <p class="card-title"><{$video.img_title}></p>
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