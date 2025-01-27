<?
if(!$url[2]) $url[2] = 'default';
// $url[3]
date_default_timezone_set($user['timezone']); 

 
$in =   strtotime(implode('-', array($get['y_in'], $get['m_in'], $get['d_in'])));
$out =   strtotime(implode('-', array($get['y_out'], $get['m_out'], $get['d_out'])));

if($in) $q .= "  &&  time_start >=  $in  "; 
if($out) $q .= "  &&  time_start <=  $out  "; 

if($get['currency'] != 'default' && $get['currency'] ) $q .= "  &&  currency  =  '".strtoupper($get['currency'])."'  "; 


if($get['status'] == 'open'){
	$q .= "  &&  status  = 0   "; 
}
if($get['status'] == 'closed'){
	$q .= "  &&  status  = 1   "; 
}



$table = $core->table("SELECT * FROM lots WHERE  demo = '".$demo."'  && user_id = '".$user['id']."'    $q  order by id desc", $url[3], 10);
$pages = $core->pages();


$profit = $db->result("SELECT sum(profit) FROM lots WHERE  demo = '".$demo."'  && user_id = '".$user['id']."'    $q ");
$loss = $db->result("SELECT sum(lot) FROM lots WHERE  demo = '".$demo."'  && user_id = '".$user['id']."'   && profit = 0   $q ");
$profit = number_format($profit, 0, '.', ' ');


$currency = $db->in_array("SELECT * FROM currency   order by id ");





include('tpl/traderoom/tradehistory.tpl');
