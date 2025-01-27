<?
if(!$url[2]) $url[2] = 'default'; // $url[3]
$left = strtotime(date('Y-m-d 00:00:00'));	
$count = $db->read("SELECT count(id) FROM stats   WHERE  online  > '".$left."'  ");	

switch($url[2]){



  default: 
		

		
   include('tpl/dashboard/header.tpl');
   include('tpl/dashboard/stat/stat.tpl');
   include('tpl/dashboard/footer.tpl');
  break;
		
		
  case 'list':	
   $table = $db->in_array("SELECT * FROM stats   WHERE    online  > '".$left."'         order by  id desc ");
 
   		
		
		
   
   include('tpl/dashboard/stat/list.tpl');
  break;
		
		
		
  case '2':
		
		
   $table  =  $db->in_array("SELECT * FROM  stat  ");
		
		
   include('tpl/dashboard/header.tpl');
   include('tpl/dashboard/stat/stat2.tpl');
   include('tpl/dashboard/footer.tpl');
  break;
		



		
		
 
}

 

function getDevice($agent){
	if(stristr($agent,'windows')) return 'windows';
 	if(stristr($agent,'android')) return 'android';
 	if(stristr($agent,'Mac')) return 'ios';
	
}

function isMob($agent){
	if(stristr($agent,'iphone')) return true;
 	if(stristr($agent,'android')) return  true;
    return false;
	
}

function getBrowser($agent){
	if(stristr($agent,'OPR')) return 'opera';
	if(stristr($agent,'Edg')) return 'edge';
	if(stristr($agent,'Firefox')) return 'firefox';
	if(stristr($agent,'Chrome')) return 'chrome';
	if(stristr($agent,'safari')) return 'safari';
}

function refSite($ref){
	if(stristr($ref,'yandex')) return 'yandex';
    if(stristr($ref,'google')) return 'google';
	
    return 'ref';
}


function isOnline($time){
    if($time > time()-5) return true;
	return false;
}

 