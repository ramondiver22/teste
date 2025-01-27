<?	 
// Payment notification

if(function_exists('http_build_query')){
	$request =  http_build_query($_REQUEST);
	$request = $core->filter($request);
    $arr = array(
		  'request'   =>   $request,
		  'd'         =>   date("d"),
		  'm'         =>   date("m"),
		  'y'         =>   date("Y"),
		  'h'         =>   date("H"),
		  'i'         =>   date("i"),
	);
	$db->insert('payin_log', $arr);
}



switch($url[1]){


  case 'epaycore':

if(isset($_POST['epc_batch']) && isset($_POST['epc_sign']))
{
   # your merchant password
   $password = $conf['epaycore_pass'];

   # sign params
   $sign = [
      $_POST['epc_merchant_id'],
      $_POST['epc_order_id'],
      $_POST['epc_created_at'],
      $_POST['epc_amount'],
      $_POST['epc_currency_code'],
      $_POST['epc_dst_account'],
      $_POST['epc_src_account'],
      $_POST['epc_batch'],
      $password
   ];

   # get sha256 signature
   $sign = hash('sha256', implode(':', $sign));

   # if signature not valid
   if($_POST['epc_sign'] !== $sign)
   {
      # set header 400
      header('HTTP/1.1 400 Bad request');

      # exit
      die('Invalid signature');
   }

   $amount = floatval( $_POST['epc_amount']);
   $id = intval($_POST['epc_order_id']);
   addBalance($id, $amount);


   # if signature valid
   echo $_POST['epc_batch'];
}




   break;


 
  case 'coinpayments':


 // Fill these in with the information from your CoinPayments.net account.
    $cp_merchant_id = $conf['coinpayments_id'];
    $cp_ipn_secret = $conf['coinpayments_secret'];
    $cp_debug_email = $conf['admin_email'];;

	
	$order_currency = 'USD';


    if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
        errorAndDie('IPN Mode is not HMAC');
    }

    if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
        errorAndDie('No HMAC signature sent.');
    }

    $request = file_get_contents('php://input');
    if ($request === FALSE || empty($request)) {
        errorAndDie('Error reading POST data');
    }

    if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
        errorAndDie('No or incorrect Merchant ID passed');
    }

    $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
    if ($hmac != $_SERVER['HTTP_HMAC']) { 
        errorAndDie('HMAC signature does not match');
    }
    
    // HMAC Signature verified at this point, load some variables.

    $txn_id = $_POST['txn_id'];
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $amount1 = floatval($_POST['amount1']);
    $amount2 = floatval($_POST['amount2']);
    $currency1 = $_POST['currency1'];
    $currency2 = $_POST['currency2'];
    $status = intval($_POST['status']);
    $status_text = $_POST['status_text'];

    //depending on the API of your system, you may want to check and see if the transaction ID $txn_id has already been handled before at this point

    // Check the original currency to make sure the buyer didn't change it.
    if ($currency1 != $order_currency) {
        errorAndDie('Original currency mismatch!');
    }    

           $id = intval($_POST['invoice']);

    if ($status >= 100 || $status == 1) {
		 addBalance($id, $amount1);

    } else if ($status < 0) {
       	 $db->update('payin', "status = 'error', amount = '".$amount."'  WHERE id = '".$id."'   ");
    } else {
        $db->update('payin', "status = 'wait', amount = '".$amount."'  WHERE id = '".$id."'   ");

    }
    die('IPN OK');
    fechar();
	exit;
break;


 
  
} 


function addBalance($id, $amount){
   global $db, $core;
   $payin = $db->assoc("SELECT  *  FROM payin  WHERE id = '".$id."'  ");
   $user = $db->assoc("SELECT * FROM users WHERE  id = '".$payin['user_id']."' ");
   if($user['id']  && $payin['status'] != '1'){
     
	 //$db->update('users', "balance=balance+'".$amount."'  WHERE id = '".$payin['user_id']."'   ");
	 //$db->update('payin', "status = '1', amount = '".$amount."'  WHERE id = '".$payin['id']."'   ");

//$core->partnerStat('deposits', $amount, $user['id']);

   }
}



 