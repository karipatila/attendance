
<?php
	error_reporting(0);
	@ini_set('display_errors', 0);
	/*
		Created by:					karipatila [QSF-E]
		Quickybaby.com/forum:		karipatila
		Throwaway project email:	sulo.patila@mail.com
		
		CW attendance, availability and tank tracker.
		
		u406288039_qsfj
		mysql.hostinger.fi
		u406288039_comm :: J0HlJjlqV08O
	*/
	
	$baseURL = 'http://apcr.hol.es/roster/';
	
	# Application ID registered to Wargaming (by karipatila)
	$applicationID = 'b4319422268043312f2b98daad5e7040';
	$APIErrorMessage = '<p><strong>Error: API did not respond.</strong></p><br />';
	
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
			<p><a href="https://api.worldoftanks.eu/wot/auth/login/?application_id=<?php echo $applicationID; ?>&redirect_uri=http://apcr.hol.es/roster/">OpenID Login</a></p>
		</div>
	<?php } elseif(!isset($_COOKIE['wotm8'])) {
		$token = $_GET['access_token'];
		$id = $_GET['account_id'];
		$nick = $_GET['nickname'];
		
		$clans = array('QSF','QSF-C','QSF-E','QSF-L','QSF-X', 'WHO');
		$playerInfoRequest = 'http://api.worldoftanks.eu/wot/clan/membersinfo/?application_id='.$applicationID.'&member_id='.$id;
		$json = file_get_contents($playerInfoRequest);
		$obj = json_decode($json);
		if(isset($obj->data)){
			$clan = $obj->data->$id->abbreviation;
		}
		if(in_array($clan, $clans)){
			setcookie("wotm8[token]", $token, time()+432000);
			setcookie("wotm8[id]", $id, time()+432000);
			setcookie("wotm8[nick]", $nick, time()+432000);
			header("Location: ".$baseURL);
		} else {
			header("Location: ".$baseURL."unavailable/");		
		}
	}	else {
	?>
		<div id="container">
	<?php
			$nick = $_COOKIE['wotm8']['nick'];
			$id = $_COOKIE['wotm8']['id'];
			$token = $_COOKIE['wotm8']['token'];
			echo '<div id="logout"><a href="'.$baseURL.'logout.php">Log Out</a></div>';
			
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
			
			$isAdmin = false;
			$setup = false;
			if( ($role != 'private') && ($role != 'recruit') && ($role != 'reservist') || $id == 511922213){
				$isAdmin = true;
			}
			if($isAdmin && isset($_GET['setup'])){
				$setup = true;
			}
			
			if($isAdmin == true){
				echo '<div id="settings"><a href="'.$baseURL.'settings/">Settings</a></div>';
			}
			
			$result = mysql_query('SELECT * FROM roster WHERE player_id='.$id) or die (mysql_error());
			while($row = mysql_fetch_array($result)){
				$db_player_id = $row['player_id'];
				$mon = $row['monday'];
				$tue = $row['tuesday'];
				$wed = $row['wednesday'];
				$thu = $row['thursday'];
				$fri = $row['friday'];
				$sat = $row['saturday'];
				$sun = $row['sunday'];
				$updated = $row['updated'];
			}
			if(isset($_GET['day'])){
			if(strlen($_GET['day']) <= 15){
				$day = $_GET['day'];
			} else {
				$day = false;
			}
		}
?>
			<h1><img src="<?php echo $emblem; ?>" alt="<?php echo $clan; ?>" /> [<?php echo $clan; ?>] Roster</h1>
			<?php
			if($setup == false){
			?>
			<form action="submit.php" method="POST">
				<fieldset>
					<legend>Available for Clan Wars, Strongholds and Tank Companies</legend>
					<table id="roster">
						<thead>
							<tr>
								<th class="first"><?php echo isset($day) ? 'Tank listing for '.ucfirst($day).' <a href="'.$baseURL.'">[Clear Selection]</a>':'&nbsp;'; ?></th>
								<th><a href="<?php echo $baseURL; ?>monday/">Mon</a></th>
								<th><a href="<?php echo $baseURL; ?>tuesday/">Tue</a></th>
								<th><a href="<?php echo $baseURL; ?>wednesday/">Wed</a></th>
								<th><a href="<?php echo $baseURL; ?>thursday/">Thu</a></th>
								<th><a href="<?php echo $baseURL; ?>friday/">Fri</a></th>
								<th><a href="<?php echo $baseURL; ?>saturday/">Sat</a></th>
								<th><a href="<?php echo $baseURL; ?>sunday/">Sun</a></th>
							</tr>
						</thead>
						<tbody>
							<tr class="personal">
								<td class="first <?php echo $role; ?>"><?php echo $nick; ?></td>
								<td><input type="checkbox" value="1" name="weekdays[]"<?php echo !empty($mon) ? ' checked':''; ?>></td>
								<td><input type="checkbox" value="2" name="weekdays[]"<?php echo !empty($tue) ? ' checked':''; ?>></td>
								<td><input type="checkbox" value="3" name="weekdays[]"<?php echo !empty($wed) ? ' checked':''; ?>></td>
								<td><input type="checkbox" value="4" name="weekdays[]"<?php echo !empty($thu) ? ' checked':''; ?>></td>
								<td><input type="checkbox" value="5" name="weekdays[]"<?php echo !empty($fri) ? ' checked':''; ?>></td>
								<td><input type="checkbox" value="6" name="weekdays[]"<?php echo !empty($sat) ? ' checked':''; ?>></td>
								<td class="last"><input type="checkbox" value="7" name="weekdays[]"<?php echo !empty($sun) ? ' checked':''; ?>></td>
							</tr>
	<?php
		$result = mysql_query('SELECT * FROM roster WHERE clan = "'.$clan.'" AND player_id NOT IN ('.$id.')') or die (mysql_error());
		$rowcount = 0;
		
		$monday = !empty($mon) ? 1 : 0;
		$tuesday = !empty($tue) ? 1 : 0;
		$wednesday = !empty($wed) ? 1 : 0;
		$thursday = !empty($thu) ? 1 : 0;
		$friday = !empty($fri) ? 1 : 0;
		$saturday = !empty($sat) ? 1 : 0;
		$sunday = !empty($sun) ? 1 : 0;
		
		while($row = mysql_fetch_array($result)){
			$db_player_id = $row['player_id'];
			$db_player_name = $row['player_name'];
			$db_player_rank = $row['player_rank'];
			$mon = $row['monday'];
			$tue = $row['tuesday'];
			$wed = $row['wednesday'];
			$thu = $row['thursday'];
			$fri = $row['friday'];
			$sat = $row['saturday'];
			$sun = $row['sunday'];
			$updated = $row['updated'];
			
	?>
							<tr<?php echo $rowcount %2 != 0 ? ' class="odd"':''; ?>>
								<td class="first <?php echo $db_player_rank; ?>"><span class="nickname"><?php echo $db_player_name; ?></span></td>
								<td<?php echo $day == 'monday' ? ' class="highlight"':''; ?>><?php echo !empty($mon) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
								<td<?php echo $day == 'tuesday' ? ' class="highlight"':''; ?>><?php echo !empty($tue) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
								<td<?php echo $day == 'wednesday' ? ' class="highlight"':''; ?>><?php echo !empty($wed) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
								<td<?php echo $day == 'thursday' ? ' class="highlight"':''; ?>><?php echo !empty($thu) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
								<td<?php echo $day == 'friday' ? ' class="highlight"':''; ?>><?php echo !empty($fri) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
								<td<?php echo $day == 'saturday' ? ' class="highlight"':''; ?>><?php echo !empty($sat) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
								<td class="<?php echo $day == 'sunday' ? 'highlight ':''; ?>last"><?php echo !empty($sun) ? '<img src="star.png" alt="" />':'&nbsp;'; ?></td>
							</tr>
	<?php
			$monday = $monday + $mon;
			$tuesday = $tuesday + $tue;
			$wednesday = $wednesday+ $wed;
			$thursday = $thursday + $thu;
			$friday = $friday + $fri;
			$saturday = $saturday + $sat;
			$sunday = $sunday + $sun;
			++$rowcount;
		}
	?>
						</tbody>
						<tfoot>
							<tr>
								<th>&nbsp;</th>
								<th<?php echo $monday >= 15 ? ' class="green"':''; ?>><?php echo $monday; ?></th>
								<th<?php echo $tuesday >= 15 ? ' class="green"':''; ?>><?php echo $tuesday; ?></th>
								<th<?php echo $wednesday >= 15 ? ' class="green"':''; ?>><?php echo $wednesday; ?></th>
								<th<?php echo $thursday >= 15 ? ' class="green"':''; ?>><?php echo $thursday; ?></th>
								<th<?php echo $friday >= 15 ? ' class="green"':''; ?>><?php echo $friday; ?></th>
								<th<?php echo $saturday >= 15 ? ' class="green"':''; ?>><?php echo $saturday; ?></th>
								<th<?php echo $sunday >= 15 ? ' class="green"':''; ?>><?php echo $sunday; ?></th>
							</tr>
						</tfoot>
					</table>
				</fieldset>
				<input type="hidden" name="player_name" value="<?php echo $nick; ?>" />
				<input type="hidden" name="player_rank" value="<?php echo $role; ?>" />
				<input type="hidden" name="player_clan" value="<?php echo $clan; ?>" />
				<input type="hidden" name="player_id" value="<?php echo $_COOKIE['wotm8']['id']; ?>" />
<?php			}
			$tanksInGarageRequest = 'https://api.worldoftanks.eu/wot/tanks/stats/?application_id='.$applicationID.'&access_token='.$_COOKIE['wotm8']['token'].'&account_id='.$_COOKIE['wotm8']['id'];
			$json = file_get_contents($tanksInGarageRequest);
			$obj = json_decode($json);
			
			if(isset($obj->data)){
				foreach($obj->data->$id as $tank){
					if($tank->in_garage == 1){
						$tonks[] = $tank->tank_id;
					}
				}
								
				$tonkList = implode(',',$tonks);
				$tonkInfoRequest = 'https://api.worldoftanks.eu/wot/encyclopedia/tankinfo/?application_id='.$applicationID.'&tank_id='.$tonkList;
				$json = file_get_contents($tonkInfoRequest);
				$obj = json_decode($json);
				$tierArray = array();
				$player_tank_list = array();
				$cwtiers = array(6,8,10);
				if(isset($obj->data)){
					foreach($obj->data as $tank){
						$tierArray[$tank->level][] = array('image'=>$tank->contour_image, 'name'=>$tank->name_i18n, 'tank_id'=>$tank->tank_id,'type'=>$tank->type_i18n);
						if(in_array($tank->level, $cwtiers)){
							$player_tank_list[] = $tank->tank_id;
						}
					}
				}
				if($setup == false){
				
?>
				<input type="hidden" name="player_tank_list" value="<?php echo implode(',',$player_tank_list); ?>" />
				<p><input type="submit" name="submitButton" value="Submit" id="submit" /></p>
			</form>
<?php
		}  # BEGIN POSSIBLE SETUP VIEW
				$dayQuery = isset($day) ? ' AND '.$day.'=1':'';
				$result = mysql_query('SELECT tank_id FROM cwtanklist WHERE clan = "'.$clan.'"') or die (mysql_error());
				$wantedTonkList = array();
				while($row = mysql_fetch_array($result)){
					$wantedTonkList[] = $row['tank_id'];
				}
				$result = mysql_query('SELECT tanks, player_name FROM roster WHERE clan = "'.$clan.'"'.$dayQuery) or die (mysql_error());
				$tierArray = array();
				$tonkList = array();
				while($row = mysql_fetch_array($result)){
					$temp = explode(',',$row['tanks']);
					foreach($temp as $tank){
						if(in_array($tank, $wantedTonkList) || $setup == true){
							$tonkList[$tank][] = $row['player_name'];
						}
					}
				}
				$listForRequest = implode(',',array_keys($tonkList));
				$tonkInfoRequest = 'https://api.worldoftanks.eu/wot/encyclopedia/tankinfo/?application_id='.$applicationID.'&tank_id='.$listForRequest;
				$json = file_get_contents($tonkInfoRequest);
				$obj = json_decode($json);
				$tierArray = array();
				$player_tank_list = array();
				$cwtiers = array(6,8,10);

				if(isset($obj->data)){
					foreach($obj->data as $tank){
						$tonkCount = count($tonkList[$tank->tank_id]);
						$tierArray[$tank->level][] = array('image'=>$tank->contour_image, 'name'=>$tank->name_i18n, 'tank_id'=>$tank->tank_id,'type'=>$tank->type_i18n,'count'=>$tonkCount);
						if(in_array($tank->level, $cwtiers)){
							$player_tank_list[] = $tank->tank_id;
						}
					}
				}
	?>
			<div id="tanktable">
			<?php if($setup == false){ ?>
			<h2>Available preferred tanks<?php echo isset($day) ? ' on '.ucfirst($day):''; ?></h2>
			<?php } ?>
	<?php
				if($setup == true){
					echo '<h2>Preferred tanks for ['.$clan.']</h2>';
					echo '<form action="submitCWtanks.php" method="post">';
					$selectedTanks = array();
					$result = mysql_query('SELECT tank_id FROM cwtanklist WHERE clan = "'.$clan.'"') or die (mysql_error());
					while($row = mysql_fetch_array($result)){
						$selectedTanks[] = $row['tank_id'];
					}
				}
				if(!empty($tierArray)){
					$hasArty = array();
					$counter = 0;
					echo '<table class="tanks">';
					echo '<thead><tr>';
					echo $setup == true ? '<td>&nbsp;</td>':'';
					echo'<td>&nbsp;</td><td>Tier 6</td><td>#</td></tr></thead>';
					echo '<tbody>';
					if(isset($tierArray[6])){
						foreach($tierArray[6] as $tank){
							echo '<tr class="';
							echo $counter %2 == 0 ? 'odd':'';
							if($setup == true){
								echo in_array($tank['tank_id'], $selectedTanks) ? ' highlight':'';
							}
							echo '">';
							if($setup == true){
								echo '<td class="box"><input type="checkbox" name="tank[]" value="'.$tank['tank_id'].'"';
								echo in_array($tank['tank_id'], $selectedTanks) ? ' checked':'';
								echo '/></td>';
							}
							echo'<td class="icon"><img src="'.$tank['image'].'" alt="" /></td><td>' . $tank['name'] . '</td><td>'.$tank['count'].'</td></tr>';
							if($tank['type'] == 'SPG'){
								$hasArty[] = 6;
							}
							++$counter;
						}
						echo '</tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
						echo '<table class="tanks">';
						echo '<thead><tr>';
						echo $setup == true ? '<td>&nbsp;</td>':'';
						echo'<td>&nbsp;</td><td>Tier 8</td><td>#</td></tr></thead>';
						echo '<tbody>';
					}
					$counter = 0;
					if(isset($tierArray[8])){
						foreach($tierArray[8] as $tank){
							echo '<tr class="';
							echo $counter %2 == 0 ? 'odd':'';
							if($setup == true){
								echo in_array($tank['tank_id'], $selectedTanks) ? ' highlight':'';
							}
							echo '">';
							if($setup == true){
								echo '<td class="box"><input type="checkbox" name="tank[]" value="'.$tank['tank_id'].'"';
								echo in_array($tank['tank_id'], $selectedTanks) ? ' checked':'';
								echo '/></td>';
							}
							echo '<td class="icon"><img src="'.$tank['image'].'" alt="" /></td><td>' . $tank['name'] . '</td><td>'.$tank['count'].'</td></tr>';
							if($tank['type'] == 'SPG'){
								$hasArty[] = 8;
							}
							++$counter;
						}
						echo '</tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
						echo '<table class="tanks last">';
						echo '<thead><tr>';
						echo $setup == true ? '<td>&nbsp;</td>':'';
						echo '<td>&nbsp;</td><td>Tier 10</td><td>#</td></tr></thead>';
						echo '<tbody>';
					}
					$counter = 0;
					if(isset($tierArray[10])){
						foreach($tierArray[10] as $tank){
							echo '<tr class="';
							echo $counter %2 == 0 ? 'odd':'';
							if($setup == true){
								echo in_array($tank['tank_id'], $selectedTanks) ? ' highlight':'';
							}
							echo '">';
							if($setup == true){
								echo '<td class="box"><input type="checkbox" name="tank[]" value="'.$tank['tank_id'].'"';
								echo in_array($tank['tank_id'], $selectedTanks) ? ' checked':'';
								echo '/></td>';
							}
							echo '<td class="icon"><img src="'.$tank['image'].'" alt="" /></td><td>' . $tank['name'] . '</td><td>'.$tank['count'].'</td></tr>';
							if($tank['type'] == 'SPG'){
								$hasArty[] = 10;
							}
							++$counter;
						}
					}
					echo '</tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
					echo $setup == true ? '<div id="submitTanks"><input type="hidden" name="clan" value="'.$clan.'" /><input type="submit" name="submitCWtanks" value="Submit" /> or <a href="'.$baseURL.'">Return to Roster</a></div></form>':'';
					array_unique($hasArty);
					if(!empty($hasArty)){
						$hasArty = array_unique($hasArty);
						$arty = implode(', ',$hasArty);
						#echo '<p>Artillery available for tiers: '.$arty.'</p>';
					}
				}
			}
	?>
	</div> <!-- tanktable -->
	</div> <!-- container -->
	<footer>
		<p>Created by: karipatila [QSF-E]</p>
	</footer>
	<?php
	}
	?>	
	</body>
</html>