<? 

	
	// Copy trader
	if($demo == 0){
	 $users_copy = $db->in_array("SELECT * FROM copy_traders  WHERE trader_id = '".$user['id']."' && status = '1' ");
    
	foreach($users_copy as $item){
	
   $u = $db->assoc("SELECT * FROM users WHERE id = '".$item['user_id']."' ");	
		
		
	if($u['balance'] >= $item['amount']){
		   
       $db->update('users', "balance = balance-".$item['amount']." WHERE  id = '".$item['user_id']."' ");
		
 

	   $binary_percent_amount = $item['amount'] * intval($binary_percent) / 100;
	   $binary_percent_amount =  @number_format($binary_percent_amount, 2, '.', '');
		
	 $arr = array(
	    'user_id'       =>   $item['user_id'],
	    'lot'           =>   $item['amount'],
	    'trend'         =>   $form['trend'],
	    'profit'        =>   0 - $item['amount'],
	    'current_balance'   =>    $u['balance'],
	    'status'        =>   0,
	    'course_start'  =>   $course,
	    'course_end'    =>   '',
	    'currency_k'    =>   $currency['k'],
	    'currency'      =>   $currency['name'],
	    'currency_id'   =>   $currency['id'],
	    'time_start'    =>   time(),
	    'time_end'      =>   $time_end,
	    'demo'          =>   $demo,
	    'partner_cron'  =>   0,
	    'option_type'   =>   $option['type'],
        'top_trader'    =>   $user['id'],
		'forex'         =>   $currency['forex'],
	    'leverage'      =>   intval($form['leverage']),
	    'stoploss_amount'     =>   $form['stoploss'],
	    'stoploss_course'     =>   $stoploss_course,	   
	    'takeprofit_amount'   =>   $form['takeprofit'],
	   	'takeprofit_course'   =>   $takeprofit_course,
		   'binary_percent'  =>  $binary_percent,
		   'binary_percent_amount'  =>  $binary_percent_amount,
		   'tipo' => $tipo,
		   'marketing' => ((int) $item['level'] === 1) ? 1 : 0
		 
		 
	 );
	   $copy_id = $db->insert('lots', $arr);
	   $arr['id'] = $copy_id;
		$redis->hset('lots', $copy_id, json_encode($arr));
	   $redis->hset('lots:'.$item['user_id'], $copy_id, $copy_id);

	}
   }
}
	