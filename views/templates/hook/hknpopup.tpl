{if $popup_type eq 1}
    <div class="hkn_popup_container">
        <div class="hkn_popup_box" data-id="{$id_popup}">
            <div class="hkn_popup">
                <div class="hkn_popup_image">
                    <a href="{if $link != ''}{$link}{else}#{/if}" target="_blank">
                        {$content nofilter}
                    </a>
                </div>
                <a class="hkn_popup_close" href="#">X</a>
            </div>
        </div>
    </div>
{elseif $popup_type eq 2}
    <div class="hkn_popup_required hidden">
        <div class="hkn_popup_container">
            <span>{$heading nofilter}</span>
            <span>{$content nofilter}</span>
            <span>{$notice nofilter}</span>
            <button class="btn btn-primary hkn_accept">{$button}</button>
        </div>
    </div>
{else}

{/if}

