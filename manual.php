<? 
 

function geTables(){
 global $db;	
 $query ="SHOW TABLES";
 $result = mysqli_query($db->link, $query);
 $rows = mysqli_num_rows($result); 
 for ($i = 0 ; $i < $rows ; ++$i){
     $row = mysqli_fetch_row($result);
     $tb = trim($row[0]);
     $tables[] =  $tb;  
 }
 return $tables;	
}



function getColumns($table){
 global $db;	
        $s = $db->in_array("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE    `TABLE_SCHEMA` = '".$db->base ."'    &&  `TABLE_NAME`='".$table."';");
	    $col = array();
	    foreach($s as $item){
			$col[] = $item['COLUMN_NAME'];
		}
 return $col;	
}


$tables =  geTables();
 



include 'tpl/dashboard/header.tpl';	
include 'tpl/dashboard/manual.tpl';
include 'tpl/dashboard/footer.tpl';