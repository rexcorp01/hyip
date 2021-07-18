<?php

$_auth = 90;
require_once('module/auth.php');

$table = 'News';
$id_field = 'nID';
$out_link = moduleToLink('news/admin/newses');

try 
{

	if (sendedForm()) 
	{
		checkFormSecurity();
		
		$a = $_IN;
		strArrayToStamp($a, 'nTS', 0);
		strArrayToStamp($a, 'nDBegin', 1);
		strArrayToStamp($a, 'nDEnd', 2);
		if (!$a['nTS'])
			setError('date_empty');
		if (!$a['nTopic'])
			setError('topic_empty');
		if (!$a['nAnnounce'])
			setError('ann_empty');
		if (!$a['nText'])
			setError('text_empty');
/*            
        $a['nTopic']=htmlspecialchars($a['nTopic']);
        $a['nAnnounce']=htmlspecialchars($a['nAnnounce']);
        $a['nText']=FilterHTML($a['nText']);    
            
        $a['nTopic']=mysqli_real_escape_string($a['nTopic']);
        $a['nAnnounce']=mysqli_real_escape_string($a['nAnnounce']);
        $a['nText']=mysqli_real_escape_string($a['nText']);
        $a['nDBegin']=mysqli_real_escape_string($a['nDBegin']);
        $a['nDEnd']=mysqli_real_escape_string($a['nDEnd']);
*/
        $fields = explode(', ', 'nDBegin, nDEnd, nTS, nTopic, nAttn, nAnnounce, nText');
                
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
	stampArrayToStr($el, 'nTS', 0);
	stampArrayToStr($el, 'nDBegin, nDEnd', 1);
	setPage('el', $el, 2);
}
else
	setPage('today', timeToStr(time(), 0));

showPage();