<?	   
 
$title = $lang[7];
 
$email = $core->filter($_COOKIE['email']);

if($user){
  $core->redir("/traderoom");
} 
 
  
switch($url[1]){
	
   default:
    if($_POST){
     if(!$form['pass']){
	   $result = $lang[22];
	 }elseif($_SESSION['captcha']  != $form['pass']){
       $result = $lang[23];
	    unset($_SESSION['captcha']);
     }elseif(!$db->read("SELECT id FROM users WHERE login = '".$form['email']."'  ")){
       $result = $lang[24];
     }else{
	   $code = $core->gen(12, 'int');
	   $_SESSION['remind'] = $code;
	   send1($form['email'], $code);
	   $_SESSION['remind_email'] = $form['email'];
       echo 'ok';
	   fechar();
	   exit;
	  }
      echo  $result;
	  fechar();
	  exit;
    }
	include('tpl/main/header_login.tpl');
    include('tpl/main/remind.tpl');

   break;




   case '2':
    if($_POST){
	  if(!$form['code2']){
     	$result  =  $lang[22];
	  }elseif($_SESSION['remind'] != $form['code2']){
	    $result  = $lang[25];
	  }else{
    	$email = $core->filter($_SESSION['remind_email']);
		$new_pass = $core->gen(9, 'text');
		$md5_pass = md5(md5($new_pass).'19931n88bnb137');
		$db->update('users', "pass = '".$md5_pass."', pass_text='".$new_pass."'  WHERE login =  '".$email."'  ");
		send2($email, $new_pass);
		unset($_SESSION['remind_email']);
		unset($_SESSION['remind']);
		$core->mess($lang[26]);
		echo 'ok';
        fechar();
		exit;
	  }
	  echo $result;
	  fechar();
	  exit;
	}
	include('tpl/main/header_login.tpl');
    include('tpl/main/remind2.tpl');

   break;
}


function send1($email, $code){
    global $core, $conf, $url;
    	
	$text = "
		 
		   Olá,

		   Esqueceu a senha? Não tem problema.

		   Basta digitar o código abaixo na tela de recuperação.

		   Código: ".$code."

		   Duvidas?
		   Mande um email para: suporte@".$core->host."
		 
		 ";
	$core->postMail($email, "Recuperar Senha - ".$conf['sitename'], $text);
}


function send2($email, $pass){
    global $core, $conf, $url;
    
	$text = "
		 
		   Olá,
		   
		   Estes são os seu dados de acesso, não compartilhe com ninguém.

		   Login: ".$email."
		   Pass:  ".$pass."

		   Os seus dados são criptografados e armazenados com seguraça.
		 
		 ";
	$core->postMail($email, "Sua nova senha - ".$conf['sitename'], $text);
}