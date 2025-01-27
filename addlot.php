<?

if($form['lot']  &&  $form['binarytime'] > 0  && ($form['trend'] == 'up' || $form['trend'] == 'down')   && $form['currency_id']){
  
 
	$db->update('users', "`w1_amount` = '".$form['lot']."' WHERE  id = '".$user['id']."' ");

	 
	
   
 if($conf['close'] == 1)  json_err($lang[30].": Erro 0x000003");

	
	if($db->read("SELECT count(*) FROM lots WHERE user_id='".$user['id']."' AND status = '0'") >= 10) json_err("Maximo de Operações Atingida");
  
   $currency = $db->assoc("SELECT * FROM  currency  WHERE   id = '".intval($form['currency_id'])."'  && status = '1'    ");
	


  if($currency['time_status'] == 0)  json_err("Not trading time");
	
	$intervalos = array(
		60 => 1,
		300 => 5,
		900 => 15
	);
	$binaryTime = intval($form['binarytime']);
	if( !$binaryTime === 60 || !$binaryTime === 300 || !$binaryTime === 900 ){
		json_err('Tempo não encontrado');
	}
	$timestamp = getdate();
	$tipo = "M".$intervalos[intval($form['binarytime'])];
	$time = time();
	$minutos = date('i', time());
	$input = intval($form['binarytime']) / 60;
	$tempo = 0;
	while(!($minutos < $tempo)) $tempo += $input;

	$time_end = mktime(date('G'), $tempo, 0); //$time + intval($form['binarytime']);
	$proximo = $time + 30;
	if($time_end < $proximo) $time_end = mktime(date('G'), $tempo+$input, 0);




//    $course = $db->read("SELECT course FROM currency WHERE  id = '".$currency['id']."' ");
	
   $course = $redis->hget('currency', $currency['k']);
   $form['lot'] = floatval($form['lot']);

   if($course <= 0){
	   json_err($lang[30].": Erro 0x000001");
   }

 
   if($user['status'] == 2){
	   json_err($lang[14]);
   }


   if(!$currency){
	   json_err($lang[30].": Erro 0x000002");
   }


   if($form['lot'] < $conf['minlot']  && $conf['minlot'] != 0){
	   json_err($lang[32].' R$'.$conf['minlot']);
   }
	 if($form['lot'] > $conf['maxlot']  && $conf['maxlot'] != 0){
		json_err('Valor maximo permitido: R$ '.$conf['maxlot']);
	}
 

   if($user['balance_demo'] < $form['lot']  && $demo == 1){
	   json_err($lang[31]);
   }
	 $valor = 0;
	 $balance = $form['lot'];
   if($user['balance'] < $form['lot']  && $demo == 0){
		$valor = $form['lot'] - $user['balance'];
		$balance = $form['lot'] - $valor;
		if($user['bonus'] < $valor){
			json_err($lang[31]);
		}
   }


	
	
 


   // Maximum volume of lots
   $current_amount =  $db->result("SELECT sum(lot) FROM lots   WHERE   status = '0'    &&    user_id = '".$user['id']."'   &&    currency_id = '".$currency['id']."'  ");
   
   
   if($demo == 1) $deposit = $user['balance_demo'] + $current_amount;
   if($demo == 0) $deposit = $user['balance'] + $user['bonus'] + $current_amount;
	
	
   $current_amount = $current_amount + $form['lot'];
   $max_amount = $deposit * $conf['maxlot'] / 100;
   
   if($current_amount >  $max_amount && $conf['maxlot'] != 0){
	  
	    
	   json_err($lang[33].'  '.$conf['maxlot'].'% '.$lang[34]);
   }


	
 
   $binary_percent = $currency['profit'];
 

   $binary_percent_amount = $form['lot'] * intval($binary_percent) / 100;
   $binary_percent_amount =  @number_format($binary_percent_amount, 2, '.', '');


   
 	
 if($demo == 1){
    $db->update('users', "balance_demo = balance_demo-".$form['lot']." WHERE  id = '".$user['id']."' ");
		$current_balance = $user['balance_demo'];
 }else{
    $db->update('users', "balance = balance-".$balance.", bonus = bonus-".$valor.", lots=lots+1, last_trade_time = '".time()."'    WHERE  id = '".$user['id']."' ");
	 	$current_balance = $user['balance']+$user['bonus'];
 }
	
   
   $arr = array(
	    'user_id'       =>   $user['id'],
	    'lot'           =>   $form['lot'],
		'lot_bonus'		=> $valor,
	    'trend'         =>   $form['trend'],
	    'profit'        =>   0 - $form['lot'],
	    'current_balance'   =>    $current_balance,
	    'status'        =>   0,
	    'course_start'  =>   $course,
	    'course_end'    =>   '',
	    'currency_k'    =>   $currency['k'],
	    'currency'      =>   $currency['name'],
	    'currency_id'   =>   $currency['id'],
	    'time_start'    =>   $time,
	    'time_end'      =>   $time_end,
	    'demo'          =>   $demo,
	    'partner_cron'  =>   0,
	    'option_type'   =>   $option['type'],	   
	    'binary_percent'  =>  $binary_percent,
	    'binary_percent_amount'  =>  $binary_percent_amount,
			'tipo' => $tipo,
			'marketing' => ((int) $user['level'] === 1) ? 1 : 0

	);
	//if($user['level'] === 1) echo 'ok';
	$lot_id =  $db->insert('lots', $arr);
    $lot = $db->assoc("SELECT * FROM  lots WHERE   id = '".$lot_id."'");
	$arr['id'] = $lot_id;
 	$redis->hset('lots', $lot_id, json_encode($arr));
	$redis->hset('lots:'.$user['id'], $lot_id, $lot_id);
 
	
	
	
	
	
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


 


