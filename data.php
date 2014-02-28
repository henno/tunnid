<?php 

$timetable = array();
if (!isset($_GET['date_from'])) {
	if (date( "w", strtotime('now')) == 0) {
		$thisweek = date('d.m.Y', strtotime("Monday last week"));
	} else {
		$thisweek = date('d.m.Y', strtotime("Monday this week"));
	}

	$firstDay = date('Ymd', strtotime($thisweek));
	$lastDay = date('Ymd', strtotime($thisweek.'+1 week'));

	$data = file_get_contents('https://siseveeb.ee/tkhk/veebilehe_andmed/tunniplaan?opetaja=28243&nadal='.$thisweek);
	$data = json_decode($data, true);
	array_push($timetable, $data);
} else {
	$firstDay = date('Ymd', strtotime($_GET['date_from']));
	$lastDay = date('Ymd', strtotime($_GET['date_to']));

	$mondays = getDateForSpecificDayBetweenDates($_GET['date_from'], $_GET['date_to'], 1);
	foreach ($mondays as $monday) {
		$data = file_get_contents('https://siseveeb.ee/tkhk/veebilehe_andmed/tunniplaan?opetaja=28243&nadal='.$monday);
		$data = json_decode($data, true);
		array_push($timetable, $data);
	}
}

$table = '';
$groups = array();
$subjects = array();

$paevad = array(
	'Mon' => 'Esmaspäev',
	'Tue' => 'Teisipäev',
	'Wed' => 'Kolmapäev',
	'Thu' => 'Neljapäev',
	'Fri' => 'Reede',
	'Sat' => 'Laupäev',
	'Sun' => 'Pühapäev',
);
$today = date('Ymd', strtotime('today'));

$count = 0;
foreach ($timetable as $week) {
	foreach ($week['tunnid'] as $key => $day) {
		$currentDay = date('Ymd', strtotime($key));
		// echo $currentDay;
		if (($firstDay <= $currentDay) && ($currentDay <= $lastDay)) {
			$future = ($today <= $currentDay)? 'text-muted' : '';
			$table .= '<tr class="info daterow"><td></td><td colspan="5">'.$paevad[date('D', strtotime($key))].' '.date('d.m.Y', strtotime($key)).'</td></tr>';
			foreach ($day as $n => $lesson) {
				$count++;
				$table .= '<tr class="lessonrow '.$future.'">'.
				'<td class="count">'.$count.'</td>'.
				'<td>'.$lesson["aine"].'</td>'.
				'<td>'.$lesson["grupp"].'</td>'.
				'<td>'.$lesson["algus"].'</td>'.
				'<td>'.$lesson["lopp"].'</td>'.
				'<td>'.$lesson["ruum"].'</td>'.
				'</tr>';

				if (!in_array($lesson['grupp'], $groups) && $lesson['grupp'] !== '') {
					array_push($groups, $lesson['grupp']);
				}
				if (!in_array($lesson['aine'], $subjects) && $lesson['aine'] !== '') {
					array_push($subjects, $lesson['aine']);
				}
			}
		}
	}
}

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

asort($groups); asort($subjects);
$gselect = '<option value="">Kõik grupid ('.count($groups).')</option>';
foreach ($groups as $group) {
	$gselect .= '<option>'.$group.'</option>';
}
$subjectSelect = '<option value="">Aine</option>';
foreach ($subjects as $subject) {
	$subjectSelect .= '<option>'.$subject.'</option>';
}

echo json_encode(array('table'=>$table, 'groups'=>$gselect, 'subjects'=>$subjectSelect));
?>