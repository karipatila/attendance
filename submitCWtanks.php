<?php
	$tanks = $_POST['tank'];
	$clan = $_POST['clan'];
	
	$mysql_hostname = 'mysql.hostinger.fi';
	$mysql_database = 'u406288039_qsfj';
	$mysql_user = 'u406288039_comm';
	$mysql_password = 'J0HlJjlqV08O';
	$con=mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	$db = mysql_select_db($mysql_database, $con);
	
	if(!empty($tanks)){
		$query = 'DELETE FROM cwtanklist WHERE clan="'.$clan.'"';
		$retval = mysql_query($query, $con);
		if(! $retval )
		{
			die('Could not enter data: ' . mysql_error());
		}
		foreach($tanks as $tank){
			$query = 'INSERT INTO cwtanklist (clan, tank_id) VALUES ("'.$clan.'",'.$tank.')';
			$retval = mysql_query($query, $con);
			if(! $retval )
			{
				die('Could not enter data: ' . mysql_error());
			}
		}
		mysql_close($con);
	}
	header('location: http://apcr.hol.es/attendance/');
?>