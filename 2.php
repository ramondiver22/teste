<?php


function lots(){
	global $db, $redis;
	//$lots = $db->in_array("SELECT * FROM lots WHERE  status = '0'   && forex = '0'    limit 0,10000  ");
	$lots_ = $redis->hgetall('lots');
  
	$course = $redis->hgetall('currency');
	foreach($lots_ as $lot){
	   $lot = json_decode($lot, true);
		$course_end = $course[$lot['currency_k']];//$db->read("SELECT course FROM currency WHERE  id = '".$lot['currency_id']."' ");
	 
	 $profit = profit($lot, $course_end);
   
	 
	 $lot['profit'] = $profit;
	 $redis->hset('lots', $lot['id'], json_encode($lot));
	 if($lot['time_end'] <= time()){
	   close($lot, $course_end, $profit);
	 }	
  
   }
  }


function close($lot, $course_end, $profit){
	global $db, $redis;

		  
		  if($lot['demo'] == 1){	  
			$db->update('users', "balance_demo = balance_demo+".floatval($lot['lot'])." WHERE  id = '".$lot['user_id']."' "); 
		    $db->update('users', "balance_demo = balance_demo+".floatval($profit)." WHERE  id = '".$lot['user_id']."' ");
		  }
		  
		  if($lot['demo'] == 0){
			if(floatval($lot['lot_bonus']) > 0 && floatval($profit) > 0){
				$lot['lot'] = floatval($lot['lot']) - floatval($lot['lot_bonus']);
				$db->update('users', "bonus = bonus+".floatval($lot['lot_bonus'])." WHERE  id = '".$lot['user_id']."' ");  
			}
			$db->update('users', "balance = balance+".floatval($lot['lot'])." WHERE  id = '".$lot['user_id']."' ");  
		    $db->update('users', "balance = balance+".floatval($profit)." WHERE  id = '".$lot['user_id']."' ");
		  }
			
			$db->update('lots', "status = '1', profit = '".floatval($profit)."', course_end = '".$course_end."' WHERE  id = '".$lot['id']."'  ");
		  	$redis->hdel('lots', $lot['id']);
		  	$redis->hdel('lots:'.$lot['user_id'], $lot['id']);
			alertMessage($profit, $lot);
	
}




 
function profit($lot, $course_end){ 
	global $db, $redis;
	  $profit = -$lot['lot'];
	  
	  if($lot['trend'] == 'down'){
	    if($course_end < $lot['course_start']){
				$profit = $lot['binary_percent_amount'];
	    }
	  }
	  if($lot['trend'] == 'up'){
	    if($course_end > $lot['course_start']){
				$profit = $lot['binary_percent_amount'];
	    }
	  }
	  //if($course_end == $lot['course_start']){
		//  $profit = 0;
	  //}
   
	
	
	  //$db->update('lots', "profit = '".floatval($profit)."', course_end = '".$course_end."'   WHERE  id = '".$lot['id']."'  ");
	  $redis->hset('profit', $lot['id'], $course_end);
	
	
	 // Profit last day
	 $left = strtotime(date('Y-m-d 00:00:00'));
	 //$profit_day = $db->read("SELECT sum(profit) FROM lots WHERE user_id = '".$lot['user_id']."'  &&  time_start  > ".$left." && demo = '0'  && status = '1'   ");
	  //$arr = array(
     // 'profit_day'            =>  $profit_day,
     // );
     // $db->update('users', $arr, " id = '".$lot['user_id']."' ");
	
	
	

   return $profit;
}



function alertMessage($profit, $lot){
	 global $db;
    $arr = array(
     'time'              => time(),
     'status'            => '0',
     'user_id'           => $lot['user_id'],
     'currency_k'        => $lot['currency_k'],
     'currency_name'     => $lot['currency'],
	 'profit'            => $profit,	
   );
   $db->insert('alerts', $arr);

}

 lots();