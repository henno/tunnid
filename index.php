<?php 
include 'config.php';

$conn = new mysqli($server, $user, $pass, $database);
if ($conn->connect_error) {
  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}
$conn->set_charset("utf8");

$result = $conn->query("SELECT content FROM pagedata WHERE name = 'last_update';");
$row = $result->fetch_assoc();

$lastUpdate = $row['content'];
$updated = (date("H:i m.d.Y") - strtotime($lastUpdate));

$start_date = new DateTime(date('Y-m-d H:i', strtotime($lastUpdate)));
$since_start = $start_date->diff(new DateTime(date('Y-m-d H:i')));

$sinceUpdate = ($since_start->days)*24 + ($since_start->h);

if (date( "w", strtotime('now')) == 0) {
		$thisweek = date('d.m.Y', strtotime("Monday last week"));
		$nextweek = date('d.m.Y', strtotime($thisweek. "+1 week Friday"));
	} else {
		$thisweek = date('d.m.Y', strtotime("Monday this week"));
		$nextweek = date('d.m.Y', strtotime($thisweek. "+1 week Friday"));
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Tunnid</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
		<link href="css/jquery.datetimepicker.css" rel="stylesheet">
		<link href="css/main.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
<?php 
$today = date('d.m.Y', strtotime('today'));
$yearStart = (date('m', strtotime('today')) < 9)? date('d.m.Y', strtotime('1st September last year')) : date('d.m.Y', strtotime('1st September this year'));

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
?>
	<body class="<?php echo ($sinceUpdate >= 24)? 'should-update' : ''; ?>">
		<div class="container">
			<div class="navbar navbar-default">
				<form action="" class="navbar-form navbar-left">
						<div class="form-group">
							<input type="text" name="date_from" id="date_from" class="form-control date" value="<?php echo $thisweek; ?> 08:30" placeholder="Algus">
						</div>
						<div class="form-group">
							<input type="text" name="date_to" id="date_to" class="form-control date" value="<?php echo $nextweek; ?> 22:05" placeholder="Lõpp">
						</div>
						<div class="form-group">
							<select name="group" id="group-selector" class="form-control selector">
							</select>
						</div>
						<div class="form-group">
							<select name="lessontype" id="lessontype" class="form-control selector">
								<option value="">Kõik tunnid (2)</option>
								<option value="arvutiklass">Praktika</option>
								<option value="teooria">Teooria</option>
							</select>
						</div>
						<div class="form-group">
							<select name="subject" id="subject-selector" class="form-control selector">
							</select>
						</div>
						<div class="btn-group btn-group-sm">
						  <a href="#" class="btn btn-default" id="remove-filters" title="Eemalda filtrid"><i class="glyphicon glyphicon-remove"></i></a>
						  <a href="#" class="btn btn-default" id="update-database" title="Uuenda andmebaasi (<?php echo $lastUpdate; ?>)"><i class="glyphicon glyphicon-refresh"></i></a>
						</div>
				</form>
				<div class="navbar-right">
					<img src="362.GIF" class="loader" width="25" height="25" alt="">
				</div>
			</div>
			<div class="last-update"><?php echo $lastUpdate; ?></div>
			<div class="row">
				<div class="col-lg-12">
				<ul class="nav nav-pills menu-period">
					<li class="dropdown">
					    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
					    Perioodid <span class="caret"></span>
					    </a>
					    <ul class="dropdown-menu">
							<?php foreach ($perioodid as $key => $periood): ?>
								<li><a href="#" class="period" data-start="<?php echo $periood['start'] ?>" data-end="<?php echo $periood['end'] ?>"><?php echo ($key+1).'. periood ('.$periood['start'].'-'.$periood['end'].')' ?></a></li>
								<?php
									if ((date('Ymd', strtotime($periood['end'])) >= date('Ymd')) && (date('Ymd', strtotime($periood['start'])) <= date('Ymd'))) {
										$currentPeriod = $key;
									}
								?>
							<?php endforeach ?>
					    </ul>
					</li>
					<li><a href="#" class="period" data-start="<?php echo $thisweek; ?>" data-end="<?php echo $nextweek; ?>" >Jooksev ja järgmine nädal</a></li>
					<li><a href="#" class="period" data-start="<?php echo $perioodid[$currentPeriod]['start']; ?>" data-end="<?php echo $perioodid[$currentPeriod]['end']; ?>" >Jooksev periood</a></li>
					<li><a href="#" class="period" data-start="<?php echo $perioodid[$currentPeriod-1]['start']; ?>" data-end="<?php echo $perioodid[$currentPeriod]['end']; ?>" >Eelmine ja jooksev periood</a></li>
					<li><a href="#" class="period" data-start="<?php echo $yearStart; ?>" data-end="<?php echo $perioodid[7]['end']; ?>" >Jooksev õppeaasta</a></li>
				</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="process-buttons btn-group btn-group-xs">
						<button type="button" class="processed-remove btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
						<button type="button" class="processed-apply btn btn-success"><i class="glyphicon glyphicon-ok"></i></button>
					</div>
					<div class="table-container">
						<table id="maintable" class="table table-condensed">
							<thead>
								<tr>
									<th class="check-column"><input type="checkbox" id="checkAll"></th>
									<th class="header-rownum">Jrknr</th>
									<th>Aine</th>
									<th>Grupp</th>
									<th>Algus</th>
									<th>Lõpp</th>
									<th>Klass</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
		</div>


<div class="modal fade bs-example-modal-sm" id="alert" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body">
      <form id="passform" action="" method="post">
		<div class="row">
			<div class="col-md-9">
	      		<input type="password" id="update-password" class="form-control">
			</div>
			<div class="col-md-3">
		      	<button id="submit-password" required pattern=".{6,}" class="btn btn-success btn-block">Ok!</button>
			</div>
		</div>
      </form>
      </div>
    </div>
  </div>
</div>

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
		<script src="js/jquery.datetimepicker.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>