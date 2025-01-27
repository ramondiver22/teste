<?php

header("Content-Type: application/json");

$arr['today'] = $db->in_array("SELECT i, f, o, `time`, profit_today AS 'profit', photo FROM users WHERE profit_today > '0' AND copy = '1' order by profit_today desc limit 0,20 ");

$arr['three'] = $db->in_array("SELECT i, f, o, `time`, profit_three AS 'profit', photo FROM users WHERE profit_three > '0' AND copy = '1' order by profit_three desc limit 0,20 ");

$arr['seven'] = $db->in_array("SELECT i, f, o, `time`, profit_seven AS 'profit', photo FROM users WHERE profit_seven > '0' AND copy = '1' order by profit_seven desc limit 0,20 ");


echo json_encode($arr);	   