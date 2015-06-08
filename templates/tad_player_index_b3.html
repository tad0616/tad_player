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
    <div class="col-md-4">
      <select id="menu1" class="form-control">
        <{$cate_select}>
      </select>
    </div>
    <div class="col-md-8">
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
      <div class="thumbnails">
    <{/if}>

    <div class="col-md-3">
      <div class="thumbnail" style="margin: 20px 0px; background-color: #fcfcfc;">
        <div class="thumb_height" style="background-color:black;position:relative;height:120px;overflow:hidden;">
          <a href="index.php?pcsn=<{$sub_cate.pcsn}>">
            <img src="<{$sub_cate.pic}>" alt="<{$sub_cate.title}>" style="z-index:1; width :100%;">
            <div style="font-size: 1em; font-weight: bold; color: <{$font_color}>; position: absolute; bottom: 2px; left: 10px; z-index: 2; text-shadow: 1px 1px 0 <{$border_color}>, -1px -1px 0 <{$border_color}>, 1px -1px 0 <{$border_color}>, -1px 1px 0 <{$border_color}>, 0px -1px 0 <{$border_color}>, 0px 1px 0 <{$border_color}>, -1px 0px 0 <{$border_color}>, 1px 0px 0 <{$border_color}>;"><{$sub_cate.title}></div>
          </a>
        </div>
        <div class="caption">
          <div style="font-size:13px; color:#666666; text-align:center;"><{$sub_cate.num}></div>
          <{if $sub_cate.pcsn_num}><div style="font-size:13px;color:#666666;text-align:center;"><{$sub_cate.pcsn_num}></div><{/if}>
        </div>
      </div>
    </div>
    <{assign var="i" value=$i+1}>
    <{assign var="total" value=$total+1}>
    <{if $i == 4 || $total==$count}>
        </div>
      </div>
    <{/if}>
    <{if $i == 4}><{assign var="i" value=0}><{/if}>
  <{/foreach}>
<{/if}>

<{if $content}>
  <{foreach item=video from=$content}>
    <{if $i==0}>
    <div class="row">
      <div class="thumbnails">
    <{/if}>

    <div class="col-md-3">
      <div class="thumbnail" style="margin: 20px 0px;">
        <{if $video.url}><a href="<{$video.url}>" alt="<{$video.info}>" title="<{$video.info}>" style="color:white;"><{/if}>
          <div class="thumb_height" style="background-color:black;position:relative;height:120px;overflow:hidden;">
          <img src="<{$video.pic}>" alt="<{$video.img_title}>" style="z-index:1; width :100%;">
          <{if $video.img_title}>
            <div style="color: <{$font_color}>; font-size: 12px; position: absolute; bottom: 2px; left: 10px; z-index: 2; text-shadow: 1px 1px 0 <{$border_color}>, -1px -1px 0 <{$border_color}>, 1px -1px 0 <{$border_color}>, -1px 1px 0 <{$border_color}>, 0px -1px 0 <{$border_color}>, 0px 1px 0 <{$border_color}>, -1px 0px 0 <{$border_color}>, 1px 0px 0 <{$border_color}>;"><{$video.img_title}></div>
          <{/if}>
          </div>
        <{if $video.url}></a><{/if}>
        <div class="caption">
          <{if $video.post_date}><div style="font-size: 11px; color: #666666;"><span class="badge badge-info pull-right"><{$video.counter}></span><{$video.post_date}></div><{/if}>
          <{if $rating_js}><div id="rating_psn_<{$video.psn}>"></div><{/if}>

          <div>
            <{if $video.url}><a href="<{$video.url}>" alt="<{$video.info}>" title="<{$video.info}>"><{/if}>

            <{if $video.url}></a><{/if}>
          </div>
        </div>
      </div>
    </div>

    <{assign var="i" value=$i+1}>
    <{assign var="total" value=$total+1}>
    <{if $i == 4 || $total==$count}>
        </div>
      </div>
    <{/if}>
    <{if $i == 4}><{assign var="i" value=0}><{/if}>
  <{/foreach}>
<{/if}>


<div class="text-center" style="margin: 20px auto;">
  <{$bar}>
</div>


<div class="text-center" style="margin: 20px auto;">
  <{$push}>
</div>