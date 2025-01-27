<?
if(!$url[2]) $url[2] = 'default';// $url[3]



switch($url[2]){ // $url[4]


  default: 
    $in =   strtotime(implode('-', array($get['y_in'], $get['m_in'], $get['d_in'])));
    $out =   strtotime(implode('-', array($get['y_out'], $get['m_out'], $get['d_out'])));

	if($in) $q .= "  &&  time >=  $in  "; 
	if($out) $q .= "  &&  time <=  $out  "; 

    $table = $core->table("SELECT * FROM payin WHERE user_id = '".$user['id']."'  $q  order by id desc", $url[3], 10); // $url[4]
    $pages = $core->pages();


    
    include('tpl/traderoom/payhistory.tpl');
 
  break;



  

  case 'payout':
    $in =   strtotime(implode('-', array($get['y_in'], $get['m_in'], $get['d_in'])));
    $out =   strtotime(implode('-', array($get['y_out'], $get['m_out'], $get['d_out'])));

	if($in) $q .= "  &&  time >=  $in  "; 
	if($out) $q .= "  &&  time <=  $out  "; 

    $table = $core->table("SELECT * FROM payout WHERE   user_id = '".$user['id']."'  $q  order by id desc", $url[3], 10); // $url[4]
    $pages = $core->pages();

 
    include('tpl/traderoom/payhistoryout.tpl');
  

  break;

  case 'cancel':

    $payout = $db->assoc("SELECT * FROM payout WHERE id = '".$form['id']."' && user_id = '".$user['id']."'");

    $arr2 = array();
    $arr = array(
     'status'            => 7,
    );
    
    if($payout['tipo'] === 'normal' && $payout['status'] === "0") {
      $db->update('payout', $arr, " id = '".$form['id']."' ");
      $bonus = json_decode($payout['metadata']);
      if($db->update('users', "`balance` = `balance` + '".$payout['amount_usd']."', `bonus` = `bonus` + '".$bonus->bonus_perdido."' WHERE id = '".$payout['user_id']."'")){
        
        echo 'ok';
      } 
    }elseif($payout['tipo'] === 'afiliado' && $payout['status'] === "0"){
      $db->update('payout', $arr, " id = '".$form['id']."' ");
      if($db->update('users', "`balance_afiliado` = `balance_afiliado` + '".$payout['amount_usd']."' WHERE id = '".$payout['user_id']."'")){
        echo 'ok';
      } 
    }
		
    fechar();
	exit;
  break;			


}


function paysystem($id){
   global $db;
	$i = $db->assoc("SELECT * FROM  payin_types WHERE id = '".$id."'   ");
	return  $i['name'];
}

function  paytype($id){
	global $db;
	return $db->assoc("SELECT * FROM payout_types WHERE id  = '".$id."' ");
}
