<?php 
include 'config.php';
include('simple_html_dom.php');

$loginUrl = 'https://siseveeb.khk.ee/ajax_send';
getUrl($loginUrl, 'post', $loginFields);

$table_subjects = getUrl('https://siseveeb.khk.ee/kutseope/oppetoo/paevik/ajax_cmd?cmd=k_daybook_opetaja_list_type', 'post', array('list' => 2013, 'filter_table' => true));

$subjects = str_get_html($table_subjects);
$gp = 0;
$GLOBALS["grade_period"] = $gp;

$groups = array();
$daybooks = array();
$periods = array();


foreach ($subjects->find('tr') as $tr) {

	$current_daybook = array();

	$columncount = 0;
	foreach ($tr->find('tbody>tr>td') as $i => $td) {
		$a = $td->find('a', 0);
		if ($a) {
			$subject_string = explode(' (', $a->plaintext);
			$subject_name = $subject_string[0];

			$subject_id_string = explode('paevik=', $a->href);
			$subject_id = $subject_id_string[1];


			if ($i === 0) {

				$periods = array();
				$current_daybook['id'] = $subject_id;

				if (!in_array($a->innertext, $groups))
					array_push($groups, $a->innertext);	
				$current_daybook['group'] = $a->innertext;
			}

			if ($i === 1) {
				$current_daybook['name'] = $subject_name;
				$current_daybook['href'] = $a->href;
				$current_daybook['fullname'] = $a->plaintext;

				$lessons_html = str_get_html(getUrl($a->href));

				$entries = $lessons_html->find('div[id=daybook_entries]', 0);
				$studentrows = $entries->find('.active_student');
				$studentCount = count($studentrows);
				$prds = 0;
				$grades = array();

				foreach ($studentrows as $j => $student) {
					$t = $student->find('td[class=daybook_R]');
					if ($j == 0) {
						$prds = count($t);
						for ($n=0; $n < $prds; $n++) { 
							$grades[$n] = 0;
						}
					}

					foreach ($t as $n => $g) {
						if ($g->plaintext != '') $grades[$n]++;
					}

				}

				$module = $entries->find('.moodul');
				if (count($module) > 0) array_pop($grades);

				$current_daybook['grades'] = $grades;
				
				$current_daybook['students'] = $studentCount;
				$pl = $lessons_html->find('span[id=palnned_size_number]', 0);
				$current_daybook['planned'] = $pl->plaintext;
			}

			$lessonCount = 0;
			$plannedCount = 0;

			if ($i > 1) {
				if (count($lessons = explode('/', $a->plaintext)) > 1) {
					$lessonCount = $lessons[0];
					$plannedCount = $lessons[1];
				} else {
					$lessonCount = 0;
					$plannedCount = $a->plaintext;		
				}
			}

			if ($i >= 2 && $i <= 9) {
				$periods[$i-1] = array();
				$periods[$i-1]['lessoncount'] = $lessonCount;
				$periods[$i-1]['planned'] = $plannedCount;
			}

			$columncount++;
		}
	}
	if (count($current_daybook) > 0) {
/*		$conn = new mysqli($server, $user, $pass, $database);
		$conn->set_charset("utf8");
		$q = "SELECT COUNT(*) AS lc FROM tunnid where subject_id=".$current_daybook['id'].";";
		$rs = $conn->query($q);
		$itt = $rs->fetch_assoc();
		$current_daybook['lessoncount'] = $itt['lc'];
		$conn->close();*/

		$current_daybook['periods'] = $periods;
		array_push($daybooks, $current_daybook);
	}
}





$sql = "TRUNCATE daybooks; INSERT INTO daybooks (id, name, fullname, planned, students, groupname, theory, p1c, p2c, p3c, p4c, p5c, p6c, p7c, p8c, p1p, p2p, p3p, p4p, p5p, p6p, p7p, p8p) VALUES ";

$sql_grades = "TRUNCATE grades; INSERT INTO grades (gradecount, daybook_id, period) VALUES ";
$grade_vals = "";
foreach ($daybooks as $daybook) {


	$did = $daybook['id'];
	$dname = $daybook['name'];
	$dfullname = $daybook['fullname'];
	$dplanned = $daybook['planned'];
	$dstudents = $daybook['students'];
	$dgroupname = $daybook['group'];

	if (strpos($dfullname, 'praktiline') !== FALSE) 
		$dtheory = 0;
	else
		$dtheory = 1;

	$periods = $daybook['periods'];
	$p1c = (isset($periods[1]))? $periods[1]['lessoncount'] : 0;
	$p2c = (isset($periods[2]))? $periods[2]['lessoncount'] : 0;
	$p3c = (isset($periods[3]))? $periods[3]['lessoncount'] : 0;
	$p4c = (isset($periods[4]))? $periods[4]['lessoncount'] : 0;
	$p5c = (isset($periods[5]))? $periods[5]['lessoncount'] : 0;
	$p6c = (isset($periods[6]))? $periods[6]['lessoncount'] : 0;
	$p7c = (isset($periods[7]))? $periods[7]['lessoncount'] : 0;
	$p8c = (isset($periods[8]))? $periods[8]['lessoncount'] : 0;
	$p1p = (isset($periods[1]))? $periods[1]['planned'] : 0;
	$p2p = (isset($periods[2]))? $periods[2]['planned'] : 0;
	$p3p = (isset($periods[3]))? $periods[3]['planned'] : 0;
	$p4p = (isset($periods[4]))? $periods[4]['planned'] : 0;
	$p5p = (isset($periods[5]))? $periods[5]['planned'] : 0;
	$p6p = (isset($periods[6]))? $periods[6]['planned'] : 0;
	$p7p = (isset($periods[7]))? $periods[7]['planned'] : 0;
	$p8p = (isset($periods[8]))? $periods[8]['planned'] : 0;

	$vals = "(".$did.", '".$dname."', '".$dfullname."', ".$dplanned.", ".$dstudents.", '".$dgroupname."', ".$dtheory.", ".$p1c.", ".$p2c.", ".$p3c.", ".$p4c.", ".$p5c.", ".$p6c.", ".$p7c.", ".$p8c.", ".$p1p.", ".$p2p.", ".$p3p.", ".$p4p.", ".$p5p.", ".$p6p.", ".$p7p.", ".$p8p."),";
	$sql .= $vals;

	foreach ($daybook['grades'] as $i => $grade) {
		$grade_vals .= "(".$grade.", ".$did.", ".($i+1)."),";
	}
}

$sql_grades .= substr($grade_vals, 0, -1)."; ";
$sql = substr($sql, 0, -1)."; ".$sql_grades;

include 'config.php';

$conn = new mysqli($server, $user, $pass, $database);
$conn->set_charset("utf8");
$conn->multi_query($sql);

// echo $sql.'<br>';
?>







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