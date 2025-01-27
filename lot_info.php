<? 
$table = $db->assoc("SELECT * FROM lots WHERE user_id = '".$user['id']."'  && id = '".intval($url[2])."'  ");
	
	
	
	

include('tpl/mob/lot_info.tpl');   

