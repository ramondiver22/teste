<? 
header("Content-Type: application/json");


// $courses_id = array(91 => "btcusd",92 => "xrpusd",93 => "eosusd",94 => "bchusd",95 => "trxusd",96 => "ltcusd",98 => "xlmusd",99 => "ethusd",100 => "dshusd",101 => "bnbusd",102 => "xmrusd",103 => "etcusd",105 => "dogusd",106 => "zecusd",108 => "aapl",109 => "amzn",110 => "ba",111 => "meta",112 => "ibm",113 => "ko",114 => "ma",115 => "mcd",116 => "msft",117 => "nke",118 => "tsla",119 => "v",120 => "xauusd",121 => "xagusd",122 => "eurusd",123 => "gbpchf",124 => "gbpjpy",125 => "eurchf",126 => "eurjpy",127 => "usdcad",128 => "usdchf",129 => "usdjpy",130 => "gbpusd",131 => "audusd",132 => "usdtry",133 => "usdzar",134 => "nzdusd",135 => "audnzd",136 => "euraud",137 => "eurcad",151 => "btcusdotc",152 => "tonusdt");


$courses_id = array(91 => "btcusd", 92 => "xrpusd", 94 => "bchusd", 96 => "ltcusd", 99 => "ethusd", 105 => "dogusd", 108 => "aapl", 109 => "amzn", 115 => "mcd", 116 => "msft", 118 => "tsla", 120 => "xauusd", 121 => "xagusd", 122 => "eurusd", 123 => "gbpchf", 124 => "gbpjpy", 126 => "eurjpy", 127 => "usdcad",128 => "usdchf", 129 => "usdjpy", 130 => "gbpusd", 131 => "audusd", 134 => "nzdusd");

$course = $redis->hgetall('currency');

foreach($courses_id as $key => $value) {
   $arr['currency'][$key] = $course[$value];
}

$lots_open = [];
$lots = $redis->hgetall('lots:'.$user['id']);

foreach($lots as $lot_id){
   $lot = json_decode($redis->hget('lots', $lot_id), true);
   if($lot) $lots_open[] = $lot; 
}

$arr['lots_open']  =   ($lots_open) ? $lots_open : [];//$db->in_array("SELECT * FROM lots WHERE user_id = '".$user['id']."'  && status = 0       limit 0,100 ");

$arr['count_lots_open']  = count($arr['lots_open']);

$arr['lots_close']  =  $db->in_array("SELECT lots.id, lots.trend, lots.currency_id, lots.currency, lots.currency_k, currency.category AS currency_cat, lots.profit, lots.lot, lots.time_start, lots.time_end FROM lots, `currency` WHERE lots.user_id = '".$user['id']."' && currency.id = lots.currency_id  && lots.status = 1   order by lots.id desc  limit 0,20  ");


$arr['count_lots_close']  =  $db->read("SELECT count(id)  FROM lots WHERE user_id = '".$user['id']."'  && status = 1  && demo = '".$user['demo']."'  ");


$alert = $db->assoc("SELECT * FROM alerts WHERE user_id = '".$user['id']."'  && status = '0'   order by time  ");
$setarr = array(
   'status'            => '1',
);

if($alert['id']) $db->update('alerts', $setarr, " id = '".$alert['id']."' ");
$now = time() + 30;
$relogios = [];
$relogios[60] = (mktime(date('G'), date('i')+1, 0) < $now) ? mktime(date('G'), date('i')+2, 0) : mktime(date('G'), date('i')+1, 0);
$minutos = date('i');
$tempo = 0;
while(!($minutos < $tempo)) $tempo += 5;
$relogios[300] = (mktime(date('G'), $tempo, 0) < $now) ? mktime(date('G'), $tempo+5, 0) : mktime(date('G'), $tempo, 0);
$tempo = 0;
while(!($minutos < $tempo)) $tempo += 15;
$relogios[900] = (mktime(date('G'), $tempo, 0) < $now) ? mktime(date('G'), $tempo+15, 0) : mktime(date('G'), $tempo, 0);

$arr['alert'] = $alert;
$arr['demo'] = $user['demo'];
$arr['logout'] = 0;
$arr['time']  = time();	
$arr['relogios'] = $relogios;
$arr['balance']  =  $user['balance']+$user['bonus'];
$arr['balance_demo']  = $user['balance_demo'];
$arr['balance_invest']  = $user['balance_invest'];
$arr['partner_balance']  = $user['partner_balance'];


echo json_encode($arr);	   