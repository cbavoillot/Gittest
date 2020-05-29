{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='Mon compte'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Detail budget'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Details budget'} {if $start1front}Pour la période du {$start1front} au {$end1front} {/if}</h1>

<p class="info-title">{l s=''}</p>
{if $slowValidation}
	<p class="alert alert-warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.'}</p>
{/if}
<div class="block-center" id="block-history">
	{if $orders && count($orders)}
		<table id="order-list" class="table table-bordered footab">
			<thead>
				<tr>
					<th class="first_item" data-sort-ignore="true">{l s='Order reference'}</th>
					<th class="item">{l s='Date'}</th>
					<th data-hide="phone" class="item">{l s='Total price HT'}</th>
					<th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Payment'}</th>
					<th class="item">{l s='Status'}</th>
					<th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Invoice'}</th>
					
				</tr>
			</thead>
			<tbody>
				{foreach from=$orders item=order name=myLoop}
					<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
						<td class="history_link bold">
							{if isset($order.invoice) && $order.invoice && isset($order.virtual) && $order.virtual}
								<img class="icon" src="{$img_dir}icon/download_product.gif"	alt="{l s='Products to download'}" title="{l s='Products to download'}" />
							{/if}
							
								{Order::getUniqReferenceOf($order.id_order)}
							
						</td>
						<td data-value="{$order.date_add|regex_replace:"/[\-\:\ ]/":""}" class="history_date bold">
							{dateFormat date=$order.date_add full=0}
						</td>
						<td class="history_price" data-value="{$order.total_paid_tax_excl}">
							<span class="price">
								{displayPrice price=$order.total_paid_tax_excl currency=$order.id_currency no_utf8=false convert=false}
							</span>
						</td>
						<td class="history_method">{$order.payment|escape:'html':'UTF-8'}</td>
						<td{if isset($order.order_state)} data-value="{$order.id_order_state}"{/if} class="history_state">
							{if isset($order.order_state)}
								<span class="label{if isset($order.order_state_color) && Tools::getBrightness($order.order_state_color) > 128} dark{/if}"{if isset($order.order_state_color) && $order.order_state_color} style="background-color:{$order.order_state_color|escape:'html':'UTF-8'}; border-color:{$order.order_state_color|escape:'html':'UTF-8'};"{/if}>
									{$order.order_state|escape:'html':'UTF-8'}
								</span>
							{/if}
						</td>
						<td class="history_invoice">
							{if (isset($order.invoice) && $order.invoice && isset($order.invoice_number) && $order.invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
								<a class="link-button" href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order.id_order}")|escape:'html':'UTF-8'}" title="{l s='Invoice'}" target="_blank">
									<i class="icon-file-text large"></i>{l s='PDF'}
								</a>
							{else}
								-
							{/if}
						</td>
					
					</tr>
				{/foreach}
				
				<thead>
				<tr>
				<th>{if $budget1}Votre budget : {$budget1} € HT {/if}</th>
				<th>Total dépensé:</th>
				<th>{if $depenses1 }{$depenses1|round:2}{/if} € HT</th>
				<th>Votre balance:</th>
				<th>{if $balance1 }{$balance1|round:2}{/if} € HT </th>
				<th></th>
				</tr>
				</thead>
			</tbody>
		</table>
		
		<div id="block-order-detail" class="unvisible">&nbsp;</div>
	{else}
		<p class="alert alert-warning">{l s='You have not placed any orders.'}</p>
	{/if}
</div>
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Retour sur mon compte'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{$base_dir}">
			<span><i class="icon-chevron-left"></i> {l s='Page accueil'}</span>
		</a>
	</li>
</ul>