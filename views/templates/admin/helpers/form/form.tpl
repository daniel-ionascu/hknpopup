{*
* 2006-2021 THECON SRL
*
* NOTICE OF LICENSE
*
* DISCLAIMER
*
* YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
* USED BY THIS MODULE.
*
* @author    THECON SRL <contact@thecon.ro>
* @copyright 2006-2021 THECON SRL
* @license   Commercial
*}

{extends file="helpers/form/form.tpl"}
{block name="input_row"}
    {if $input.type == 'checkbox_table'}
        {assign var=all_setings value=$input.values}
        {assign var=id value=$all_setings['id']}
        {assign var=name value=$all_setings['name']}
        {if isset($all_setings) && count($all_setings) > 0}
            <div class="form-group {$input.class_block|escape:'htmlall':'UTF-8'} custom-list"  {if $input.display}style="display: block" {/if}>
                <label class="control-label col-lg-3">
        <span class="{if $input.hint}label-tooltip{else}control-label{/if}" data-toggle="tooltip" data-html="true" title="" data-original-title="{$input.hint|escape:'htmlall':'UTF-8'}">
          {$input.label|escape:'htmlall':'UTF-8'}
        </span>
                </label>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        <a href="#" class="btn btn-default show_checked"><i class="icon-check-sign"></i> {l s='Show Checked'  mod='thfsales'}</a>
                                    </th>
                                    {if $input.search}
                                        <th>
                                            <a href="#" class="btn btn-default show_all"><i class="icon-check-empty"></i> {l s='Show All'  mod='thfsales'}</a>
                                        </th>
                                    {/if}
                                    <th>
                    <span class="title_box">
                      {if $input.search}
                          <input type="text" class="search_checkbox_table" placeholder="{l s='search...'  mod='thfsales'}">
                      {/if}
                    </span>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                            <table class="table table-bordered">
                                <tbody>
                                {foreach $all_setings['query'] as $key => $setings}
                                    <tr>
                                        <td class="text-center fixed-width-xs">

                                            <input type="checkbox" class="{$input.type|escape:'htmlall':'UTF-8'} {$input.class_input|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}_{$setings[$id]|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}_{$setings[$id]|escape:'htmlall':'UTF-8'}" value="{$setings[$id]|escape:'htmlall':'UTF-8'}" {if $all_setings['value'] && is_array($all_setings['value'])}{if in_array($setings[$id], $all_setings['value'])}checked="checked" {/if}{/if} />
                                        </td>
                                        <td class="text-center fixed-width-xs">{$setings[$id]|escape:'htmlall':'UTF-8'}</td>
                                        <td>
                                            <label for="{$input.name|escape:'htmlall':'UTF-8'}_{$setings[$id]|escape:'htmlall':'UTF-8'}">
                                                {$setings[$name]|escape:'htmlall':'UTF-8'}
                                                {if isset($setings['reference']) && $setings['reference']}
                                                    ({$setings['reference']|escape:'htmlall':'UTF-8'})
                                                {/if}

                                                {if isset($all_setings['name2']) && $all_setings['name2']}
                                                    {$setings[$all_setings['name2']]|escape:'htmlall':'UTF-8'}
                                                {/if}
                                            </label>
                                        </td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {elseif $input.type == 'html_title'}
        {if isset($input.html_content)}
            {$input.html_content}
        {else}
            <div class="form-group">
                <label class="control-label col-lg-3">
                </label>
                <div class="col-lg-4">
                    <div class="custom-html-title" style="position:relative; left: -50%; width: 150%; height: 30px; background-color: #363A41; margin-top: 13px; color: #fff; font-size: 16px; line-height: 30px; padding-left: 20px;">{$input.name|escape:'htmlall':'UTF-8'}</div>
                </div>
            </div>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
