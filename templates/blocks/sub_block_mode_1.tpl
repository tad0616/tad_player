<{if $video.title|default:false}>
    <div class="row" style="margin:5px 0px; border-bottom: 1px dotted #cfcfcf;">
        <div class="col-sm-4">
            <a href="<{$xoops_url}>/modules/tad_player/play.php?psn=<{$video.psn}>">
                <img src="<{$xoops_url}>/uploads/tad_player/img/s_<{$video.psn}>.png" style="width:100%;" alt="image of <{$video.title}>">
                <span class="sr-only visually-hidden"><{$video.title}></span>
            </a>
        </div>

        <div class="col-sm-8">
            <a href="<{$xoops_url}>/modules/tad_player/play.php?psn=<{$video.psn}>"><{$video.title}></a> (<{$video.counter}>)
        </div>
    </div>
<{/if}>