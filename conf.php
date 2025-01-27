<? 

	
if(!$url[2]) $url[2] = 'default';
// $url[3]

switch($url[2]){
 
  default:
   include('tpl/dashboard/header.tpl');
   include('tpl/dashboard/conf.tpl');
   include('tpl/dashboard/footer.tpl');		
  break;	
		
  case 'general':
	 if($_POST){
		 foreach($form as $k => $v){
		   $id = $db->read("SELECT id FROM conf WHERE k = '". $k."' ");	 
            $arr = array(
            'v'  => $v,
            );
            $db->update('conf', $arr, " id = '".$id."' ");
		 }

		echo 'ok'; 
    fechar();
        exit;
	 }	
     
	 $i = $conf;	
   include('tpl/dashboard/header.tpl');
 	 include 'tpl/dashboard/conf/general.tpl';
   include('tpl/dashboard/footer.tpl');
  break;
		
		
  case 'partner':
	 if($_POST){
 
		 foreach($form as $k => $v){
		   $k = str_replace("level","",$k);	 
		   $id = $db->read("SELECT id FROM levels WHERE level = '". $k."' ");	 
            $arr = array(
            'percent'  => $v,
            );
            $db->update('levels', $arr, " id = '".$id."' ");
		 }
 
		echo 'ok'; 
    fechar();
        exit;
	 }	
     
	 $levels = $db->in_array("SELECT * FROM levels ");
		
		
 	 include 'tpl/dashboard/conf/partner.tpl';

  break;		
		

		
		
		
		
}		
