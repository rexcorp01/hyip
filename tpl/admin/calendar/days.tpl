{strip}
{include file='header.tpl' title=$_AT['Days']}

{include file='calendar/_statuses.tpl'}

{include file='list_admin.tpl'
	title=$_AT['Days']
	url='*'
	fields=[
		'cID'=>[$_AT['ID']],
		'cTS'=>[$_AT['Date']],
		'cPerc'=>['%'],
		'cType'=>[$_AT['<small>Type</small>']|html_entity_decode]
	]
	values=$list
	row='*'
	btns=['del'=>$_AT['Delete']]
}

<a href="{_link module='calendar/admin/day'}?add" class="button-blue">{$_AT['Add day']}</a><br>

{include file='footer.tpl'}
{/strip}