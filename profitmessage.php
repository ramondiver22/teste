<? 
 $k = $form['k'];
 
	
$lot = $db->assoc("SELECT  * FROM lots WHERE   user_id = '".$user['id']."'  && id = '".intval($form['id'])."'  ");

?>	
  <div class="alertmessage" id="alertlot_<?=$lot['id']?>" style="display: none">
<? //date("H:i",$lot['time_start'])?>
	
		   <img   class="icon"   src="img/icon/<?=$k?>.png" alt="">  
	    
	    <div class="profitinfo">
			
		    <div  class="n1"><?=$lot['currency']?></div>
	        <div  class="n2">$<?=$lot['lot']?></div>
			<div class="clear"></div>

			<div  class="n3 lot_profit_<?=$lot['id']?>">$0.00</div>
	      
		
	
	        <div class="clear"></div>

	    </div>
        <div class="clear"></div>

	
  </div>