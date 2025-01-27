<?php
if(!$url[2]) $url[2] = 'default';
// $url[3]
global $conf;

switch($url[2]){
  
  default:
  case 'relatorios':
    global $core, $conf;    
    
    $table = $db->in_array("SELECT  partner_stat.user_id, partner_stat.regs, partner_stat.deposits, partner_stat.deposito, partner_stat.saques, partner_stat.lots, partner_stat.profit, partner_stat.refprofit,  users.login, users.rev FROM partner_stat, users WHERE partner_stat.user_id = users.id");
    $dados = [];
    foreach($table as $usuario){
      foreach($usuario as $key => $value){
        //if(is_null($value)){
          if($key != "login" && $key != "user_id" && $key != "rev") {
            $dados[$usuario['user_id']][$key] += (float)$value;
          }else{
            $dados[$usuario['user_id']][$key] = $value;
          }
          if($key === "rev" ) $dados[$usuario['user_id']][$key] = ($value > 0) ? $value : $conf['porcentagem'];
    //    }
      }
      $saques = $db->read("SELECT sum(amount_pay) FROM payout WHERE user_id = '".$usuario['user_id']."' && status = '1' && tipo = 'afiliado'");
      $dados[$usuario['user_id']]['saques'] = ($saques) ? $saques : '0';
      $registrados = $db->in_array("SELECT referal_id FROM referals WHERE user_id = '".$usuario['user_id']."'");
      $ids = [];
      if($registrados) {
        foreach($registrados as $registrados) $ids[] =  $registrados['referal_id'];
        $ativos = $db->read("SELECT count(*) FROM users WHERE id in (".implode(',', $ids).") AND depositante = '1'");
        $dados[$usuario['user_id']]['ativos'] = ($ativos) ? $ativos : '0';
      }else{
        $dados[$usuario['user_id']]['ativos'] = 0;
      }

  }

    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/afiliados/relatorios.tpl');
    include('tpl/dashboard/footer.tpl');
  break;

  case 'retiradas':
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/afiliados/retiradas.tpl');
    include('tpl/dashboard/footer.tpl');

  break;

  case 'ver_afiliado':
    $usuario = $db->assoc("SELECT * FROM users WHERE id = '".intval($url[3])."'");
    $inicio = "";
    $final = "";
    
      if(!is_null($_POST['inicio'])) $inicio = " AND time_end > '".strtotime($_POST['inicio']." 00:00:00")."'";
      if(!is_null($_POST['final'])) $final = " AND time_end < '".strtotime($_POST['final']." 23:59:59")."'";

    $registrados = $db->in_array("SELECT referal_id FROM referals WHERE user_id = '".$usuario['id']."'");
    $registrado = "";
    if($registrados) {
      foreach($registrados as $registrados) $ids[] =  $registrados['referal_id'];
      $registrado = $db->in_array("SELECT * FROM users WHERE id in (".implode(',', $ids).") && level = 0");

    }else{ 
      $registrado = array();
    }
    include('tpl/dashboard/afiliados/ver_afiliado.tpl');

  break;

  case 'retiradas_list':
	  
    if($form['q']) $q .= " && users.login like '%".$form['q']."%'  ";			
 		
     $table = $db->in_array("SELECT  payout.*,  users.* FROM payout, users WHERE payout.user_id = users.id && payout.tipo = 'afiliado' && status = '0' && payout.marketing = '0' $q    order by payout.id desc ");

    include('tpl/dashboard/afiliados/retiradas_list.tpl');
  break;
		
		
  case 'retiradas_history':
   	  
    if($form['q']) $q .= " && users.login like '%".$form['q']."%'  ";	
		
     $table = $db->in_array("SELECT  payout.*,  users.login FROM payout, users WHERE payout.user_id = users.id && payout.tipo = 'afiliado' && status != '0' && payout.marketing = '0' $q    order by payout.id desc ");

    include('tpl/dashboard/afiliados/retiradas_history.tpl'); 
  break;

  case 'configuracoes':
    if($_POST){
      foreach($form as $k => $v){
        $id = $db->read("SELECT id FROM conf WHERE k = '". $k."' ");	 
             $arr = array(
             'v'  => $v,
             );
             $db->update('conf', $arr, " id = '".$id."' ");
             if($k === "diasAfiliado"){
              $tempo = time()  + 86400 * $v;

              $db->update('users', "`partner_month` = '".$tempo."' WHERE `partner_balance` < 1");
             }
      }
 
     echo 'ok'; 
     fechar();
         exit;
    }	
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/afiliados/configuracoes.tpl');
    include('tpl/dashboard/footer.tpl');

  break;



}
function depositosQ($id) {
  global $db;
  $depositos = $db->read("SELECT count(amount) FROM payin WHERE user_id='".$id."' && status='1' && marketing='0'");
  if(!$depositos) $depositos = 0;
  return (float) $depositos;
}

function depositos($id) {
  global $db;
  $depositos = $db->read("SELECT sum(amount) FROM payin WHERE user_id='".$id."' && status='1' && marketing='0'");
  if(!$depositos) $depositos = 0;
  return (float) $depositos;
}


function saques($id) {
  global $db;
  $saques = $db->read("SELECT sum(amount_pay) FROM payout WHERE user_id='".$id."'");
  if(!$saques) $saques = 0;
  return (float) $saques;
}

function operacoes($id, $inicio, $final) {
  global $db;
  $op = $db->read("SELECT sum(profit) FROM lots WHERE user_id='".$id."' && demo = '0' && marketing='0'".$inicio.$final);
  if(!$op) return 0;
  return (float) $op;
}