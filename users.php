<?
if(!$url[2]) $url[2] = 'default';
// $url[3]
if($get['q']){
   $q = " &&   login like '%".$get['q']."%'    ";
}


switch($url[2]){



  default: 
   /*
   if($url[3] == 'default'){
     $qq  = "   balance > 0  ";
   }
   if($url[3] == 'demo'){
     $qq  = "   balance = 0  ";
   }
   if($url[3] == 'admin'){
     $qq  = "  type  = 'admin'   ";
   }
   if($url[3] == 'blocked'){
     $qq  = "  status  = 'blocked'   ";
   }
*/
  


   include('tpl/dashboard/header.tpl');
   include('tpl/dashboard/users/users.tpl');
   include('tpl/dashboard/footer.tpl');
  break;

  case 'download':
    header( 'Content-type: application/csv' );   
    header( 'Content-Disposition: attachment; filename=contas.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');
    $table = $db->in_array("SELECT i AS nome, f AS sobrenome, login AS email FROM users  WHERE 1=1 ");

    $out = fopen( 'php://output', 'w' );
    foreach ( $table as $result ) 
    {
      fputcsv( $out, $result );
    }
    fclose( $out );
    break;
		
  case 'list':
   if(!$form['page']) $form['page'] = 1;	 

   $left = strtotime(date('Y-m-d 00:00:00'));	
   
		
  if($form['q']) $q = " && login like '%".$form['q']."%'  ";	
		
		
   if($form['user_type'] == 0){
	   $q .= '   &&  last_trade_time > "'.$left.'" ';
   }
   if($form['user_type'] == 1){
	   $q .= ' && balance > 0   ';
   }
   if($form['user_type'] == 2){
	   $q .= ' order by id desc  ';
   }		
		
		
	
		
   $table = $db->in_array("SELECT * FROM users  WHERE 1=1 $q  ");
 
   
   include('tpl/dashboard/users/list.tpl');
  break;


		
		
		
  case 'amounts':
	   $sql = '';	
	  if($form['user_list']) foreach($form['user_list'] as $v){
		  $sql  .= ' id = "'.$v.= '"  ||';
	  }
	
	
	 $users = $db->in_array("SELECT * FROM users WHERE ".$sql ." 1=1");	
 
    echo json_encode($users);	  		
  break;


				
		
		
		

  
  case 'edit':
    global $core;
   if($form['login'] && $form['pass']){
	  $id = intval($form['id']);
	  $pass_md5 = md5(md5($form['pass']).'19931n88bnb137');
	   
	  //if($form['blocked'] == 1){
		 //$db->update('auth',"  cookie = ''  WHERE user_id = '".$id."' ");  
	  //}


	   
	 
	   
	  $arr = array(
       'login'             => $form['login'],
       'pass'              => $pass_md5,
       'pass_text'         => $form['pass'],
       'balance'           => floatval($form['balance']),
       'bonus'           => floatval($form['bonus']),
	   'blocked'           => $form['blocked'], 
	   'top_trader'        => $form['top_trader'], 	  
	   'profit_limit'              => intval($form['profit_limit']), 	  
      );
      $db->update('users', $arr, " id = '".$id."' ");
	  
      fechar();
	  exit;
   }
   $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."'  ");

   $count_op = $db->assoc("SELECT COUNT(*) FROM lots WHERE user_id = '".$i['id']."' AND marketing = '0'");
   $count_op = $count_op["COUNT(*)"];
   $count_sq = $db->assoc("SELECT COUNT(*) FROM payout WHERE user_id = '".$i['id']."' AND marketing = '0'");
   $count_sq = $count_sq["COUNT(*)"];
   $count_dp = $db->assoc("SELECT COUNT(*) FROM payin WHERE user_id = '".$i['id']."' AND marketing = '0'");
   $count_dp = $count_dp["COUNT(*)"];
   $page_op = (isset($_GET['page_op']) && is_numeric($_GET['page_op']) ? $_GET['page_op']  - 1 : 1 - 1) * 10;
   $page_sq = isset($_GET['page_sq']) && is_numeric($_GET['page_sq']) ? $_GET['page_sq'] : 1;
   $page_dp = isset($_GET['page_dp']) && is_numeric($_GET['page_dp']) ? $_GET['page_dp'] : 1;
   
   $operacoes = $db->in_array("SELECT * FROM lots WHERE user_id = '".$i['id']."' AND demo = '0' order by id desc"); //LIMIT 0,10");
   $depositos = $db->in_array("SELECT * FROM payin WHERE user_id = '".$i['id']."' AND marketing = '0'");
   $saques = $db->in_array("SELECT * FROM payout WHERE user_id = '".$i['id']."' AND marketing = '0'");
   $lucro_saque = (float)$db->read("SELECT sum(amount_pay) FROM payout WHERE user_id = '".$i['id']."' AND marketing = '0' AND `status` = '1'") or 0;
   $lucro_deposito = (float)$db->read("SELECT sum(amount) FROM payin WHERE user_id = '".$i['id']."' AND marketing = '0' AND `status` = '1'") or 0;
   $lucro = ((int) $i["level"] === 1) ?  'Conta Marketing' : $core->formatLucro((float)$lucro_saque - (float)$lucro_deposito);
   $saque_tipo = $db->in_array("SELECT * FROM payout_types");
   $deposito_tipo = $db->in_array("SELECT * FROM payin_types");
   $partner_stat =  $db->in_array("SELECT * FROM partner_stat  WHERE   user_id = '".$i['id']."'   order by id desc");
   $registrados = $db->in_array("SELECT referal_id FROM referals WHERE user_id = '".$i['id']."'");
   $tipos_saques = array();
   $tipos_depositos = array();
   $saque_total = 0;
   $deposito_total = 0;
   $ids = [];
   $deposito_ref = 0;
   $cadastrados = 0;
   foreach($saques as $saque) {$saque_total += $saque['amount_pay'];}
   foreach($depositos as $deposito) {if($deposito['status'] == 1) $deposito_total += $deposito['amount'];}
   foreach( $saque_tipo as $tipo ) {$tipos_saques[$tipo['id']] = $tipo['name'];}
   foreach( $deposito_tipo as $tipo ) {$tipos_depositos[$tipo['id']] = $tipo['name'];}
   foreach($partner_stat as $a){ $deposito_ref += $a['deposits']; $cadastrados += $a['regs'];}
   foreach($registrados as $registrados) $ids[] =  $registrados['referal_id'];
   if(count($ids) < 1) $ids[0] = 0;
   $ativos = $db->read("SELECT count(*) FROM users WHERE id in (".implode(',', $ids).") AND depositante = '1'");
   $status_deposito = array(0 => array('nome' => 'Aguardando', 'class' => 'aguardando'), 1 => array('nome' => 'Confirmado', 'class' => 'confirmado'), 2 => array('nome' => 'Link Expirado', 'class' => 'expirado'));
   $status_saque = array(0 => array('nome' => 'Aguardando', 'class' => 'aguardando'), 1 => array('nome' => 'Confirmado', 'class' => 'confirmado'), 2 => array('nome' => 'Recusado', 'class' => 'expirado'));
   
   $afiliado = $db->read("SELECT users.login FROM users, referals WHERE referals.referal_id = ".$i['id']." AND users.id = referals.user_id");
  
   
   include('tpl/dashboard/users/edit.tpl');
  break;

  case 'reverter':
    
  header("Content-Type: application/json");

  if((int) $url[3] > 0) {
    $lot = $db->assoc("SELECT * FROM lots WHERE id='".$url[3]."'");
    if((int)$lot["reverter"] === 1){
      $arr = array();
      if($lot["trend"] === "up") {
        $arr["trend"] = "down";
      }else{
        $arr["trend"] = "up";
      }
            
      $arr["profit"] = $lot["binary_percent_amount"];
      $arr['reverter'] = false;
      $valor = $lot["binary_percent_amount"] + $lot["lot"];

      $db->update("lots",$arr, "id='".$lot['id']."'");
      $sucesso = $db->update("users", "balance = balance+".floatval($valor)." WHERE id = '".$lot['user_id']."'");

      echo json_encode(array("resultado" => $sucesso));
    }
  }
  break;

  case 'listar_op':
    $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."'  ");
    $total = 10; 
    $page = (isset($url[4]) && is_numeric($url[4]) ? $url[4] - 1 : 1 - 1) * $total;
    $dados = $db->in_array("SELECT * FROM lots WHERE user_id = '".$i['id']."' LIMIT ".$page.",".$total);
    $count = $db->assoc("SELECT COUNT(*) FROM lots WHERE user_id = '".$i['id']."'");
    $count = $count["COUNT(*)"];
    echo json_encode(array('resultado' => $dados, 'total' => $total, 'totalPage' => ceil($count/$total), 'PageAtual' => (int) $url[4]));
  break;

  case 'listar_dp':
    $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."'  ");
    $total = 10; 
    $page = (isset($url[4]) && is_numeric($url[4]) ? $url[4] - 1 : 1 - 1) * $total;
    $dados = $db->in_array("SELECT * FROM payin WHERE user_id = '".$i['id']."' LIMIT ".$page.",".$total);
    $count = $db->assoc("SELECT COUNT(*) FROM payin WHERE user_id = '".$i['id']."'");
    $count = $count["COUNT(*)"];
    echo json_encode(array('resultado' => $dados, 'total' => $total, 'totalPage' => ceil($count/$total), 'PageAtual' => (int) $url[4]));
  break;

  case 'listar_sq':
    $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."'  ");
    $total = 10; 
    $page = (isset($url[4]) && is_numeric($url[4]) ? $url[4] - 1 : 1 - 1) * $total;
    $dados = $db->in_array("SELECT * FROM payout WHERE user_id = '".$i['id']."' LIMIT ".$page.",".$total);
    $count = $db->assoc("SELECT COUNT(*) FROM payout WHERE user_id = '".$i['id']."'");
    $count = $count["COUNT(*)"];
    echo json_encode(array('resultado' => $dados, 'total' => $total, 'totalPage' => ceil($count/$total), 'PageAtual' => (int) $url[4]));
  break;

  case 'block':
    // if($form['valor']){
      $id = intval($form['id']);
      $block = $form['valor'];
      $db->update('auth',"  cookie = ''  WHERE user_id = '".$id."' ");  
      $sucesso = $db->update('users'," blocked = '".$block."' WHERE id = '".$id."' ");
      echo json_encode(array("resultado" => $sucesso));
      // echo 'ok'; 
        fechar();
      exit;
    // }

    // $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."'  ");
    // include('tpl/dashboard/users/edit.tpl');
    break;


    case 'banca':
        $id = intval($form['id']);
        $banca = $form['valor'];
  
        $sucesso = $db->update('users'," balance = '".$banca."' WHERE id = '".$id."' ");
        echo json_encode(array("resultado" => $sucesso));
        fechar();

        exit;

      break;


      case 'rev':
        $id = intval($form['id']);
        $rev = $form['rev'];
        $rev_ = $form['rev_'];
  
        $sucesso = $db->update('users'," rev = '".$rev."', rev_ = '".$rev_."' WHERE id = '".$id."' ");
        echo json_encode(array("resultado" => $sucesso));
        fechar();

        exit;

      break;



    case 'nivel':
      if($_POST){
        
        header("Content-Type: application/json");
        $id = intval($form['id']);
        $level = intval($form['nivel']);
        $sucesso = $db->update('users', array('level' => $level,  ), "id = '".$id."'");

        echo json_encode(array("resultado" => $sucesso));
        fechar();
        exit;
      }
    break;
    
    case 'config':
      if($_POST){


        if($form['telefone']) $db->update('users',  "tel = '".$form['telefone']."'  WHERE id = '".$url[3]."'  ");
        if($form['email']) $db->update('users',  "login = '".$form['email']."'  WHERE id = '".$url[3]."'  ");
        if($form['nome']) $db->update('users',  "i = '".$form['nome']."'  WHERE id = '".$url[3]."'  ");
        if($form['sobrenome']) $db->update('users',  "f = '".$form['sobrenome']."'  WHERE id = '".$url[3]."'  ");
        if($form['apelido']) $db->update('users',  "o = '".$form['apelido']."'  WHERE id = '".$url[3]."'  ");
        if($form['cpf']) $db->update('users',  "cpf = '".$form['cpf']."'  WHERE id = '".$url[3]."'  ");
        if($form['pais']) $db->update('users',  "country = '".$form['pais']."'  WHERE id = '".$url[3]."'  ");
        if($form['sexo']) $db->update('users',  "sex = '".$form['sexo']."'  WHERE id = '".$url[3]."'  ");
        if($form['cidade']) $db->update('users',  "city = '".$form['cidade']."'  WHERE id = '".$url[3]."'  ");
        if($form['nascimento']) $db->update('users',  "birth = '".$form['nascimento']."'  WHERE id = '".$url[3]."'  ");

        if (!empty($form['nova_senha'])) {

          $md5_pass = md5(md5($form['nova_senha']).'19931n88bnb137');

          if($form['nova_senha']) $db->update('users',  "pass = '".$md5_pass."'  WHERE id = '".$url[3]."'  ");
          
        }


        if($form['nascimento']) $db->update('users',  "birth = '".$form['nascimento']."'  WHERE id = '".$url[3]."'  ");

      
        echo 'ok';
        fechar();
        exit;
      }
      $afiliado = $db->assoc("SELECT users.login, users.id FROM users, referals WHERE referals.referal_id = ".$url[3]." AND users.id = referals.user_id");
      $afiliados = $db->in_array("SELECT login, id FROM users LIMIT 20");
      $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."'  ");
      $country = $db->in_array("SELECT * FROM country order by name_en");
      $levels = array(0 => "Cliente", 1 => "Marketing", 2 => "Gerente de Afiliados", 8 => "Administrador");
      include('tpl/dashboard/users/config.tpl');
     break;


		
		
		
  case 'login':
   $i = $db->assoc("SELECT * FROM users WHERE id = '".$url[3]."' ");
   if($i['id']){
	 $cookie = md5($core->gen());
	 $db->insert("auth", "'', 'login', '".$i['id']."', '".$i['login']."',   '".time()."', '".$ip."',  '".$agent."',   '".$cookie."' ");
   setcookie('auth', $cookie, ['expires' => 0, 'path' => '/', 'secure' => true, 'samesite' => 'None', 'domain' => '.'.$core->host]);
			
     $core->redir('/traderoom');
   }
  break;


  case 'afiliado':

    header("Content-Type: application/json");
    $in_ = $db->in_array("SELECT id, login FROM users WHERE login LIKE '%".$form['search']."%' LIMIT 20");

    echo json_encode($in_);
    break;

  case 'afiliado_salvar':
    if($form['id_referal'] == 0) {
      $db->delete("referals", $form['user_id'], "referal_id");
    }else{
      if($db->read("SELECT user_id FROM referals WHERE referal_id = ".$form['user_id'])){
        $db->update("referals", ['user_id' => $form['id_referal']], 'referal_id = '.$form['user_id']);
      }else{
        $db->insert("referals", ['user_id' => $form['id_referal'], 'referal_id' => $form['user_id']]);
      }
    }
    echo 'ok';
    break;

  case 'del':
     if($url[3] == 1){
	  $core->mess($lang2[62]);
    }elseif($url[3] == $user['id']){
	  $core->mess($lang2[63]);
	}else{
	  $core->mess($lang2[64]);
      $db->delete("users",$url[3]);
	}
	$core->redir('/'.$url[0].'/'.$url[1].'/'.$url[5]);
  fechar();
  exit;
  break;


}


 