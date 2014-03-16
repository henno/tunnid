<?php 
include 'config.php';
include('simple_html_dom.php');

$loginUrl = 'https://siseveeb.ee/tkhk/ajax_send';
getUrl($loginUrl, 'post', $loginFields);

$table_subjects = getUrl('https://siseveeb.ee/tkhk/kutseope/oppetoo/paevik/ajax_cmd?cmd=k_daybook_opetaja_list_type', 'post', array('list' => 2013, 'filter_table' => true));

$all_subjects = array();
$subjects = str_get_html($table_subjects);
$gp = 0;
$GLOBALS["grade_period"] = $gp;
foreach ($subjects->find('tr') as $tr) {

	$current_subject = array();

	foreach ($tr->find('tbody>tr>td') as $i => $td) {
		$a = $td->find('a', 0);
		if ($a) {
			$subject_string = explode(' (', $a->plaintext);
			$subject_name = $subject_string[0];

			$subject_id_string = explode('paevik=', $a->href);
			$subject_id = $subject_id_string[1];


			if ($i === 0) {
				$current_subject['id'] = $subject_id;
				$current_subject['group'] = $a->innertext;
			}

			if ($i === 1) {
				$current_subject['name'] = $subject_name;
				$current_subject['href'] = $a->href;
				$current_subject['fullname'] = $a->plaintext;

				$lessons_html = str_get_html(getUrl($a->href));

				$entries = $lessons_html->find('div[id=daybook_entries]', 0);
				$studentrows = $entries->find('.active_student');
				$studentCount = count($studentrows);
				$periods = 0;
				$grades = array();

				foreach ($studentrows as $j => $student) {
					$t = $student->find('td[class=daybook_R]');
					if ($j == 0) {
						$periods = count($t);
						for ($n=0; $n < $periods; $n++) { 
							$grades[$n] = 0;
						}
					}

					foreach ($t as $n => $g) {
						if ($g->plaintext != '') $grades[$n]++;
					}

				}

				$module = $entries->find('.moodul');
				if (count($module) > 0) array_pop($grades);

				$current_subject['grades'] = $grades;
				
				$current_subject['student_count'] = $studentCount;
				$pl = $lessons_html->find('span[id=palnned_size_number]', 0);
				$current_subject['planned'] = $pl->plaintext;
			}


			if ($i === 2)
				$current_subject['p1'] = $a->plaintext;
			if ($i === 3)
				$current_subject['p2'] = $a->plaintext;
			if ($i === 4)
				$current_subject['p3'] = $a->plaintext;
			if ($i === 5)
				$current_subject['p4'] = $a->plaintext;
			if ($i === 6)
				$current_subject['p5'] = $a->plaintext;
			if ($i === 7)
				$current_subject['p6'] = $a->plaintext;
			if ($i === 8)
				$current_subject['p7'] = $a->plaintext;
			if ($i === 9)
				$current_subject['p8'] = $a->plaintext;
		}
	}
	if (count($current_subject) > 0) {
		$conn = new mysqli($server, $user, $pass, $database);
		$conn->set_charset("utf8");
		$q = "SELECT COUNT(*) AS lc FROM tunnid where subject_id=".$current_subject['id'].";";
		$rs = $conn->query($q);
		$itt = $rs->fetch_assoc();
		$current_subject['lessoncount'] = $itt['lc'];
		$conn->close();

		array_push($all_subjects, $current_subject);
	}
}


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

<div class="container">
	<br>
	<a href="index.php">&larr; tagasi</a>
	<br>
	<div class="table-container">
		<table class="table table-subjects table-condensed">
			<thead>
				<tr>
					<th>Aine</th>
					<th>Grupp</th>
					<th>p1</th>
					<th>p2</th>
					<th>p3</th>
					<th>p4</th>
					<th>p5</th>
					<th>p6</th>
					<th>p7</th>
					<th>p8</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($all_subjects as $subject): ?>
				<?php $GLOBALS["grade_period"] = 0; ?>
				<tr>
					<td><a href="<?php echo $subject['href'] ?>" target="_new" title="<?php echo $subject['fullname'] ?>"><?php echo $subject['name'] ?></a></td>
					<td><?php echo $subject['group'] ?></td>
					
					<td><?php if (isset($subject['p1'])) { lessons2badge($subject['p1']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p2'])) { lessons2badge($subject['p2']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p3'])) { lessons2badge($subject['p3']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p4'])) { lessons2badge($subject['p4']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p5'])) { lessons2badge($subject['p5']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p6'])) { lessons2badge($subject['p6']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p7'])) { lessons2badge($subject['p7']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>
					<td><?php if (isset($subject['p8'])) { lessons2badge($subject['p8']); grades2badge($subject['grades'], $subject['student_count']); } ?></td>

				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>


</body>
</html>





<?php 
function getUrl($url, $method = '', $vars = '') {
	$ch = curl_init();
	if ($method == 'post') {
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
 	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$buffer = curl_exec($ch);
	curl_close($ch);
	return $buffer;
}
function lessons2badge($lesson){
	if (count($lessons = explode('/', $lesson)) > 1) {
		$color = ($lessons[0] >= $lessons[1])? 'label-success' : 'label-danger';
	} else 
		$color = 'label-danger';
	echo '<span class="label '.$color.'" title="Tunnid">'.$lesson.'</span>';
}
function grades2badge($grades, $count){
	if (($GLOBALS["grade_period"] < count($grades)) && (count($grades) > 0)) {
		$current = $grades[$GLOBALS["grade_period"]];
		if ($current < $count) $color = 'label-danger';
		else $color = 'label-success';
		echo '<span class="label '.$color.'" title="Hinded" >'.$current.'/'.$count.'</span>';
		$GLOBALS["grade_period"]++;
	} else {
		if (0 < $count) $color = 'label-danger';
		else $color = 'label-success';
		echo '<span class="label '.$color.'" title="Hinded" >0/'.$count.'</span>';
	}
}
?>