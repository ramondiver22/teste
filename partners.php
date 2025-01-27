<? 

if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){


  default:
 
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/partners/partners.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
		
		
		
		
  case 'referals':
  
    if($form['q']) $q .= " && users.login like '%".$form['q']."%'  ";	
		
 		
 		
     $table = $db->in_array("SELECT  referals.*,  users.login FROM referals, users WHERE referals.user_id=users.id     $q    order by referals.id desc ");
    include('tpl/dashboard/partners/list.tpl');
  break;
		
 
		
}

function getLogin($id){
  global $db;
	return $db->read("SELECT login FROM users WHERE id = '".$id."' ");
	
}
 