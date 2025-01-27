<?

switch($url[2]){// $url[3]


  default:
    if($form['trader']){

      header('Content-Type: application/json; charset=utf-8');
      if($user['id'] == $form['trader']) {
        header('HTTP/1.1 205 Reset Content');
        echo json_encode(['msg' => "Não é possivel copiar seu proprio usuário."]);
        fechar();
        exit;
      }

      if($db->read("SELECT * FROM copy_traders WHERE user_id='".$user['id']."' AND trader_id='".$form['trader']."'")){
        header('HTTP/1.1 205 Reset Content');
        echo json_encode(['msg' => "Usuário copiado/aguardando confirmação."]);
        fechar();
        exit;
      }
      
      $arr = array(
        'user_id'           => $user['id'],
        'trader_id'         => $form['trader'],
        'amount'            => $form['valor'],
        'status'            => 0
      );
      $db->insert('copy_traders', $arr);
      header('HTTP/1.1 200 OK');
      echo json_encode(['status' => true]);
      fechar();
      exit;
    }	 

    $table = $db->in_array("SELECT * FROM users  WHERE `copy` = '1' order by 	`profit_percent`	desc LIMIT 0,20");

    $trader = $db->in_array("SELECT * FROM users  WHERE `copy` = '1' order by 	`profit_percent`	desc LIMIT 1");

    $meus_ = $db->in_array("SELECT trader_id, `status` FROM copy_traders WHERE user_id='".$user['id']."'");

    $meus = [];

    foreach($meus_ as $value){
      $meus[$value['trader_id']] = $value['status'];
    }

    include('tpl/traderoom/copy.tpl');
  break;

  case 'filtro':

    if(!$form['page']) $form['page'] = 1;		  
    if($form['q']) $q .= " users.f like '%".$form['q']."%' or users.i like '%".$form['q']."%' ";	

    $table = $db->in_array("SELECT * FROM users  WHERE  $q AND `copy` = '1' order by 	`profit_percent`	desc LIMIT 0,20");
		

    include('tpl/traderoom/copy_list_user.tpl');
 
   break;

  case 'meuscopys':
    $table = $db->in_array("SELECT users.o, users.f, users.i, users.profit, users.profit_percent, users.id AS user_id, copy_traders.id, copy_traders.status, copy_traders.amount FROM users, copy_traders  WHERE copy_traders.trader_id = users.id  && copy_traders.user_id = '".$user['id']."'");	

    $table2 = $db->in_array("SELECT users.o, users.f, users.i, users.profit, users.profit_percent, users.id AS user_id, copy_traders.id, copy_traders.amount FROM users, copy_traders  WHERE copy_traders.user_id = users.id  && copy_traders.trader_id = '".$user['id']."' && status = '1'");
    

   include('tpl/traderoom/copy_meus.tpl');

  break;

  case 'solicitacoescopy':
    
    $table = $db->in_array("SELECT users.o, users.f, users.i, users.profit, users.profit_percent, users.id AS user_id, copy_traders.id, copy_traders.amount FROM users, copy_traders  WHERE copy_traders.user_id = users.id  && copy_traders.trader_id = '".$user['id']."' && status = '0'");

    include('tpl/traderoom/copy_solicitacoes.tpl');
 
   break;

  case 'perfil':
    header('Content-Type: application/json; charset=utf-8');
    if($form['id']):
      $id = $form['id'];

      $status = $db->read("SELECT `status` FROM copy_traders WHERE user_id = '".$user['id']."' AND trader_id = '".$id."' ");
      
      $perfil =  $db->assoc("SELECT * FROM users WHERE id = '".$id."' && copy = '1'");
      
      if($perfil) :

        echo json_encode([
          'nome' => $perfil['i']." ".$perfil['f'],
          'apelido' => $perfil['o'],
          'imagem' => ($perfil['photo']) ? $perfil['photo'] : '../img/perfil.jpg',
          'seguidores' => seguidores($id),
          'negociacoes' => negociacoes($id),
          'negociacoes_lucro' => negociacoes_lucro($id),
          'lucro' => lucro($id),
          'valorMax' => valorMax($id),
          'valorMin' => valorMin($id),
          'status' => $status,
        ]);
        fechar();
        exit;

      else :
        header('HTTP/1.1 205 Reset Content');
        echo json_encode(['msg' => 'Usuario não encontrado!']);
        fechar();
        exit;
      endif;
    endif;
    break;

  case 'rejeitar':
    if($form['id']) {
     // header('Content-Type: application/json; charset=utf-8');
      header('HTTP/1.1 200 OK');
      $arr = array(
        'status' => '2'
      );
      $db->update('copy_traders', $arr, " user_id = '".$form['id']."' && trader_id = '".$user['id']."'");
      echo json_encode(['status' => true]); //echo 'ok';
      exit;
    }

    break;
  
  case 'aceitar':
    if($form['id']) {
     // header('Content-Type: application/json; charset=utf-8');
      header('HTTP/1.1 200 OK');
      $arr = array(
        'status' => '1'
      );
      $db->update('copy_traders', $arr, " user_id = '".$form['id']."' && trader_id = '".$user['id']."'");
      echo json_encode(['status' => true]); //echo 'ok';
      fechar();
      exit;
    }
    break;
  
  case 'salvar': 
    if($form['id'] && $form['valor'] >= 0){

      header('Content-Type: application/json; charset=utf-8');
      header('HTTP/1.1 200 OK');
      $arr = array(
      'amount'            => $form['valor'],
      );
      $db->update('copy_traders', $arr, " id = '".$form['id']."'");
      echo json_encode(['status' => true]);
      fechar();
      exit;
    }	 
    break;

  case 'deletar': 
    switch($url[3]) :
      case 'seguindo':
          if($form['id'] && $user){
            $db->query("DELETE FROM copy_traders WHERE id = '".$form['id']."' && user_id = '".$user['id']."'");
            echo 'ok';
            fechar();
            exit;
          }
        break;
      case 'seguidor':
          if($form['id'] && $user){
            $db->query("DELETE FROM copy_traders WHERE id = '".$form['id']."' && trader_id = '".$user['id']."'");
            echo 'ok';
            fechar();
            exit;
          }
        break;
    endswitch;
    break;
}

function profit_percent($id){
  global $db;
  $ganhos = $db->read("SELECT count(*) FROM lots WHERE user_id ='".$id."' && demo = '0' && profit > 0");
  $perdas = $db->read("SELECT count(*) FROM lots WHERE user_id = '".$id."' && demo = '0' && profit < 0");
  if($ganhos < 1) return 0;
  $profit = $ganhos + $perdas;
  $percent = ($ganhos / $profit) * 100;

  return number_format($percent, 2);
}

function seguidores($id){
  global $db;

  $seguidores = $db->read("SELECT count(*) FROM copy_traders WHERE trader_id = '".$id."' && status = '1'");

  return $seguidores;
}


function negociacoes($id){
  global $db;

  $negociacoes = $db->read("SELECT count(*) FROM lots WHERE user_id = '".$id."' && demo = '0'");

  return $negociacoes;
}

function negociacoes_lucro($id) {
  global $db;

  $negociacoes_lucro = $db->read("SELECT count(*) FROM lots WHERE user_id = '".$id."' && demo = '0' && profit > '0'");

  return $negociacoes_lucro;
}

function lucro($id) {
  global $db;

  $lucro = $db->read("SELECT sum(profit) from lots WHERE user_id = '".$id."' && demo = '0' ");

  return $lucro;
}

function valorMax($id) {
  global $db;

  $valorMax = $db->read("SELECT lot FROM lots WHERE user_id = '".$id."' && demo = '0' order by 	`lot`	desc LIMIT 1");

  return $valorMax;
}

function valorMin($id) {
  global $db;

  $valorMin = $db->read("SELECT lot FROM lots WHERE user_id = '".$id."' && demo = '0' order by 	`lot`	asc LIMIT 1");

  return $valorMin;
}