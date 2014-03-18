<?php 
include 'config.php';

include 'config.php';

$conn = new mysqli($server, $user, $pass, $database);
$conn->set_charset("utf8");
$dbs = $conn->query("SELECT * FROM daybooks ORDER BY groupname ASC");
$daybooks = $dbs->fetch_all(MYSQLI_ASSOC);

$perioodid = array(
array('start'=> '02.09.2013', 'end'=> '06.10.2013'),
array('start'=> '07.10.2013', 'end'=> '10.11.2013'),
array('start'=> '11.11.2013', 'end'=> '15.12.2013'),
array('start'=> '16.12.2013', 'end'=> '02.02.2014'),
array('start'=> '03.02.2014', 'end'=> '09.03.2014'),
array('start'=> '10.03.2014', 'end'=> '13.04.2014'),
array('start'=> '14.04.2014', 'end'=> '18.05.2014'),
array('start'=> '19.05.2014', 'end'=> '31.08.2014'),
);


$currentPeriod = 0;
$todayYmd = date('Ymd', strtotime('today'));
$GLOBALS['todayYmd'] = $todayYmd;
?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Tunnid</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/main.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
<body>
<div class="alert alert-info">
	<p># Andmete uuendamiseks mine <a href="subjects-update.php" target="_new">/subjects-update.php</a></p>
	<p># Kolmesed tunnid arvatavasti katki</p>
</div>
<div class="">
	<br>
	<a href="index.php">&larr; tagasi</a>
	<br>
	<div class="table-container">
		<table class="table table-subjects table-condensed table-bordered">
			<thead>
				<tr class="center">
					<th>Grupp</th>
					<th>Aine</th>
					<?php foreach ($perioodid as $i => $periood): ?>
						<?php $currentPeriod = (($todayYmd > date('Ymd', strtotime($periood['start']))) && ($todayYmd < date('Ymd', strtotime($periood['end']))))? ($i+1) : $currentPeriod; ?>
						<th colspan="4" class="<?php echo ($currentPeriod == ($i+1))? 'current-period' : '' ?>"><?php echo ($i+1); ?>. periood<br><?php echo substr($periood['start'], 0, -5) ?> - <?php echo substr($periood['end'], 0, -5) ?></th>
					<?php endforeach ?>
				</tr>
			</thead>
			<tbody>
				<tr class="subheader">
					<td colspan="2"></td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>
					<td>A</td>
					<td>T</td>
					<td>P</td>
					<td class="grades">H</td>

				</tr>
				<?php foreach ($daybooks as $db): ?>
				<?php
					$pgrades_temp = $conn->query("SELECT gradecount FROM grades WHERE daybook_id=".$db['id']." ORDER BY period ASC;");
					$pgrades = $pgrades_temp->fetch_all();
					// print_r($pgrades);

					// BY SUBJECT_ID
					$plessonsQuery = "select
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[0]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[0]['end']))."' AND subject_id = ".$db['id']."
						) as p1, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[1]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[1]['end']))."' AND subject_id = ".$db['id']."
						) as p2, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[2]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[2]['end']))."' AND subject_id = ".$db['id']."
						) as p3, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[3]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[3]['end']))."' AND subject_id = ".$db['id']."
						) as p4, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[4]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[4]['end']))."' AND subject_id = ".$db['id']."
						) as p5, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[5]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[5]['end']))."' AND subject_id = ".$db['id']."
						) as p6, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[6]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[6]['end']))."' AND subject_id = ".$db['id']."
						) as p7, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[7]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[7]['end']))."' AND subject_id = ".$db['id']."
						) as p8; 
					";

					// BY SUBJECT AND THEORY
/*					$plessonsQuery = "select
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[0]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[0]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p1, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[1]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[1]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p2, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[2]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[2]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p3, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[3]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[3]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p4, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[4]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[4]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p5, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[5]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[5]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p6, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[6]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[6]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p7, 
						(select count(*) from tunnid where lessondate > '".date('Y-m-d',strtotime($perioodid[7]['start']))."' AND lessondate < '".date('Y-m-d', strtotime($perioodid[7]['end']))."' AND theory = ".$db['theory']." AND subject='".$db['name']."'
						) as p8; 
					";*/

					$plessons_temp = $conn->query($plessonsQuery);
					$plessons = $plessons_temp->fetch_assoc();
				?>
				<tr>

					<td><?php echo $db['groupname'] ?></td>
					<td title="<?php echo $db['fullname'] ?>"><?php echo $db['name'] ?></td>

					<?php 
					for ($i=0; $i < 8; $i++) { 
						 getPeriod($db, $plessons, $i, $pgrades, $perioodid);
					}
					?>
					<?php // $periodCounter++; getPeriod($db, $plessons, $periodCounter, $pgrades); ?>
					<?php // $periodCounter++; getPeriod($db, $plessons, $periodCounter, $pgrades); ?>
					

				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>


</body>
</html>





<?php 
function getPeriod($db, $plessons, $periodCounter, $pgrades, $perioodid){ ?>
	<!-- X. PERIOD -->
	<?php 
	$enteredClass = '';
	if ($db['p'.($periodCounter+1).'c'] > 0) {
		if ($db['p'.($periodCounter+1).'c'] > ($plessons['p'.($periodCounter+1)] * 2)) $enteredClass = 'red';
		if ($db['p'.($periodCounter+1).'c'] == ($plessons['p'.($periodCounter+1)] * 2)) $enteredClass = 'green';
	}

	$inTTClass = '';
	if ($plessons['p'.($periodCounter+1)] > 0) {
		if (($plessons['p'.($periodCounter+1)] * 2) == $db['p'.($periodCounter+1).'p']) $inTTClass = 'green';
		if (($plessons['p'.($periodCounter+1)] * 2) < $db['p'.($periodCounter+1).'p'] && ($GLOBALS['todayYmd'] > date('Ymd', strtotime($perioodid[$periodCounter]['end'])))) $inTTClass = 'yellow';

	}

	$gradesClass = '';
	if ($db['p'.($periodCounter+1).'p'] > 0 && isset($pgrades[$periodCounter][0]) && $pgrades[$periodCounter][0] > 0) {
		if ($pgrades[$periodCounter][0] == $db['students']) $gradesClass = 'green';
		if ($pgrades[$periodCounter][0] < $db['students'] && ($GLOBALS['todayYmd'] > date('Ymd', strtotime($perioodid[$periodCounter]['end'])))) $gradesClass = 'red';
	}

	?>
	<td class="<?php echo $enteredClass ?>"><?php echo ($db['p'.($periodCounter+1).'c'] > 0)? $db['p'.($periodCounter+1).'c'] : '' ?></td>
	<td class="<?php echo $inTTClass ?>"><?php echo ($plessons['p'.($periodCounter+1)] > 0)? ($plessons['p'.($periodCounter+1)]*2) : '' ?></td>
	<td><?php echo ($db['p'.($periodCounter+1).'p'] > 0)? $db['p'.($periodCounter+1).'p'] : '' ?></td>
	<?php 
	if ($db['p'.($periodCounter+1).'p'] > 0)
		$cgp = (isset($pgrades[$periodCounter][0]) && $pgrades[$periodCounter][0] > 0)? $pgrades[$periodCounter][0].'/'.$db['students'] : '';
	else $cgp = '';
	?>
	<td class="<?php echo $gradesClass ?>"><?php echo $cgp ?></td>
<?php }
?>