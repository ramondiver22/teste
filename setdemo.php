<? 

$demo = 0;
if($url[2] == 1) $demo = 1;
 // $url[3]
  $db->update('users', "demo = '".$demo."'  WHERE  id = '".$user['id']."'  "); 

$core->redir($ref);