<?  
if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){


  default:
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/asset/assets.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
			
		
  case 'binary':
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/asset/assets_binary.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
				
		
  case 'list':
			
     if($form['q']) $q = " && name like '%".$form['q']."%'  ";	
			
 		
     $table = $db->in_array("SELECT  * FROM currency WHERE forex = 1   $q ");
    include('tpl/dashboard/asset/list.tpl');
  break;
			
		
 	
  case 'list_binary':
				
    if($form['q']) $q = " && name like '%".$form['q']."%'  ";	

    $table = $db->in_array("SELECT  * FROM currency WHERE `category` IN (7, 8, 9)  $q");
     
   
    //$table = $db->in_array("SELECT  * FROM currency WHERE forex = 0  $q");
   include('tpl/dashboard/asset/list_binary.tpl');
 break;
			
		
		
  case 'status':
  if($form['id']){	
	$arr = array(
      'status'            => intval($form['v']),
    );
   $db->update('currency', $arr, " id = '".intval($form['id'])."' ");
  } 
  break;
				
		
		
   case 'leverage':
	if($form['leverage']){
	    if($form['asset_all']){
           $db->update('currency'," leverage  = '".$form['leverage']."'    WHERE forex = 1");
		}
		$arr = array(
         'leverage'  => $form['leverage'],
        );
        $db->update('currency', $arr, " id = '".intval($form['id'])."' ");
	 echo 'ok';
   fechar();
     exit;
	}	
	$id = intval($url[3]);	 // $url[4]
    $i = $db->assoc("SELECT * FROM currency WHERE id = '".$id."' ");
    include('tpl/dashboard/asset/leverage.tpl');
  break;
		
		
   case 'settings':
	
	if($form['profit']){
	    if($form['asset_all'] != 1) $w = " && id = '".intval($form['id'])."' ";
           $db->update('currency'," profit  = '".$form['profit']."'   WHERE forex = 0   $w ");
		
		 
	 echo 'ok';
   fechar();
     exit;
	}	
		
	$id = intval($url[3]);		
    $i = $db->assoc("SELECT * FROM currency WHERE id = '".$id."' ");
    include('tpl/dashboard/asset/settings.tpl');
  break;		

}	