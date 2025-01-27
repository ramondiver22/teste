<? 

	
if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){
 

		
  case 'contacts':
		
	 if($form['q']) $q = " &&  login like '%".$form['q']."%'      ";	
		
	 $table = $db->in_array("SELECT chats.last_message,chats.id, users.login,users.online,users.photo FROM chats, users WHERE  chats.user_from = users.id && chats.user_to = '".$user['id']."'  $q    order by chats.last_time  desc");
		
		
		
 	 include 'tpl/dashboard/chat/contacts.tpl';

  break;
		
		
  case 'messages':
	 $id = intval($form['id']);	
	
		
	$db->update('users', "newmess=0 WHERE id = '".$user['id']."' ");
		
	 $table = $db->in_array("SELECT * FROM chat_messages      WHERE 	chat_id = '".$id."' ");	
 	 include 'tpl/dashboard/chat/messages.tpl';
  break;
		
			
  case 'addmessage':
		
   $chat_id = intval($form['chat_id']);	
		
   $message = $core->filterText($_POST['message']);	
		
		
   $chat = $db->assoc("SELECT * FROM chats  WHERE id = '".$chat_id."' ");
	
		
   $arr = array(
     'last_message'      => $message,
     'last_time'         => time(),
   );
   $db->update('chats', $arr, " id = '".$chat_id."' ");
	
		
		
		
   $arr = array(
   'chat_id'   => $chat_id,
   'message'           => $message,
   'user_from'         => $user['id'],
   'user_to'           => $chat['user_from'],
   'time'              => time(),
   );
  $db->insert('chat_messages', $arr);
	
		
  break;
			
		
		
}	


function f($v){
	$v = str_replace("\n","<br>", $v); 
	return $v;
}

function online($time){
	$r = date("d.m.Y H:i:s");
	
	if($time > time()-600){
		$r = 'Online';
	}
	
	return $r;
}
