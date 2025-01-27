<?
if(!$url[2]) $url[2] = 'default';
// $url[3]




switch($url[2]){
// $url[3]

  default: 
   $tarifs  = $db->in_array("SELECT * FROM  invest_tarifs  order by  id");
 
   include('tpl/traderoom/invest.tpl');
  break;

		
  case 'history':
   $table = $db->in_array("SELECT * FROM invest WHERE  user_id = '".$user['id']."'   order by id desc");

   include('tpl/traderoom/invest_history.tpl');
  break;

		
  case 'add':
   $tarif  = $db->assoc("SELECT * FROM  invest_tarifs  WHERE id = '".$url[3]."' "); // $url[4]
   if(!$tarif) exit();

   $int_end = time() + ($tarif['days'] * 86400);
   $end = date("d.m.Y", $int_end);

 

   $form['amount'] = floatval($form['amount']);
   
   
   $profit_total = $form['amount'] + ($form['amount'] * $tarif['profit'] / 100);
   $profit_total = number_format($profit_total, 2, '.', '');


   if($_POST){
    if($form['amount'] <  $tarif['amount_min']){
     $mess = $lang[83].' '.$tarif['amount_min'].'$';
    }elseif($form['amount'] >  $tarif['amount_max']){
     $mess = $lang[84].' '.$tarif['amount_max'].'$';
    }elseif($user['balance'] <  $form['amount']){
     $mess = $lang[85];
    }elseif($form['terms'] != 1){
     $mess = $lang[86];
    }else{

     $db->update('users', "balance = balance-".$form['amount']."  WHERE id = '".$user['id']."'     ");
     
	 
	 
	 
	 $all_days =    intval(($int_end-time())/86400);
     $profit_for_day = $profit_total/$all_days;
	 
	 $arr = array(
	    'user_id'      =>   $user['id'],
	    'status'       =>   0,
	    'tarif_id'     =>   $tarif['id'],
	    'time_start'   =>   time(),
	    'time_end'     =>   $int_end,
	    'amount'       =>   $form['amount'],
	    'profit'       =>   $profit_total,
	    'profit_day'   =>   $profit_for_day,
	  );
	  $db->insert('invest', $arr);
      
	 
       echo 'ok';
	   exit;
    }
	echo $mess;
    exit;   
   }

   if($tarif['amount_min'] == $tarif['amount_max']){
     $fixamount = $tarif['amount_max'];
   }
	 

   


   include('tpl/traderoom/invest_add.tpl');
  break;
}





function status($status){
   global $lang;
  if($status == 0)  return  $lang[88];
  if($status == 1)  return   $lang[89];
}

function tarif($id){
  global $db;
  return $db->read("SELECT name FROM invest_tarifs   WHERE id = '".$id."' ");
}


function current_profit($table){
    if($table['status'] == 1)  return $table['profit'];

    $all_days =    intval(($table['time_end']-$table['time_start'])/86400);
    $profit_for_day = $table['profit']/$all_days;

    $count_days = intval((time()-$table['time_start'])/86400);
    $profit = $profit_for_day  * $count_days;
 
    $profit = number_format($profit, 2, '.', '');

	
	return $profit;
}