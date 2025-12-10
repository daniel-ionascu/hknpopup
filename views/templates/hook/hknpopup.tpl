{**
 * HKN Popup Manager - Frontend Template
 * Version 3.0.0
 *
 * @author    Daniel Ionascu <danielionascudev@gmail.com>
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}

{if isset($popups) && $popups}
    {foreach from=$popups item=popup}
        <div class="hkn-popup hkn-position-{$popup.placement|escape:'htmlall':'UTF-8'}"
             data-popup-id="{$popup.id|intval}"
             data-trigger-type="{$popup.trigger_type|escape:'htmlall':'UTF-8'}"
             data-trigger-value="{$popup.trigger_value|intval}"
             data-animation="{$popup.animation|escape:'htmlall':'UTF-8'}"
             data-show-close-btn="{if $popup.show_close_btn}1{else}0{/if}"
             data-close-on-overlay="{if $popup.close_on_overlay}1{else}0{/if}"
             data-auto-close="{$popup.auto_close|intval}"
             data-cookie-days="{$popup.cookie_days|intval}"
             data-show-once-session="{if $popup.show_once_session}1{else}0{/if}"
             {if $popup.url}data-url="{$popup.url|escape:'htmlall':'UTF-8'}"{/if}>

            {* Overlay *}
            <div class="hkn-popup-overlay"></div>

            {* Popup container *}
            <div class="hkn-popup-container">
                {* Close button *}
                {if $popup.show_close_btn}
                    <button class="hkn-popup-close" type="button" aria-label="{l s='Close' mod='hknpopup'}">&times;</button>
                {/if}

                {* Content based on popup type *}
                <div class="hkn-popup-content">
                    {if $popup.type == 'images_only'}
                        {* Images only popup *}
                        {if $popup.image_desktop}
                            <img src="{$module_path}uploads/{$popup.image_desktop|escape:'htmlall':'UTF-8'}"
                                 alt=""
                                 class="hkn-popup-image hkn-popup-image-desktop"
                                 loading="lazy">
                        {/if}
                        {if $popup.image_mobile}
                            <img src="{$module_path}uploads/{$popup.image_mobile|escape:'htmlall':'UTF-8'}"
                                 alt=""
                                 class="hkn-popup-image hkn-popup-image-mobile"
                                 loading="lazy">
                        {elseif $popup.image_desktop}
                            {* Fallback to desktop image on mobile if no mobile image *}
                            <img src="{$module_path}uploads/{$popup.image_desktop|escape:'htmlall':'UTF-8'}"
                                 alt=""
                                 class="hkn-popup-image hkn-popup-image-mobile"
                                 loading="lazy">
                        {/if}

                    {elseif $popup.type == 'html'}
                        {* HTML content popup *}
                        <div class="hkn-popup-html">
                            {$popup.content nofilter}
                        </div>

                    {elseif $popup.type == 'overlay'}
                        {* Overlay only (no content box) *}

                    {else}
                        {* Classic popup: image + HTML content *}
                        {if $popup.image_desktop || $popup.image_mobile}
                            <div class="hkn-popup-image-wrapper">
                                {if $popup.image_desktop}
                                    <img src="{$module_path}uploads/{$popup.image_desktop|escape:'htmlall':'UTF-8'}"
                                         alt=""
                                         class="hkn-popup-image hkn-popup-image-desktop"
                                         loading="lazy">
                                {/if}
                                {if $popup.image_mobile}
                                    <img src="{$module_path}uploads/{$popup.image_mobile|escape:'htmlall':'UTF-8'}"
                                         alt=""
                                         class="hkn-popup-image hkn-popup-image-mobile"
                                         loading="lazy">
                                {elseif $popup.image_desktop}
                                    <img src="{$module_path}uploads/{$popup.image_desktop|escape:'htmlall':'UTF-8'}"
                                         alt=""
                                         class="hkn-popup-image hkn-popup-image-mobile"
                                         loading="lazy">
                                {/if}
                            </div>
                        {/if}
                        {if $popup.content}
                            <div class="hkn-popup-html">
                                {$popup.content nofilter}
                            </div>
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
{/if}
