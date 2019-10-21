<{foreach from=$block item=video}>
  <{includeq file="$xoops_rootpath/modules/tad_player/templates/blocks/sub_block_mode_`$video.mode`.tpl"}>
<{/foreach}>
<div class="clearfix"></div>