<?

	
	
switch($url[2]){ //switch($url[3]){
		
		
default:	
 
break;
		
		
 case 'add_demo':

	  $arr = array(
          'balance_demo'      => '10000',
       );
      $db->update('users', $arr, " id = '".$user['id']."' ");


		
 break;
}