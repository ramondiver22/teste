<? 
	
	
switch($url[2]){
// $url[3]

   
default:
       	
if($form['trader_id']  && $form['amount']){

  
  $arr = array(
   'user_id'           => $user['id'],
   'trader_id'         => $form['trader_id'],
   'amount'            => $form['amount'],
  );
  $db->insert('copy_traders', $arr);
  echo 'ok';
  fechar();
  exit;
}	
	

	
$table = $db->in_array("SELECT * FROM users  WHERE `copy` = 1 order by 	profit	desc ");	
	
$table2 = $db->in_array("SELECT users.f, users.i, users.profit, copy_traders.id, copy_traders.amount FROM users, copy_traders  WHERE copy_traders.trader_id = users.id  && copy_traders.user_id = '".$user['id']."' ");	

  include('tpl/traderoom/traders.tpl'); 
break;


 
case 'del':
	 $id = 	$db->read("SELECT id FROM copy_traders WHERE user_id = '".$user['id']."'  &&  id = '".$form['id']."' ");
	 if($id > 0)	$db->delete("copy_traders", $id);
  echo 'ok';
  fechar();
  exit;  
 break;
		
}
	
	
	
	
	
	
