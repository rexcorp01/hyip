{strip}
{include file='header.tpl' title=$_AT['Settings']}

{include file='edit_admin.tpl'
    values=$cfg
    fields=[
        'ShowCount'=>['I', $_AT['Row count in page']],
        'InBlock'=>['I', $_AT['Row count in block']]
    ]
}

{include file='footer.tpl'}
{/strip}