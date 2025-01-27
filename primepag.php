<? 
if(!$url[2]) $url[2] = 'default';
// $url[3]


function listarPagamentos() {
  global $url, $tokenPrime;
  $access_token = null;

  $token = json_decode(gerarToken($tokenPrime));
  if(!$token->access_token) throw new Exception("Erro: Entre em contato com o administrador.");
  $page = ($url[2] && $url[3] && $url[4]) ? '&payment_start_date='.$url[2].'T00:00:00&payment_end_date='.$url[3].'T23:59:59&page='.$url[4] : '';
  $curl = curl_init("https://api.primepag.com.br/v1/pix/qrcodes?status=paid".$page);
    curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer '.$token->access_token,
      'Host: api.primepag.com.br'
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
} 
switch($url[2]){
  default:
  
  header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(listarPagamentos());
    break;
}
