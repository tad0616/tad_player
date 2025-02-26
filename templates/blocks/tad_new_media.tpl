<{if $block|default:false}>
  <{foreach from=$block item=video}>
    <{if $video.mode}>
      <{include file="$xoops_rootpath/modules/tad_player/templates/blocks/sub_block_mode_`$video.mode`.tpl"}>
    <{else}>
      <{include file="$xoops_rootpath/modules/tad_player/templates/blocks/sub_block_mode_1.tpl"}>
    <{/if}>
  <{/foreach}>
  <div class="clearfix"></div>
<{/if}>