<?
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

 $redis = new Redis();
 $redis->connect('localhost', 6379); 
 
 
function fechar() {
   global $redis, $db;
   $redis->close();
   $db->connectClose();
}

// Logout
if($url[0] == 'traderoom' && !$user  && $form['json'] == 1){
   echo json_encode(array('logout' => 1)); 
   exit;
}

// Access users
if($url[0] == 'traderoom' && !$user){
   $core->redir("/");
}
// Access users Mob
if($url[0] == 'mob' && !$user){
   $core->redir("/login");
}
if($url[0] == 'admin' && $user['level'] != 8){
   $core->redir("/");
}

if($url[0] == 'dashboard' && $user['level'] != 8){
   $core->redir("/");
}



// Demo account support
if($user['demo'] == 1){
	$demo = 1;
}else{
	$demo = 0;
}


 

// Lang
//if(!$url[0]) $url[0] = 'en';
$lang_list = $db->in_array("SELECT * FROM lang_list ");

if(isset($get['ref']) || is_array($get['ref']) || $get['ref'] !== null) {
   if($get['ref']){
      $core->redir('/partner/'.$get['ref']);
   }
}

switch($url[0]){


 
  default:
   
  break;

  
  case 'traderoom':
     $title = 'Trading platform';
 	
	// Categories
	// $currcategory[0] = 'Crypto';
	// $currcategory[1] = 'Stock';
	// $currcategory[2] = 'Forex';
	// $currcategory[3] = 'Options';	
   // $currcategory[4] = 'Index';	
	// $currcategory[5] = 'Commodities';	
   $currcategory[7] = 'Opções';	
   $currcategory[8] = 'Crypto';	
   $currcategory[9] = 'Stock';



     // Color interface
     if($user['color'] == 1){
		 $tpl_color = '_white'; 
	 }else{
		 $tpl_color = ''; 
	 }
  break;
 
  case 'admin':

   break;
}

// Page construct
if(!$url[0]) $url[0] = 'default';
if(is_dir('pages/'.$url[0]) && $url[0]){
    if(!$url[1]) $url[1] = 'default';
    if (file_exists('pages/'.$url[0].'/'.$url[1].'.php')){
	    include('pages/'.$url[0].'/'.$url[1].'.php');
	}else{
        include('pages/main/404.php');
	}
}elseif(file_exists('pages/main/'.$url[0].'.php')){
	include('pages/main/'.$url[0].'.php');
}else{ 
    include('pages/main/pages.php');
}


$redis->close();