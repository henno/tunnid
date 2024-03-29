<?php 
include 'config.php';
$loginUrl = 'https://siseveeb.voco.ee/ajax_send';
getUrl($loginUrl, 'post', $loginFields);
//now you're logged in and a session cookie was generated

// $remote_page_content = getUrl('https://siseveeb.voco.ee/info/meeldetuletused');

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

$table_subjects = getUrl('https://siseveeb.voco.ee/kutseope/oppetoo/paevik/ajax_cmd?cmd=k_daybook_opetaja_list_type', 'post', array('list' => 2013, 'filter_table' => true));

$all_data = array();
$all_subjects = array();

include('simple_html_dom.php');
$subjects = str_get_html($table_subjects);
foreach ($subjects->find('tr') as $tr) {

	$current_subject = array();

	foreach ($tr->find('td>span>a') as $i => $a) {

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

			$lessons_html = str_get_html(getUrl($a->href));
			$dates = $lessons_html->find('th.th_kuupaev'); // with K1, K2 ...

			$title = $lessons_html->find('div.program_div>h2>span[title]', 0);
			$group_name = trim($title->plaintext);



			foreach ($dates as $date) {
				if (strpos($date->class, 'daybook_R') == FALSE) { // if isnt K, K1, ...

					$lesson = array();
					$lesson['name'] = $subject_name;
					$d = $date->find('span', 0);
					$lesson['date'] = trim($d->plaintext);

					 // AINE ID!!!
					$lesson['subject_id'] = $subject_id;
					$lesson['group'] = $group_name;

					$link = $d->find('a[title]', 0);
					$amount = explode('t', $link->title);
					$lesson['amount'] = (int)$amount[0] / 2;

					$lessonlink = $link->href;
					$lesson_id = explode('tund=', $lessonlink);

					$lesson['lesson_id'] = $lesson_id[1];
					array_push($all_data, $lesson);
				}

			}
		}
	}
	if (count($current_subject) > 0)
		array_push($all_subjects, $current_subject);
}

$update_query = "";
foreach ($all_data as $row) {
	$the_date = explode('.', $row['date']); // 0 = day, 1 = month
	$update_query .= "UPDATE tunnid SET processed=1, subject_id=".$row['subject_id'].", lesson_id=".$row['lesson_id']." WHERE subject='".$row['name']."' AND dayofmonth(lessondate)=".$the_date[0]." AND month(lessondate)=".$the_date[1]." AND groupname='".$row['group']."' AND (processed=0 OR lesson_id IS NULL) LIMIT ".$row['amount'].";";
}

$subject_query = "";
foreach ($all_subjects as $row) {
	$subject_query .= "UPDATE tunnid set subject_id=".$row['id']." WHERE subject='".$row['name']."' AND groupname='".$row['group']."' AND processed=0; ";
}


$conn = new mysqli($server, $user, $pass, $database);
if ($conn->connect_error) {
  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}

$conn->set_charset("utf8");

if($conn->multi_query($update_query) === false) {
  trigger_error('Wrong SQL: ' . $update_query . ' Error: ' . $conn->error, E_USER_ERROR);
}
$conn->close();

$conn = new mysqli($server, $user, $pass, $database);
$conn->set_charset("utf8");
echo $conn->multi_query($subject_query).'<br>';
echo $subject_query;

?>