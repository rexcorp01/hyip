<?phpglobal $self_category, $admin_modules;$self_category = '';$admin_modules = array();foreach ($_rwlinks as $m => $r)	if ($r['admin'])	{		if (!is_string($r['admin']))			$r['admin'] = $m;		if (!$r['category'])		{			$s = explode('/', $r['admin'], 2);			if ($s[1])			{				$r['category'] = $s[0];				$r['admin'] = $s[1];			}		}		prepVal($r['category'], 3);		prepVal($r['admin'], 3);		if ($m == $_GS['module'])			$self_category = $r['category'];		if ($r['admin'] != '-')        {          $admin_modules[$r['category']][$r['admin']] = $m;        }	}setPage('up_category', $self_category);setPage('up_modules', $admin_modules[$self_category]);setPage('admin_modules_links', $admin_modules);$uid = _uid();$admin_links_list=array();$admin_links=array(); $sql="SELECT url       FROM Admin_menu       WHERE admin_id='".mysqli_real_escape_string($uid)."'       ORDER BY date_add DESC";  $result = $db->_doQuery($sql);  while($row = $db->fetch($result))  {    $r=$_rwlinks[$row['url']];    prepVal($r['category'], 3);    prepVal($r['admin'], 1);    $r['admin']=str_replace("{!ru!}", "", $r['admin']);    $r['admin']=str_replace("{!en!}", "", $r['admin']);    $ar=explode('/', $r['admin']);    $r['admin']=$ar[1];    $admin_links[]=array(                          'url' => $row['url'],                          'name' => $r['admin']                        );    $admin_links_list[]=$row['url'];  }  setPage('admin_links', $admin_links);  setPage('admin_links_list', $admin_links_list);  $root_dir=str_replace("/rw.php", "", $_SERVER['SCRIPT_FILENAME']);  $SiteInf=file_get_contents($root_dir.'/module/_config/siteinf.txt');  setPage('SiteInf', $SiteInf);  setPage('SiteInfDisable', $_cfg['Sys_SiteInfDisable']);clearstatcache();setPage('needupdatedb', ($_cfg['Const_DBVer'] != @filemtime('_dbstru.php')));$_AT = require_once('languages/adm_'.$_GS['lang'].'.php');foreach ($_AT as $key=>$value) $_AT[$key] = htmlspecialchars_decode($value);setPage('_AT', $_AT);