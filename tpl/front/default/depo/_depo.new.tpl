{strip}

<div class="block_form">
		{if $tpl_errors|count}
            <ul class="error_message">
            	{$_TRANS['Please fix the following errors']}:<br/>
                {if $tpl_errors['new'][0]=='psys_empty'}<li>{$_TRANS['Select pay.system']}</li>{/if}
                {if $tpl_errors['new'][0]=='psys_wrong'}<li>{$_TRANS['Wrong pay.system']}</li>{/if}
                {if $tpl_errors['new'][0]=='sum_wrong'}<li>{$_TRANS['Wrong amount']}</li>{/if}
                {if $tpl_errors['new'][0]=='sum_empty'}<li>{$_TRANS['Input amount']}</li>{/if}
				{if $tpl_errors['new'][0]=='plan_wrong'}<li>{$_TRANS['Wrong plan']}</li>{/if}
				{if $tpl_errors['new'][0]=='compnd_wrong'}<li>{$_TRANS['Wrong value']} ({$cmin}..{$cmax})</li>{/if}
				{if $tpl_errors['new'][0]=='compnd_wrong1'}<li>{$_TRANS['Wrong value for plan']} '{$cplan}'</li>{/if}
				{if $tpl_errors['new'][0]=='oper_disabled'}<li>{$_TRANS['Operation disabled']}</li>{/if}
				{if $tpl_errors['new'][0]=='low_bal1'}<li>{$_TRANS['Insufficient funds']}</li>{/if}
            </ul>
            
        {/if}

        <form method="post" name="new">
        	<input name="Login" id="new_Login" value="{$smarty.get.user}" type="hidden">
        	{if $from_admin}
	        	<div class="block_form_el cfix">
					<label for="">{$_TRANS['User']}</label>
					<div class="block_form_el_right">
						{$smarty.get.user}
					</div>
				</div>
			{/if}
			{if !$from_admin}
				<div class="block_form_el cfix">
					<label for="">{$_TRANS['Curr']}</label>
					<div class="block_form_el_right">
						<select class="select" name="Curr" id="add_Curr">
						  <option selected="" value="0">- {$_TRANS['select']} -</option>
						  {foreach $currs as $cid => $c}
				{if $c.Bal > 0}
							  <option value="{$cid}" {if $cid eq $smarty.post.Curr}selected{/if}>{$cid}</option>
				{/if}
						  {/foreach}
						</select>
					</div>
				</div>
			{/if}
			<div class="block_form_el cfix">
				<label for="">{$_TRANS['Amount']} <span class="descr_star">*</span></label>
				<div class="block_form_el_right">
					<input name="Sum" id="new_Sum" value="{$smarty.request.Sum}" type="text"> <i><span id="ccurr"></span></i>
				</div>
			</div>
			{if count($plans) > 1}
				<div class="block_form_el cfix">
					<label for="add_Plan">{$_TRANS['Plan']}</label>
					<div class="block_form_el_right">
						<select name="Plan" id="add_Plan">
							<option value="0">- {$_TRANS['auto']} -</option>
							{foreach from=$plans item=i key=k}
								<option value="{$k}">{$i}</option>
				            {/foreach}
						</select>
					</div>
				</div>
			{/if}
			{if $pcmax}
				<div class="block_form_el cfix">
					<label for="">{$_TRANS['Compaund']}</label>
					<div class="block_form_el_right">
						<input name="Compnd" id="new_Compnd" value="" type="text">
					</div>
				</div>
			{/if}

			{_getFormSecurity form='new'}
			<div class="block_form_el cfix">
	        	<label for="login_frm_Pass">&nbsp;</label>
	        	<div class="block_form_el_right">
	            	<input name="new_btn" value="{$_TRANS['Create']}" type="submit" class="button-green">
	            </div>
	        </div>
        </form>
    </div>

{*include file='edit.tpl'
	form='new'
	title_new=valueIf($from_admin, '?????????? ??????????????')
	fields=[
		'Login'=>$smarty.get.user,
		'User'=>
			[
				'X',
				'????????????????????????',
				0,
				$smarty.get.user,
				'skip'=>!$from_admin
			],
		'Bal'=>
			[
				'U',
				'balance/bal.tpl',
				'skip'=>!$from_admin and $_cfg.Const_IntCurr
			],
		'Sum'=>
			[
				'$',
				'??????????!!',
				[
					'sum_empty'=>'?????????????? ??????????',
					'sum_wrong'=>'???????????????? ??????????'
				],
				'comment'=>' <i><span id="ccurr"></span></i>'
			],
		'Plan'=>
			[
				'S',
				'????????',
				[
					'plan_wrong'=>'???????????????? ????????'
				],
				[0=>'- ???????? -'] + (array)$plans,
				'skip'=>(count($plans) <= 1)
			],
		'Compnd'=>
			[
				'%',
				'?????????????? ?????????????????? <<??????????????????????????>>',
				[
					'compnd_wrong'=>"???????????????? ???????????????? [$cmin..$cmax]",
					'compnd_wrong1'=>"???????????????? ???????????????? ?????? ?????????? '$cplan'"
				],
				'skip'=>!$pcmax
			]
	]
	values=$el
	errors=[
		'oper_disabled'=>'???????????????? ??????????????????',
		'low_bal1'=>'???????????????????????? ??????????????'
	]
*}

<script type="text/javascript">
	function updateCurr()
	{
		$('#ccurr').html('');
		$.ajax(
			{
				type: 'POST',
				url: 'ajax',
				data: 'module=balance&do=getcurr'+
					'&cid='+$('#new_PSys').val(),
				success:
					function(ex)
					{
						$('#ccurr').html(ex);
					}
			}
		);
	}
	$('#new_PSys').change(updateCurr);
	updateCurr();

    $(document).ready(function() {
             $("#new_Plan").val(0);
    });
</script>

{/strip}