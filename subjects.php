<?php 
include 'config.php';
include('simple_html_dom.php');

$loginUrl = 'https://siseveeb.ee/tkhk/ajax_send';
getUrl($loginUrl, 'post', $loginFields);

$table_subjects = getUrl('https://siseveeb.ee/tkhk/kutseope/oppetoo/paevik/ajax_cmd?cmd=k_daybook_opetaja_list_type', 'post', array('list' => 2013, 'filter_table' => true));

$all_subjects = array();
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
			$current_subject['href'] = $a->href;

			$lessons_html = str_get_html(getUrl($a->href));

			$gl = $lessons_html->find('p>span[title=teoreetiline töö]', 0);
			$current_subject['given'] = $gl->plaintext;

			$pl = $lessons_html->find('span[id=palnned_size_number]', 0);
			$current_subject['planned'] = $pl->plaintext;
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
		<table class="table table-subjects">
			<thead>
				<tr>
					<th>Aine</th>
					<th>Grupp</th>
					<th>Planeeritud</th>
					<th>Tunniplaanis</th>
					<th>Sisse kantud</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($all_subjects as $subject): ?>
				<tr>
					<td><a href="<?php echo $subject['href'] ?>" target="_new"><?php echo $subject['name'] ?></a></td>
					<td><?php echo $subject['group'] ?></td>
					<td title="<?php echo $subject['planned'] ?>"><?php echo array_sum(explode('+', str_replace(' ', '', $subject['planned']))) ?></td>
					<td><?php echo $subject['lessoncount'] ?></td>
					<td><?php echo $subject['given'] ?></td>
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
?>