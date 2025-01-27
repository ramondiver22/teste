<? 
 
 
if($form['window_id']  == 1){
  $db->update('users', "w1_type = '".intval($form['typechart'])."'  WHERE  id = '".$user['id']."'  "); 
}	
if($form['window_id']  == 2){
  $db->update('users', "w2_type = '".intval($form['typechart'])."'  WHERE  id = '".$user['id']."'  "); 
}		
	
 
 if(intval($form['resolution']) > 0) {
  $db->update('users', "`w1_resolution` = '".intval($form['resolution'])."' WHERE id = '".$user['id']."'");
  echo 'ok';
 }

 if(intval($form['time']) > 0) {
  $db->update('users', "w1_time = '".intval($form['time'])."' WHERE id = '".$user['id']."'");
 }
 
 if(intval($form['amount']) > 0) {
  $db->update('users', "w1_amount = '".intval($form['amount'])."' WHERE id = '".$user['id']."'");
 }