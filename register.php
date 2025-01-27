<?
$title = $lang[5];

if($user){
  $core->redir("/");
} 



if($_POST){
  //echo '<pre>'; print_r($_POST); echo '</pre>';
  include('classes/register.class.php');
  $reg = new Register();
  echo $reg->result;
  fechar();
  exit;
}




include('tpl/main/header_login.tpl');
include('tpl/main/register.tpl');




