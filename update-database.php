<?php 
include 'config.php';

$conn = new mysqli($server, $user, $pass, $database);
if ($conn->connect_error) {
  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}
$conn->set_charset("utf8");

// GET DATA
$timetable = array();

$firstDay = (date('m', strtotime('today')) < 9)? date('Ymd', strtotime('1st September last year')) : date('Ymd', strtotime('1st September this year'));
$year= date('m')>7 ? date('Y')+1 : date('Y');
$lastDay = date('Ymd', strtotime('31.08.'.$year));

$mondays = getDateForSpecificDayBetweenDates($firstDay, $lastDay, 1);
foreach ($mondays as $monday) {
	$data = file_get_contents('https://siseveeb.ee/tkhk/veebilehe_andmed/tunniplaan?opetaja=28243&nadal='.$monday);
	$data = json_decode($data, true);
	array_push($timetable, $data);
}

$paevad = array(
	'Mon' => 'Esmaspäev',
	'Tue' => 'Teisipäev',
	'Wed' => 'Kolmapäev',
	'Thu' => 'Neljapäev',
	'Fri' => 'Reede',
	'Sat' => 'Laupäev',
	'Sun' => 'Pühapäev',
);

$tobase = "INSERT INTO tunnid (subject, lessondate, dayname, groupname, starttime, endtime, theory, room) VALUES ";
foreach ($timetable as $week) {
	foreach ($week['tunnid'] as $key => $day) {
		$date = date('Y-m-d', strtotime(str_replace('-', '/', $key)));

		foreach ($day as $n => $lesson) {
			$theory = (strpos($lesson['ruum'], 'arvutiklass') !== FALSE)? 0 : 1;
			$group = ($lesson["grupp"] !== "")? $lesson["grupp"] : "-";
			$tobase .= "('".$lesson["aine"]."', '".$date."', '".$paevad[date('D', strtotime($date))]."', '".$group."', '".$lesson["algus"]."', '".$lesson["lopp"]."', '".$theory."', '".$lesson["ruum"]."' ),";
		}
	}
}

$tobase = substr($tobase, 0, -1);
$tobase .= "ON DUPLICATE KEY UPDATE theory=theory;";


// BEFORE INSERT
$before = "DELETE FROM tunnid WHERE lessondate > CURDATE();";
$conn->query($before);

if($conn->query($tobase) === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
} else {
  $last_inserted_id = $conn->insert_id;
  // echo 'Rows affected: '. $affected_rows = $conn->affected_rows .'<br>';
}

$updated = date("H:i d.m.Y");
if ($conn->query("UPDATE pagedata SET content = '". $updated ."' WHERE name = 'last_update';") === false)
  trigger_error('Error: ' . $conn->error, E_USER_ERROR);

echo $updated;

// GET MONDAYS
function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
{
	$sdo = $startDate;
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    if (date("w", $startDate) > 1) {
    	$startDate = strtotime($sdo.'-1 week');
    }

    $dateArr = array();

    do
    {
        if(date("w", $startDate) != $weekdayNumber)
        {
            $startDate += (24 * 3600); // add 1 day
        }
    } while(date("w", $startDate) != $weekdayNumber);


    while($startDate <= $endDate)
    {
        $dateArr[] = date('d.m.Y', $startDate);
        $startDate += (7 * 24 * 3600); // add 7 days
    }

    return($dateArr);
}
?>