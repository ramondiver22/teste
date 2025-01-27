<?	
$i = $db->assoc("SELECT * FROM users WHERE  id = '".$url[1]."'     ");
if($i['id']){
  session_start();
  setcookie('partner', $i['id'],  time()+86400*30*6, '/');
  $core->partnerStat('clicks', 1, $i['id']);

  $time_left = time() - 86400;
  if(!$db->read("SELECT id  FROM   partner_clicks_ip  WHERE   user_id =  '".$i['id']."'  && time > $time_left   && ip = '".$ip."' ")){
    $core->partnerStat('uniq_clicks', 1, $i['id']);
	$arr = array(
		    'user_id'      =>   $i['id'],
		    'ip'           =>   $ip,
		    'time'         =>   time(),
	);		
    $db->insert('partner_clicks_ip', $arr);
  }
}
$core->redir('/register');