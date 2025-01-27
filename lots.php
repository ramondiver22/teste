<?
if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){



  default: 
 


   include('tpl/dashboard/header.tpl');
   include('tpl/dashboard/lots/lots.tpl');
   include('tpl/dashboard/footer.tpl');
  break;

		
  case 'list':
	   $left = strtotime(date('Y-m-d 00:00:00'));	
		
		
		if($form['status'] == 0) {
			$q = " && (lots.status = '0'  || lots.status = '2'   || (lots.status = 1  && lots.time_start > '".$left."')  ) ";
		} 
		if($form['status'] == 1) {
			$q = " && lots.status = '1' ";
		}
 	
    if($form['q']) $q .= " && users.login like '%".$form['q']."%'  ";	
		

					
 		
     $table = $db->in_array("SELECT  lots.*,  users.login FROM lots, users WHERE lots.user_id=users.id   $q    order by lots.id desc ");
    include('tpl/dashboard/lots/list.tpl');
  break;
			
		
  
}


 