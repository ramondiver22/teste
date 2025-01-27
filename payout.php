<? 

if(!$url[2]) $url[2] = 'default';
// $url[3]

function gerarPagamento($url, $token, $valor, $nome, $cpf, $chave, $tipo, $id){

  $bodyJ = json_encode( array( 
    'value_cents' => $valor,
    'initiation_type' => 'dict',
    'idempotent_id' => 'traderPG_'.$id,
    'authorized' => true,
    'receiver_name' => $nome,
    'receiver_document' => $cpf,
    'pix_key_type' => $tipo,
    'pix_key' => $chave,

  ));
  $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodyJ);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer '.$token,
      'Host: api.primepag.com.br'
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

$status = [0 => 'Aguardando', 1 => 'Confirmado', 2 => 'Negado', 3 => 'Aguardando Confirmação', 4 => 'Autorização pendente', 5 => 'Autorização Automática', 6 => 'Cancelado pela PrimePag'];
$status_ = ['completed' => 1, 'canceled' => 6, 'sent' => 3, 'auto_authorization' => 5, 'authorization_pending' => 4];

switch($url[2]){


  default:
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/payout/payout.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
		
  case 'list':
    if(!$form['page']) $form['page'] = 1;		  
    if($form['q']) $q .= " && users.login like '%".$form['q']."%'  ";	
		

					
 		
     $table = $db->in_array("SELECT  payout.*,  users.login, users.f, users.i FROM payout, users WHERE payout.user_id=users.id && payout.tipo = 'normal' && status = '0' && payout.marketing = '0' $q    order by payout.id desc ");
    include('tpl/dashboard/payout/list.tpl');
  break;
		
		
  case 'history':
    if(!$form['page']) $form['page'] = 1;	   	  
    if($form['q']) $q .= " && users.login like '%".$form['q']."%'  ";	
		
			
 		
     $table = $db->in_array("SELECT  payout.*,  users.login FROM payout, users WHERE payout.user_id=users.id && payout.tipo = 'normal' && status != '0'  && payout.marketing = '0' $q    order by payout.id desc ");
    include('tpl/dashboard/payout/history.tpl'); 
  break;
			
		
		
		
		
  case 'accept':

    $paymentsUrl = "https://api.primepag.com.br/v1/pix/payments";
    $access_token = null;	
  
    $resultado = json_decode(gerarToken($tokenPrime));
  
    if(!$resultado->access_token) throw new Exception("Erro: Entre em contato com o administrador.");
  
    $access_token = $resultado->access_token;
    $payout = $db->assoc("SELECT * FROM payout WHERE id = '".$form['id']."'");

    if(!$payout["metadata"]) throw new Exception("Erro: Dados não encontrado.");

    $dados = json_decode($payout["metadata"]);
    
    $resultado_final = json_decode(gerarPagamento($paymentsUrl, $access_token, $payout['amount_pay']*100, $dados->nome, $dados->cpf, $dados->chave, $dados->tipo_chave, $form['id']));
    
    if(!$resultado_final->payment) throw new Exception("Erro: Pagamento não efetuado.");
    $arr = array(
     'status'   => $status_[$resultado_final->payment->status],
     'reference_code' => $resultado_final->payment->reference_code
     
    );
    $db->update('payout', $arr, " id = '".$form['id']."' ");
    $partner = $db->read("SELECT user_id FROM referals WHERE referal_id = '".$form["id"]."'");
    if($arr['status'] != 6) $core->partnerStat('saques', $payout['amount_pay'], $partner);
    $db->update('users', "cron_afiliado = 0 WHERE id = '".$payout['user_id']."'");
    echo 'ok';
	exit;
  break;
		
		
		
  case 'cancel':
    $payout = $db->assoc("SELECT * FROM payout WHERE id = '".$form['id']."'");

    $arr2 = array();
    $arr = array(
     'status'            => 2,
    );
    
    if($payout['tipo'] === 'normal' && $payout['status'] === "0") {
      $db->update('payout', $arr, " id = '".$form['id']."' ");
      $bonus = json_decode($payout['metadata']);
      if($db->update('users', "`balance` = `balance` + '".$payout['amount_usd']."', `bonus` = `bonus` + '".$bonus->bonus_perdido."' WHERE id = '".$payout['user_id']."'")){
        
        echo 'ok';
      } 
    }elseif($payout['tipo'] === 'afiliado' && $payout['status'] === "0"){
      $db->update('payout', $arr, " id = '".$form['id']."' ");
      if($db->update('users', "`balance_afiliado` = `balance_afiliado` + '".$payout['amount_usd']."' WHERE id = '".$payout['user_id']."'")){
        echo 'ok';
      } 
    }
		
	exit;
  break;			
		
					
		
}


function paysystem($id){
	global $db;
	return $db->read("SELECT name FROM  payout_types WHERE id = '".$id."'  ");
	
	
}

