<?php

$_auth = 1;
require_once('module/auth.php');

try 
{

	if (sendedForm()) 
	{
		checkFormSecurity();
		
		if ($_GS['demo'] and ($_user['uLevel'] < 99) and (_uid() <= 3))
			showInfo('*Denied');
			
		$a = $_IN;
		if (($_cfg['Sec_MinPIN'] > 0) and (md5($a['PIN'] . $_cfg['Const_Salt']) != $_user['uPIN']))
			setError('pin_wrong');
		if (($_cfg['Account_UseName'] > 0) and !$a['aName'])
			setError('name_empty');
		if ($_cfg['SMS_REG'])
		{
			$a['aTel'] = preg_replace('|[^\d]|', '', $a['aTel']);
			if (textLen($a['aTel']) < 11)
				setError('tel_wrong');
		}
		list($h, $m) = explode(':', $a['TZ'], 2);
		if ((abs($h) > 12) or ($m < 0) or ($m >= 60))
			setError('tz_wrong');
		$a['aTZ'] = $h * 60 + $m;
		$a['aTimeOut'] = abs($a['aTimeOut']);
		if ($_cfg['Sec_MinSQA'] > 0)
		{
			if (!$a['aSQuestion'])
				setError('secq_empty');
			if (strlen($a['aSQuestion']) < $_cfg['Sec_MinSQA'])
				setError('secq_short');
			$f = 'aSQuestion, ';
			if (!sEmpty($a['aSAnswer']))
			{
				if (strlen($a['aSAnswer']) < $_cfg['Sec_MinSQA'])
					setError('seca_short');
				if ($a['aSAnswer'] == $a['aSQuestion'])
					setError('seqa_equal_secq');
				$a['aSAnswer'] = md5($a['aSAnswer'] . $_cfg['Const_Salt']);
				$f .= 'aSAnswer, ';
			}
		}
		else
			$f = '';
		if ($_cfg['SMS_REG'])
			$f .= 'aTel, ';
		strArrayToStamp($a, 'aBD', 1);
		$ga = '';
		if ($gacode = trim($a['GACode']))
		{
		  
			$ga = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

			if (!$ga->checkCode(exValue($a['GAKey'], $_user['aGA']), $gacode))
				setError('ga_wrong');
			$a['aGA'] = valueIf($_user['aGA'], '', $a['GAKey']);
			$ga = 'aGA, ';
		}
        
        $db->insert('AddInfo',
		              array(
			                 'auID' => _uid(),
			                 'aIPSec' => 4,
                             'aName' => '',
                             'aCTS' => 0,
                             'aCIP' => '',
                             'aOIDs' => '',
                             'aBD' => 0,
                             'aCountry' => '',
                             'aCity' => '',
                             'aTel' => '',
                             'aSessIP' => 0
		                  )
	    );
        
        $fields = explode(', ', valueIf($_cfg['Account_UseName'] == 2, 'aName, ') . $f . $ga . 'aTZ, aIPSec, aSessIP, aSessUniq, aTimeOut, aNoMail, aNeedReConfig');
                
        foreach ($fields as $i => $field) :
            
                if (!isset($a[$field])) {
                    unset($fields[$i]);
                }
            
        endforeach; 

		$db->update('AddInfo', $a, 	implode(', ', $fields), 'auID=?d', array(_uid()));
    
		showInfo('Saved');
	}

} 
catch (Exception $e) 
{
}

stampArrayToStr($_user, 'aBD', 1);
setPage('user', $_user);
setPage('utz', sprintf("%+02d:%02d", floor($_user['aTZ'] / 60), abs($_user['aTZ'] % 60)));

if (!$_user['aGA'])
{
	$ga = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
	if (!$_SESSION['GANewCode'])
		$_SESSION['GANewCode'] = $ga->generateSecret();
	setPage('GACode', $_SESSION['GANewCode']);
	setPage('GAQR', \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($_user['uLogin'] . '@' . $_GS['domain'], $_SESSION['GANewCode']));
}

showPage();