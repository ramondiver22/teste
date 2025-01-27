<? 
 	
if(!$url[2]) $url[2] = 'default';
//// $url[3]

switch($url[2]){
// $url[3]
 
		
		
		
  case 'list': 
    $table = $db->in_array("SELECT * FROM indicators  WHERE category = '".intval($form['category'])."'  ");
		
		
    include('tpl/traderoom/indicators.tpl');
  break;

		
		
  case 'search': 
    $table = $db->in_array("SELECT * FROM indicators  WHERE  name like '%".$form['q']."%'  ");
		
    include('tpl/traderoom/indicators.tpl');
  break;

				
  
		
		

  case 'added': 
	   $w = intval($form['window_id']);
		
      $table = $db->in_array("SELECT indicators.* FROM indicators, indicators_user  WHERE  indicators_user.indicator_id = indicators.id && indicators_user.user_id = '".$user['id']."' && indicators_user.window_id = '".$w."'   ");
		
		
    include('tpl/traderoom/indicators_added.tpl');
  break;
 
		
		
 case 'added_json': 
	   $w = intval($form['window_id']);
		
      $table = $db->in_array("SELECT indicators.* FROM indicators, indicators_user  WHERE  indicators_user.indicator_id = indicators.id && indicators_user.user_id = '".$user['id']."' && indicators_user.window_id = '".$w."'   ");
		
		
     echo json_encode($table);
  break;
 


  case 'add':
	 $indicator_id = intval($form['indicator_id']);	
	 $window_id = intval($form['window_id']);		
  		
		
	 $i = $db->read("SELECT id FROM indicators_user  WHERE user_id = '".$user['id']."' && indicator_id = '".$indicator_id."' && window_id = '".$window_id."'  ");
		
	
		
		
	 if(!$i  && $indicator_id  && $window_id){	
       $arr = array(
       'indicator_id'      => $indicator_id,
       'user_id'           => $user['id'],
       'window_id'         => $window_id,
       );
       $db->insert('indicators_user', $arr);
		echo json_encode('ok'); 
	 }else{
        echo json_encode(''); 
	 }
  break;

		
		
		
  case 'del':
	 $indicator_id = intval($form['indicator_id']);	
     $window_id = intval($form['window_id']);
		

		
  	 $id = $db->read("SELECT id FROM indicators_user  WHERE    indicator_id = '".$indicator_id."'  &&  user_id = '".$user['id']."'   &&  window_id = '".$window_id."'      ");
		
	 if($id )  $db->delete('indicators_user', $id);	
  break;

		
		
		
  case 'del_all':
     $window_id = intval($form['window_id']);
		
  	 $db->query("DELETE FROM indicators_user  WHERE  user_id = '".$user['id']."'   &&  window_id = '".$window_id."'      ");
  break;

	
}	
	
 