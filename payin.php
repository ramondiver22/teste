<? 

if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){


  default:
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/payin/payin.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
		
		
}