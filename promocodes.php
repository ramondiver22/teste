<? 

 
if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){


  default:
	$table = $db->in_array("SELECT  * FROM promocodes ");	
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/promocodes/promocodes.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
			
		
  case 'add':
	if($form['promocode']  && $form['percent']){
          
	   $arr = array(
         'promocode'         => $form['promocode'],
          'percent'           => intval($form['percent']),
       );
       $db->insert('promocodes', $arr);

	   $core->redir($ref);	
	}
	
    include('tpl/dashboard/promocodes/add.tpl');
   
  break;
		
  case 'del':
 
    if($url[3]) $db->delete('promocodes', $url[3]);
	$core->redir($ref);	
  break;
				
		
		
}
