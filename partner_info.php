<? 
 $table =  $db->assoc("SELECT * FROM partner_stat  WHERE   user_id = '".$user['id']."'  && id = '".intval($url[2])."'    ");
	
	

include('tpl/mob/partner_info.tpl');   

