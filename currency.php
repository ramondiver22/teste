<?
	
if($url[2] == 2){// $url[3]
  // Favorits list
  	
   $currency = $db->in_array("SELECT currency.*  FROM currency, currency_fav  WHERE  currency_fav.user_id = '".$user['id']."'  &&  currency.id=currency_fav.currency_id   order by  currency_fav.id ");

}else{
  // Forex/Binary list
  $currency = $db->in_array("SELECT * FROM currency WHERE status = '1'  && category = '".intval($url[2])."'  order by  time_status desc");
}
	
 
 	
function fav($currency_id){
  global $db, $user;
  if($db->read("SELECT id FROM currency_fav WHERE currency_id = '".intval($currency_id)."'  &&  user_id = '".$user['id']."'  ")){
    return true;
  }
  return false;
}
 


include('tpl/mob/currency.tpl');