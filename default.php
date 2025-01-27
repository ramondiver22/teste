<?
  $inicio = "";
  $final = "";
  
  if(!is_null($_POST['inicio'])) $inicio = " AND time > '".strtotime($_POST['inicio']." 23:59:59")."'";
  if(!is_null($_POST['final'])) $final = " AND time < '".strtotime($_POST['final'])."'";
  if(!is_null($_POST['inicio'])) $inicio_end = " AND time_end > '".strtotime($_POST['inicio']." 23:59:59")."'";
  if(!is_null($_POST['final'])) $final_end = " AND time_end < '".strtotime($_POST['final'])."'";

  // $core->redir('/dashboard/users');
  $left = strtotime(date('Y-m-d 00:00:00'));	

  $online = $db->read("SELECT count(id) FROM stats WHERE  online  > '".$left."' AND user_id > '0' ");	

  $cadastros = $db->read("SELECT  COUNT(*) FROM users WHERE 1=1 ".$inicio.$final);
  $cadastros = ($cadastros) ? $cadastros : 0;

  $cadastros_depositantes = $db->read("SELECT  COUNT(*) FROM users WHERE balance > 0 AND level=0 ".$inicio.$final);
  $cadastros_depositantes = ($cadastros_depositantes) ? $cadastros_depositantes : 0;

  $deposito = $db->read("SELECT  sum(amount) FROM payin WHERE status=1 AND marketing=0 ".$inicio.$final);
  $deposito = ($deposito) ? $deposito : 0;

  $retiradas = $db->read("SELECT  sum(amount_pay) FROM payout WHERE status=1 AND marketing=0 AND tipo='normal' ".$inicio.$final);
  $retiradas = ($retiradas) ? $retiradas : 0;

  $retiradas_pendentes = $db->read("SELECT  sum(amount_pay) FROM payout WHERE status=0 AND marketing=0 ".$inicio.$final);
  $retiradas_pendentes = ($retiradas_pendentes) ? $retiradas_pendentes : 0;

  $retiradas_af = $db->read("SELECT  sum(amount_pay) FROM payout WHERE status=1 AND marketing=0 AND tipo='afiliado' ".$inicio.$final);
  $retiradas_af = ($retiradas_af) ? $retiradas_af : 0;

  $banca = $db->read("SELECT sum(balance) FROM users WHERE 1=1 AND level=0");
  $banca = ($banca) ? $banca : 0;

  $perdas = (float) $db->read("SELECT sum(profit) FROM lots_history WHERE profit < 0 AND marketing=0 AND demo=0 AND `status`=1 ".$inicio_end.$final_end);
  //$perdas = ($perdas) ? $perdas : 0;
 //$perdas = (float) $deposito - (float) $banca;

  $lucro = (float) $deposito - (float) $retiradas;


  include('tpl/dashboard/header.tpl');
  include('tpl/dashboard/dashboard.tpl');
  include('tpl/dashboard/footer.tpl');