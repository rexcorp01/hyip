<?php

$_auth = 90;
require_once('module/auth.php');

$table = 'FAQ';
$id_field = 'fID';
$out_link = moduleToLink('faq/admin/faqs');

try 
{

	if (sendedForm()) 
	{
		checkFormSecurity();
		
		$a = $_IN;
		$a['fCTS'] = timeToStamp();
        
        $a['fQuestion']=htmlspecialchars($a['fQuestion']);
        $a['fAnswer']=FilterHTML($a['fAnswer']);

		if (!$a['fQuestion'])
			setError('question_empty');
		if (!$a['fAnswer'])
			setError('answer_empty');
            
        $fields = explode(', ', 'fHidden, fCTS, fCat, fOrder, fQuestion, fAnswer');
                
        foreach ($fields as $i => $field) :
            
                if (!isset($a[$field])) {
                    unset($fields[$i]);
                }
            
        endforeach;          
   
		if ($id = $db->save($table, $a, implode(', ', $fields), $id_field))
			showInfo('Saved', $out_link . "?id=$id");
		showInfo('*Error');
	}

} 
catch (Exception $e) 
{
}

if (!isset($_GET['add']))
{
	if (_GETN('id'))
		$el = $db->fetch1Row($db->select($table, '*', "$id_field=?d", array(_GETN('id'))));
	if (!$el)
		goToURL(moduleToLink() . '?add');
	stampArrayToStr($el, 'fCTS', 0);
	setPage('el', $el, 2);
}

$cats = array();
foreach ((array)$_cfg['FAQ__Cats'] as $c)
	$cats[$c] = $c;
setPage('cats', $cats);

showPage();