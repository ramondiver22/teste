<?

switch($url[2]){
// $url[3]


  case 'list':
    $tabs = $db->in_array("SELECT tabs.id, tabs.currency_id, currency.name, currency.forex, currency.k, currency.category  FROM  currency, tabs WHERE  currency.id=tabs.currency_id  &&   tabs.user_id = '".$user['id']."'  order by tabs.id      ");

    include 'tpl/traderoom/tabs.tpl';
  break;


  case 'add':
	 if($url[3] > 0){ // $url[4]
     $arr = array(
	         'user_id'       =>   $user['id'],
	         'currency_id'   =>  intval($url[3]),
        	);
      $db->insert('tabs', $arr);
    }
  break;


  case 'del':
	  if($url[3] > 0){ 
        $db->query("DELETE FROM tabs WHERE user_id = '".$user['id']."'   &&  id = '".intval($url[3])."' ");
	  }
  break;



}

