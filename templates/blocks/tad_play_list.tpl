<{if $block|default:false}>
    <{$block|default:''}>
<{else}>
    <div class="alert alert-danger">
        <{$smarty.const._MB_TADPLAYER_NO_PLAYLIST}>
    </div>
<{/if}>