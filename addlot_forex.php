<?

	
if($form['lot'] && $form['leverage']   && ($form['trend'] == 'up' || $form['trend'] == 'down')   && $form['currency_id']){
   

 if($conf['close'] == 1)  json_err($lang[30]);
	
	
  
   $currency = $db->assoc("SELECT * FROM  currency  WHERE   id = '".intval($form['currency_id'])."'  && status = '1'    ");
   
		
  if($currency['time_status'] == 0)  json_err("Not trading time");
	
	
	
	$time =   $currency['time'];
	
	
   $course = $currency['course'];
  
   
   $form['lot'] = floatval($form['lot']);

   if($course <= 0){
	     json_err($lang[30]);
   }

 
 
   if($user['status'] == 2){
	   json_err($lang[14]);
   }


   if(!$currency){
	    json_err($lang[30]);
   }


   if($form['lot'] < $conf['minlot']  && $conf['minlot'] != 0){
	     json_err($lang[32].' $'.$conf['minlot']);
   }
  
   if($user['balance_demo'] < $form['lot']  && $demo == 1){
	     json_err($lang[31]);
   }

   if($user['balance'] < $form['lot']  && $demo == 0){
	     json_err($lang[31]);
   }

	
 if($demo == 1){
     $db->update('users', "balance_demo = balance_demo-".$form['lot']." WHERE  id = '".$user['id']."' ");
	  $current_balance = $user['balance_demo'];
 }else{
     $db->update('users', "balance = balance-".$form['lot'].", lots=lots+1, last_trade_time = '".time()."'  WHERE  id = '".$user['id']."' ");
	  $current_balance = $user['balance'];
 }


 
   
   if($form['stoploss'] > 0){
	   $amount = floatval($form['leverage'] * $form['lot']);  
	   $cs  = ($form['stoploss']/$amount)*$course;	   
	   if($form['trend'] == 'down'){
		   $stoploss_course = $course + $cs;
	   }
	   if($form['trend'] == 'up'){
		   $stoploss_course = $course - $cs;
	   }
   }
   if($form['takeprofit'] > 0){
	   $amount = floatval($form['leverage'] * $form['lot']);   
	   $cs  = ($form['takeprofit']/$amount)*$course;	   
	   if($form['trend'] == 'down'){
		   $takeprofit_course = $course - $cs;
	   }
	   if($form['trend'] == 'up'){
		   $takeprofit_course = $course + $cs;
	   }
   }
	
   
   $arr = array(
	    'user_id'       =>   $user['id'],
	    'lot'           =>   $form['lot'],
	    'trend'         =>   $form['trend'],
	    'profit'        =>   0,
	    'current_balance'   =>    $current_balance,
	    'status'        =>   0,
	    'course_start'  =>   $course,
	    'course_end'    =>   '',
	    'currency_k'    =>   $currency['k'],
	    'currency'      =>   $currency['name'],
	    'currency_id'   =>   $currency['id'],
	    'time_start'    =>   $time,
	    'time_end'      =>   0,
	    'demo'          =>   $demo,
	    'partner_cron'  =>   0,
 	    'option_type'   =>   0,
        'forex'         =>   1,
	    'leverage'      =>   intval($form['leverage']),
	    'stoploss_amount'     =>   $form['stoploss'],
	    'stoploss_course'     =>   $stoploss_course,	   
	    'takeprofit_amount'   =>   $form['takeprofit'],
	   	'takeprofit_course'   =>   $takeprofit_course,
	);
	$lot_id = $db->insert('lots', $arr);
     $lot = $db->assoc("SELECT * FROM  lots WHERE   id = '".$lot_id."'");
 
   
	
	// Copy trader
	include 'pages/traderoom/copytrade.php';
	
	
	
	
	
	
    json_ok($lot);

   
	
	
}





json_err($lang[35]);



function json_ok($lot){
	echo json_encode(array('status' => 'ok','lot' => $lot));
	exit();
}


function json_err($mess){
	echo json_encode(array('status' => $mess));
	exit();
}


