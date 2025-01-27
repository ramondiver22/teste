<? 
if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){


  default:
	$table = $db->in_array("SELECT  * FROM payin_types ");	
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/paysystem/paysystem.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
			
		
  case 'out':
	$table = $db->in_array("SELECT  * FROM payout_types  ");	
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/paysystem/paysystem_out.tpl');
    include('tpl/dashboard/footer.tpl');
  break;
	
  case 'del':
    include('tpl/dashboard/paysystem/del.tpl');
  break;
			
		
  // Edit payin	
  case 'edit':		
	if($form['name']){
	   //$img = $core->upload('files/upload');
	   $id = intval($url[3]); 
	   $arr = array(
         'name'              => $form['name'],
         'account'           => $form['account'],
       );
       $db->update('payin_types', $arr, " id = '".$id."' ");
	   $core->redir('/dashboard/paysystem');
	}

		
	$i = $db->assoc("SELECT * FROM payin_types WHERE id = '".intval($url[3])."' ");	
		
    include('tpl/dashboard/paysystem/edit.tpl');
  break;	
		
		
		
  // Edit out		
  case 'edit2':		
	if($form['name']){
	   $id = intval($url[3]); 
	   $arr = array(
         'name'              => $form['name'],
         'account'           => $form['account'],
       );
       $db->update('payout_types', $arr, " id = '".$id."' ");
	   $core->redir('/dashboard/paysystem/out');
	}

		
	$i = $db->assoc("SELECT * FROM payout_types WHERE id = '".intval($url[3])."' ");	
		
    include('tpl/dashboard/paysystem/edit.tpl');
  break;	
				
				
		
   case 'add':
	if($form['name']){
	   $img = $core->upload('files/upload');
		
	   $arr = array(
         'name'              => $form['name'],
         'status_in'         => 1,
         'status_out'        => 1,
         'account'           => $form['account'],
         'img'               => $img,
       );
       $db->insert('paysystems', $arr);

	   $core->redir('/dashboard/paysystem');
	}	
    include('tpl/dashboard/paysystem/add.tpl');
  break;
		
		
		
		
  case 'status':
  if($form['id']){	
	$arr = array(
      'status'            => intval($form['v']),
    );
   $db->update('payin_types', $arr, " id = '".intval($form['id'])."' ");
  } 
  break;
				
}	