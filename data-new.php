<?php 
include 'config.php';

$conn = new mysqli($server, $user, $pass, $database);
if ($conn->connect_error) {
  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}
$conn->set_charset("utf8");

// VARS
$groups = array();
$subjects = array();
$table = '';
$lessonCount = 0;
$today = date('Ymd', strtotime('today'));
$lastDay = date('Ymd', strtotime('01.01.2000'));
$thisTime = date('H:i');
$time_from = date('H:i', strtotime('00:00'));
$time_to = date('H:i', strtotime('23:59'));

// TIME PERIOD
$period = '';
if (isset($_GET['date_from']) && isset($_GET['date_to'])) {
	if ($_GET['date_from'] != '' && $_GET['date_to'] != '') {
		$timeFrom = explode(' ', $_GET['date_from']);
		$timeTo = explode(' ', $_GET['date_to']);

		$date_from = date('Y-m-d', strtotime($timeFrom[0]));
		$date_to = date('Y-m-d', strtotime($timeTo[0]));

		if (count($timeFrom) > 1)
			$time_from = date('H:i', strtotime($timeFrom[1]));
		if (count($timeTo) > 1)
			$time_to = date('H:i', strtotime($timeTo[1]));
 
		$period = " WHERE lessondate >= '".$date_from."' AND lessondate <= '".$date_to."'";
	}
}

// GET DATA
$sql = "SELECT subject, lessondate, dayname, groupname, starttime, endtime, theory, room FROM tunnid ".$period." ORDER BY lessondate ASC, starttime ASC;";

$data = $conn->query($sql);
// echo $sql;

if($data === false)
  trigger_error('Error: ' . $conn->error, E_USER_ERROR);


// DATA LOOP
$data->data_seek(0);
while($row = $data->fetch_assoc()){

    $currentDay = date('Ymd', strtotime($row['lessondate']));

    $rowcolor = '';
    if ($today == $currentDay) {
    	$rowcolor = 'today';
    }

    // DATEROW
    if ($currentDay > $lastDay) {
    	$lastDay = $currentDay;
    	$table .= '<tr class="info daterow '.$rowcolor.'"><td><input type="checkbox" class="datecheck" /></td><td colspan="3">'.$row['dayname'].' '.date('d.m.Y', strtotime($lastDay)).'</td><td>'.date("H:i", strtotime($row["starttime"])).'</td><td class="date-end"></td><td></td></tr>';
    }

    $cD = date('Y-m-d', strtotime($row['lessondate']));
    if ((($date_from == $cD) && (date("H:i", strtotime($row["starttime"])) < $time_from)) || (($date_to == $cD) && (date("H:i", strtotime($row["endtime"])) > $time_to))) {
    	continue;
    }

    // LESSONROW
    $future = ($today < $currentDay)? 'text-muted' : '';
    if ($today == $currentDay) {
    	$future = (date('H:i', strtotime($row['starttime'])) > $thisTime)? 'text-muted' : '';
    	$rowcolor = (date('H:i', strtotime($row['starttime'])) < $thisTime && date('H:i', strtotime($row['endtime'])) > $thisTime)? 'current-lesson' : $rowcolor;
    }
    $theory = ($row['theory'] === 1)? 'subject-theory' : 'subject-computer';
	$lessonCount++;
	$table .= '<tr class="lessonrow '.$future.' '.$theory.' '.$rowcolor.'">'.
	'<td><input type="checkbox" class="rowcheck" /></td>'.
	'<td class="count">'.$lessonCount.'</td>'.
	'<td>'.$row["subject"].'</td>'.
	'<td>'.$row["groupname"].'</td>'.
	'<td>'.date("H:i", strtotime($row["starttime"])).'</td>'.
	'<td class="end-time">'.date("H:i", strtotime($row["endtime"])).'</td>'.
	'<td>'.$row["room"].'</td>'.
	'</tr>';

    // SELECT ARRAYS
    if (!in_array($row['groupname'], $groups) && $row['groupname'] !== '' && $row['groupname'] !== '-') {
		array_push($groups, $row['groupname']);
	}
    if (!in_array($row['subject'], $subjects) && $row['subject'] !== '') {
		array_push($subjects, $row['subject']);
	}
}

// SORT ARRAYS
natcasesort($groups); natcasesort($subjects);


// ARRAY TO SELECT OPTIONS
$groupSelect = '<option value="">Kõik grupid ('.count($groups).')</option>';
foreach ($groups as $group) {
	$groupSelect .= '<option>'.$group.'</option>';
}
$subjectSelect = '<option value="">Kõik ained ('.count($subjects).')</option>';
foreach ($subjects as $subject) {
	$subjectSelect .= '<option>'.$subject.'</option>';
}

echo json_encode(array('table'=> $table, 'groups'=> $groupSelect, 'subjects'=> $subjectSelect));

?>