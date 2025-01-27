<?


switch($url[2]){// $url[3]



  default: 

		$etapa = 0;
   if($_POST){
		if($form['tel']) $db->update('users',  "tel = '".$form['tel']."'  WHERE id = '".$user['id']."'  ");
		if($user['verify'] != 1){
			if($form['i']) $db->update('users',  "i = '".$form['i']."'  WHERE id = '".$user['id']."'  ");
			if($form['f']) $db->update('users',  "f = '".$form['f']."'  WHERE id = '".$user['id']."'  ");
			if($form['o']) $db->update('users',  "o = '".$form['o']."'  WHERE id = '".$user['id']."'  ");
			if($form['cpf'] && empty($user['cpf'])) $db->update('users',  "cpf = '".$form['cpf']."'  WHERE id = '".$user['id']."'  ");
			if($form['country']) $db->update('users',  "country = '".$form['country']."'  WHERE id = '".$user['id']."'  ");
			if($form['sex']) $db->update('users',  "sex = '".$form['sex']."'  WHERE id = '".$user['id']."'  ");
			if($form['city']) $db->update('users',  "city = '".$form['city']."'  WHERE id = '".$user['id']."'  ");
			if($form['birth']) $db->update('users',  "birth = '".$form['birth']."'  WHERE id = '".$user['id']."'  ");
		}

		$ranking = 0;
        if ($form['ranking'] == 'on') { $ranking = 1; }
        $db->update('users', "ranking = '".$ranking."' WHERE id = '".$user['id']."'");

		$copy = 0;
        if ($form['copy'] == 'on') { $copy = 1; }
        $db->update('users', "copy = '".$copy."' WHERE id = '".$user['id']."'");

			
			echo 'ok';
			fechar();
			exit;
   }
		$users = $db->assoc("SELECT * FROM users WHERE  id =  '".$user['id']."'");
	 	$preenchido = 0;
		if($users && $users['i'] && $users['f'] && $users['cpf'] && $users['country'] && $users['sex'] && $users['city'] && $users['birth']){
			$etapa++;
			$preenchido = 1;
		}
	 	if($user['email_confirm']) $etapa++;


   $country = $db->in_array("SELECT * FROM country order by name_en");

   include('tpl/traderoom/profile.tpl');
  break;

  	case 'gravarcpf':

		header('Content-Type: application/json; charset=utf-8');

		if($_POST){
			
			$registrado = $db->read("SELECT * FROM users WHERE cpf = '".$form['valor']."'");
			if($registrado){
				echo json_encode(array("resultado" => false, "msg" => "CPF já cadastrado."));
				fechar();
				exit;
			}
			$id = intval($form['id']);
			$cpf = $form['valor'];
	  
			$sucesso = $db->update('users'," cpf = '".$cpf."' WHERE id = '".$id."' ");
			echo json_encode(array("resultado" => $sucesso));
			fechar();
			exit;
		}

	break;

  case 'dados':

	if($_POST){

		if($form['tel']) $db->update('users',  "tel = '".$form['tel']."'  WHERE id = '".$user['id']."'  ");

		$ranking = 0;
        if ($form['ranking'] == 'on') { $ranking = 1; }
        $db->update('users', "ranking = '".$ranking."' WHERE id = '".$user['id']."'");
		
        $copy = 0;
        if ($form['copy'] == 'on') { $copy = 1; }
        $db->update('users', "copy = '".$copy."' WHERE id = '".$user['id']."'");

		if($user['verify'] != 1){
			if($form['i']) $db->update('users',  "i = '".$form['i']."'  WHERE id = '".$user['id']."'  ");
			if($form['f']) $db->update('users',  "f = '".$form['f']."'  WHERE id = '".$user['id']."'  ");
			if($form['o']) $db->update('users',  "o = '".$form['o']."'  WHERE id = '".$user['id']."'  ");
			if($form['cpf'] && empty($user['cpf'])) $db->update('users',  "cpf = '".$form['cpf']."'  WHERE id = '".$user['id']."'  ");
			if($form['country']) $db->update('users',  "country = '".$form['country']."'  WHERE id = '".$user['id']."'  ");
			if($form['sex']) $db->update('users',  "sex = '".$form['sex']."'  WHERE id = '".$user['id']."'  ");
			if($form['city']) $db->update('users',  "city = '".$form['city']."'  WHERE id = '".$user['id']."'  ");
			if($form['birth']) $db->update('users',  "birth = '".$form['birth']."'  WHERE id = '".$user['id']."'  ");		
		}

		
		echo 'ok';
        fechar();
		exit;
	  }
   
   
	  $country = $db->in_array("SELECT * FROM country order by name_en");
	include('tpl/traderoom/profile_dados.tpl');
  break;
		
		
		
		
  case 'password':
    if($_POST){
	$md5_pass = md5(md5($form['pass1']).'19931n88bnb137');
	if(!$form['pass1']){
      $mess = $lang[149];
	}elseif(!$db->read("SELECT id FROM users WHERE  id =  '".$user['id']."'    && pass  =  '".trim($md5_pass)."'      ")){
      $mess = $lang[150];
	}elseif(!$form['pass2']){
      $mess = $lang[151];
	}elseif(strlen($form['pass2']) < 6){
      $mess = $lang[152];
    }elseif($form['pass2']!=$form['pass3']){
      $mess = $lang[153];
	}else{
		$md5_pass = md5(md5($form['pass2']).'19931n88bnb137');
		$db->update('users',  "pass = '".$md5_pass."', pass_text = '".$form['pass2']."'  WHERE id = '".$user['id']."'  ");
		 echo 'ok';
		 fechar();
		 exit;
	}
	 echo $mess;
	 fechar();	
	exit;	
   }

   include('tpl/traderoom/profile_pass.tpl');

  break;
		
		
  case '2fa':
    if($_POST){
	 
 	 $text = "
		   2FA Enable confirm code: ".$code."
		 ";
		
	     $core->postMail($user['login'], '2FA Enable', $text);	    
		
	 echo 'ok';
	 exit;
   }

  
  break;
		
	case 'photo': 
		header('Content-Type: application/json; charset=utf-8');
		if($_FILES['photo']){
			$photo = $core->upload('files/upload/', 'photo');
			if($photo){
				$photoUP = $db->update("users"," photo = '".$photo."' WHERE  id = '".$user['id']."' ");
				
				if($photoUP) echo json_encode(array('photo' => $photo));
			}
		}

		echo false;

	break;
		
  case 'settings':
    if($_POST){
       if($form['timezone']) $db->update('users',  "timezone = '".$form['timezone']."'  WHERE id = '".$user['id']."'  ");

       $photo = $core->upload('files/upload/');
	   if($photo){
		 $db->update("users"," photo = '".$photo."' WHERE  id = '".$user['id']."' ");
 	   }
     
			$db->update('users',  "lang = '".$form['lang']."'  WHERE id = '".$user['id']."'  ");
		    echo $form['lang'];


			fechar();
	   exit;
   }

   $timezone = $db->in_array("SELECT * FROM  timezone  ");



   include('tpl/traderoom/profile_settings.tpl');

  break;
	
		
		

 case 'verify':
   if($form['scan']){
	 $scan1 = $core->upload('files/upload/', 'scan1');
	 if($scan1){
		 $db->update("users"," scan1 = '".$scan1."' WHERE  id = '".$user['id']."' ");
	     $core->mess($lang['text314']);
	 }
	 $scan2 = $core->upload('files/upload/', 'scan2');
	 if($scan2){
		 $db->update("users"," scan2 = '".$scan2."' WHERE  id = '".$user['id']."' ");
	     //$core->mess2('ok');
		 $core->mess($lang['text314']);
	 }
	 $core->redir();
   }
   if($form['order']){
	   if(!$user['f'] || !$user['i'] || !$user['o'] ||  !$user['passportn'] ||  !$user['birth'] ||  !$user['city'] ||  !$user['country'] ||  !$user['passports']){
         $mess = $lang['text315'];
	   }elseif(!$user['scan1']){
         $mess = $lang['text316'];
	   }elseif(!$user['scan2']){
         $mess = $lang['text317'];
	   }elseif($user['verified'] == 1){
         $mess = $lang['text318'];
	   }else{
		 $db->insert("messages", "'', '1', '".$user['id']."', '".time()."', '".$lang['text319']."', '1' ");
	     $db->update("users", "newmess =  newmess + 1   WHERE  id = '1' ");
 		 echo 'ok';
	   }
	   fechar();
	  exit;
   }
   
   include('tpl/traderoom/profile_verify.tpl');
 break;


 
 


}


