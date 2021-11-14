<?php 
/*$data = file_get_contents('https://siseveeb.khk.ee/veebilehe_andmed/tunniplaan?opetaja=28243&nadal=03.02.2014');
	print_r($data);*/
	echo array_sum(explode('+', str_replace(' ', '', "2 +2")));
?>