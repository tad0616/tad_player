<{foreach from=$block item=video}>
  <div class="row" style="margin:5px 0px; border-bottom: 1px dotted #cfcfcf;">
    <{if $video.image}>
    <div class="col-sm-3 text-right">
      <a href="<{$xoops_url}>/modules/tad_player/play.php?psn=<{$video.psn}>">
        <img src="<{$xoops_url}>/uploads/tad_player/img/s_<{$video.psn}>.png" class="img-responsive" alt="<{$video.title}>" style="width:60px;">
      </a>
    </div>
    <{/if}>
    <{if $video.image}>
    <div class="col-sm-9">
    <{else}>
    <div class="col-sm-12">
    <{/if}>
      <a href="<{$xoops_url}>/modules/tad_player/play.php?psn=<{$video.psn}>"><{$video.title}></a> (<{$video.post_date}>)
    </div>
  </div>
<{/foreach}>


