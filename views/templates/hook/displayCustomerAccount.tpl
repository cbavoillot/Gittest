
<li>
	<a style="text-decoration: none;">
		<i class="icon-credit-card"></i>
		<span><p style="padding: 5px;"><strong>Suivi budgetaire {if $budget1} {$budget1|escape:'htmlall':'UTF-8'} HT €  {/if}</strong></p>
		<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo1">Actualiser mon budget</button>
		<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo2">Voir ma balance</button>
		<div id="demo1" class="collapse"   style="height: auto;text-transform: none;font-size: 14px;" >
		{l s='Votre budget HT' mod='suivibudget'} :<br>
			<form method="post" style="display:inline;">
				<input name="budget1" type="text" value="{if isset($smarty.post.budget1)}{$smarty.post.budget1}{/if}" /><br>
				
				Du <br><input  type="text" class="datepicker" id="hasDatepicker1" name="start" value="{if isset($smarty.post.start)}{$smarty.post.start}{/if}"/><br>
				Au <br><input  type="text" class="datepicker" id="hasDatepicker2" name="end" value="{if isset($smarty.post.end)}{$smarty.post.end}{/if}"/><br>
				
				<input value="{l s='Enregistrer' mod='suivibudget'}" type="submit" style="margin: 5px;"/>
			</form>
		</span>
		</div>
		<div id="demo2" class="collapse"   style="height: auto;text-transform: none;font-size: 14px;" >
		<p>Mon budget : {if $budget1} {$budget1|escape:'htmlall':'UTF-8'} HT € {/if} </p>
		<p>Vos dépenses : {if $depenses1} {$depenses1|escape:'htmlall':'UTF-8'} HT € {else} Pas de dépenses {/if} </p>
		<p>Balance :{if $balance1} {$balance1|escape:'htmlall':'UTF-8'} HT € {/if} </p>
		<p>Periode :{if $start1 && $end1 } Du {$start1|escape:'htmlall':'UTF-8'} <br>au {$end1|escape:'htmlall':'UTF-8'} {/if} </p>
		<p><a class="btn btn-info"  href="/module/suivibudget/detailbudget">Voir le detail</a> </p>
		</div>
		
	</a>
</li>
<script type="text/javascript">
{literal}
$(function(){
	$('#hasDatepicker1').datepicker({ 
		dateFormat: "dd/mm/yy", 
		duration: 'normal',
		
		}
	);
	
	$('#hasDatepicker2').datepicker({ 
		dateFormat: "dd/mm/yy", 
		duration: 'normal',
		
		}
	);
	
	
});
{/literal}
</script>
<!-- / MODULE budget by christophe -->