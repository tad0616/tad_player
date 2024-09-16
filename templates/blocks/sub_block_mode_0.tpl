<{if $video.title|default:false}>
    <div style="padding:5px 0px; border-bottom: 1px dotted #cfcfcf;">
        <a href="<{$xoops_url}>/modules/tad_player/play.php?psn=<{$video.psn}>"><{$video.title}></a> (<{$video.counter}>)
    </div>
<{/if}>