
<?php
	/*
		Created by:					karipatila [QSF-E]
		Quickybaby.com/forum:		karipatila
		Throwaway project email:	sulo.patila@mail.com
		
		CW attendance, availability and tank tracker.
	
		TODO: 	highlight proper CW tanks
				replay file parsing
	*/
	
	$baseURL = 'http://apcr.hol.es/attendance/';
	
	# Application ID registered to Wargaming (by karipatila)
	$applicationID = 'b4319422268043312f2b98daad5e7040';
	$APIErrorMessage = '<p><strong>Error: API did not respond.</strong></p><br />';
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<base href="<?php echo $baseURL; ?>" />
		<title><?php echo isset($_COOKIE['wotm8']['nick']) ? $_COOKIE['wotm8']['nick'].' ':''; ?>Reporting for Duty!</title>
		<meta name="description" content="Fame Point tracker for QSF Clans">
		<meta name="author" content="karipatila [QSF-E]">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="http://apcr.hol.es/fame/css/style.css">
		<link rel="stylesheet" href="<?php echo $baseURL; ?>css/style.css">
		<link rel="shortcut icon" href="<?php echo $baseURL; ?>favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?php echo $baseURL; ?>favicon.ico" type="image/x-icon">
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'>
	</head>
	<body>
	<?php if(empty($_GET) && !isset($_COOKIE['wotm8'])){ ?>
		<div id="loginbox">
			<h1>ROSTER</h1>
			<p>Sign in with your World of Tanks account</p>
			<p><a href="https://api.worldoftanks.eu/wot/auth/login/?application_id=<?php echo $applicationID; ?>&redirect_uri=http://apcr.hol.es/attendance/">OpenID Login</a></p>
		</div>
	<?php } elseif(!isset($_COOKIE['wotm8'])) {
		$token = $_GET['access_token'];
		$id = $_GET['account_id'];
		$nick = $_GET['nickname'];
		setcookie("wotm8[token]", $token, time()+3600);
		setcookie("wotm8[id]", $id, time()+3600);
		setcookie("wotm8[nick]", $nick, time()+3600);
		header("Location: ".$baseURL);
	}	else {
	?>
		<div id="container">
	<?php
			$nick = $_COOKIE['wotm8']['nick'];
			$id = $_COOKIE['wotm8']['id'];
			$token = $_COOKIE['wotm8']['token'];
			echo '<p><a href="'.$baseURL.'logout.php">Log Out</a></p>';
			echo '<p>signed in as ' . $_COOKIE['wotm8']['nick'] . ' ' . $_COOKIE['wotm8']['id'] . ' ' . $_COOKIE['wotm8']['token'] . '</p>';
			
			$playerInfoRequest = 'http://api.worldoftanks.eu/wot/clan/membersinfo/?application_id='.$applicationID.'&member_id='.$id;
			$json = file_get_contents($playerInfoRequest);
			$obj = json_decode($json);
			if(isset($obj->data)){
				$clan = $obj->data->$id->abbreviation;
				$role = $obj->data->$id->role;
				$role_i18n = $obj->data->$id->role_i18n;
				$clan_name = $obj->data->$id->clan_name;
				$emblem = $obj->data->$id->emblems->large;
			}
?>			
			<h1><img src="<?php echo $emblem; ?>" alt="<?php echo $clan; ?>" /> Clan Wars Roster</h1>
			<form action="submit.php" method="POST">
				<fieldset>
					<legend>Available for Clan Wars</legend>
					<table>
						<thead>
							<tr>
								<th>Player</th>
								<th>Mon</th>
								<th>Tue</th>
								<th>Wed</th>
								<th>Thu</th>
								<th>Fri</th>
								<th>Sat</th>
								<th>Sun</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo $nick; ?> [<?php echo $role_i18n; ?>]</td>
								<td><input type="checkbox" value="" name="weekdays[]"></td>
								<td><input type="checkbox" value="" name="weekdays[]"></td>
								<td><input type="checkbox" value="" name="weekdays[]"></td>
								<td><input type="checkbox" value="" name="weekdays[]" checked></td>
								<td><input type="checkbox" value="" name="weekdays[]" checked></td>
								<td><input type="checkbox" value="" name="weekdays[]"></td>
								<td><input type="checkbox" value="" name="weekdays[]"checked></td>
							</tr>
							<tr>
								<td><span class="nickname">PetiteDwarf</span></td>
								<td>X</td>
								<td>&nbsp;</td>
								<td>X</td>
								<td>X</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>&nbsp;</th>
								<th>12</th>
								<th>7</th>
								<th>11</th>
								<th>9</th>
								<th>10</th>
								<th>10</th>
								<th>6</th>
							</tr>
						</tfoot>
					</table>
				</fieldset>
				<p><input type="submit" name="submitButton" value="Submit" /></p>
			</form>
<?php			
			$tanksInGarageRequest = 'https://api.worldoftanks.eu/wot/tanks/stats/?application_id='.$applicationID.'&access_token='.$_COOKIE['wotm8']['token'].'&account_id='.$_COOKIE['wotm8']['id'];
			$json = file_get_contents($tanksInGarageRequest);
			$obj = json_decode($json);
			if(isset($obj->data)){
				foreach($obj->data->$id as $tank){
					if($tank->in_garage == 1){
						$tonks[] = $tank->tank_id;
					}
				}
				
				$cwtanks = array(3649,7169,9489);
				
				$tonkList = implode(',',$tonks);
				$tonkInfoRequest = 'https://api.worldoftanks.eu/wot/encyclopedia/tankinfo/?application_id='.$applicationID.'&tank_id='.$tonkList;
				$json = file_get_contents($tonkInfoRequest);
				$obj = json_decode($json);
				$tierArray = array();
				if(isset($obj->data)){
					foreach($obj->data as $tank){
						$tierArray[$tank->level][] = array('image'=>$tank->contour_image, 'name'=>$tank->name_i18n, 'tank_id'=>$tank->tank_id,'type'=>$tank->type_i18n);
					}
				}
				if(!empty($tierArray)){
					$hasArty = array();
					$counter = 0;
					echo '<table class="tanks">';
					echo '<thead><tr><td>&nbsp;</td><td>Tier 6</td></tr></thead>';
					echo '<tbody>';
					foreach($tierArray[6] as $tank){
						echo '<tr';
						echo $counter %2 == 0 ? ' class="odd"':'';
						echo '><td><img src="'.$tank['image'].'" alt="" /></td><td>' . $tank['name'] . '</td></tr>';
						if($tank['type'] == 'SPG'){
							$hasArty[] = 6;
						}
						++$counter;
					}
					echo '</tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
					echo '<table class="tanks">';
					echo '<thead><tr><td>&nbsp;</td><td>Tier 8</td></tr></thead>';
					echo '<tbody>';
					$counter = 0;
					foreach($tierArray[8] as $tank){
						echo '<tr';
						echo $counter %2 == 0 ? ' class="odd"':'';
						echo '><td><img src="'.$tank['image'].'" alt="" /></td><td>' . $tank['name'] . '</td></tr>';
						if($tank['type'] == 'SPG'){
							$hasArty[] = 8;
						}
						++$counter;
					}
					echo '</tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
					echo '<table class="tanks last">';
					echo '<thead><tr><td>&nbsp;</td><td>Tier 10</td></tr></thead>';
					echo '<tbody>';
					$counter = 0;
					foreach($tierArray[10] as $tank){
						echo '<tr';
						echo $counter %2 == 0 ? ' class="odd"':'';
						echo '><td><img src="'.$tank['image'].'" alt="" /></td><td';
						echo in_array($tank['tank_id'], $cwtanks) ? ' class="cw"':'';
						echo '>' . $tank['name'] . '</td></tr>';
						if($tank['type'] == 'SPG'){
							$hasArty[] = 10;
						}
						++$counter;
					}
					echo '</tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
					array_unique($hasArty);
					if(!empty($hasArty)){
						$arty = implode(', ',$hasArty);
						echo '<p>Confirmed Scumbag. Player has tier '.$arty.' artillery.</p>';
					}
				}
			}
	?>
	</div> <!-- container -->
	<?php
	}
	?>	
	</body>
</html>