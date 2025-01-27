<?

if($user){
  $core->redir("/traderoom");
} 

include('classes/login.class.php');
$title = $lang[1];	 

$email = $core->filter($_COOKIE['email']);	
if(DEMO){
	if(!$email) $email =  'admin';  
	if($email == 'admin') $pass =  'admin';  
}

$login = new Login();
			


switch($url[1]){


  default: 
    if($_POST) $login->login($form['email'], $form['pass'], $form['save']);
	include('tpl/main/header_login.tpl');
    include('tpl/main/login.tpl');	
  break;


  case '2fa':
	if($_POST) $login->login2fa($form['code']);	
    include('tpl/main/header_login.tpl');
    include('tpl/main/login_2fa.tpl');
  break;

}	





 

 

 