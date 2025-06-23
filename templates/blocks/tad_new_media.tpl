<{if $block|default:false}>
  <{foreach from=$block item=video}>
    <{if $video.mode}>
      <{include file="$xoops_rootpath/modules/tad_player/templates/blocks/sub_block_mode_`$video.mode`.tpl"}>
    <{else}>
      <{include file="$xoops_rootpath/modules/tad_player/templates/blocks/sub_block_mode_1.tpl"}>
    <{/if}>
  <{/foreach}>
  <div class="clearfix"></div>
  <div class="text-right text-end">
    <a href="<{$xoops_url}>/modules/tad_player/index.php" class="badge text-white bg-info" >more...</a>
  </div>
<{/if}>