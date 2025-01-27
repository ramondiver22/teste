<?

 
switch($url[2]){ // $url[3]

  case 'lot_height':
     $db->update('users', "`lot_height` = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;
 
		// $url[4]
		
		
  case 'w1':
     $db->update('users', "`w1`  = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;

  case 'w2':
     $db->update('users', "`w2`  = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;

  case 'w3':
     $db->update('users', "`w3`  = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;

  case 'w4':
     $db->update('users', "`w4`  = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;



  case 'window':
     $db->update('users', "`window`  = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;


  case 'sound':
     $db->update('users', "`sound` = '".intval($url[3])."'  WHERE  id = '".$user['id']."' ");
  break;

  
  case 'color':
  if($user['color'] == 1){
  $db->update('users', "`color` = '0'  WHERE  id = '".$user['id']."' ");
}else{
  $db->update('users', "`color` = '1'  WHERE  id = '".$user['id']."' ");
}

  break; 


}
