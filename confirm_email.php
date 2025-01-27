<?	
$i = $db->assoc("SELECT * FROM users WHERE  hash = '".$url[1]."'     ");
if($i['id']){
  $db->update('users', " email_confirm = '1'  WHERE id  = '".$i['id']."'    ");
   $core->redir('/');
}
