<?php 
	if(!empty($_POST) && isset($_COOKIE['wotm8'])){
		
		$player_id = strip_tags(trim($_POST['player_id']));
		$player_name = strip_tags(trim($_POST['player_name']));
		$player_rank = strip_tags(trim($_POST['player_rank']));
		$player_clan = strip_tags(trim($_POST['player_clan']));
		$player_tank_list = strip_tags(trim($_POST['player_tank_list']));
		if(!is_numeric($player_id)){
			echo 'bad id';
			exit;
		}
		if(strlen($player_name) >= 50){
			echo 'bad username';
			exit;
		}
		if(strlen($player_rank) >= 50){
			echo 'bad playername';
			exit;
		}
		if(strlen($player_clan) >= 6){
			echo 'bad clan name';
			exit;
		}
		$temp = explode(',',$player_tank_list);
		$player_tank_list = array();
		foreach($temp as $id){
			if(is_numeric($id)){
				$player_tank_list[] = $id;
			}
		}
		$player_tank_list = implode(',', $player_tank_list);
		$weekdays = $_POST['weekdays'];
		$availability = array();
		for($i = 0; $i<7; $i++){
			$j = $i+1;
			if(in_array($j, $weekdays)){
				if($day == $counter){
					$availability[] = 1;
				}
			} else {
				$availability[] = 0;
			}
		}
		$list = implode(', ', $availability);
		$query = 'REPLACE INTO roster (player_id, player_name, player_rank, clan, tanks, monday, tuesday, wednesday, thursday, friday, saturday, sunday)';
		$query .= ' VALUES ('.$player_id.', "'.$player_name.'", "'.$player_rank.'", "'.$player_clan.'", "'.$player_tank_list.'", '.$list.');';
		
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
		$retval = mysql_query($query, $con);
		if(! $retval )
		{
			die('Could not enter data: ' . mysql_error());
		}
		mysql_close($con);
		header('location: http://apcr.hol.es/attendance/');
	}
?>