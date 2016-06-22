<?php
include_once('class_and_config.php'); 
$admin = new risesite;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Application for download img</title>
	<script src="jquery-1.11.3.min.js"></script>
	<script src="script.js"></script>
	<link rel='stylesheet'  href='style.css' type='text/css' />
</head>
<body>
	<h3>Application for download img from<br /><?php echo "https://sg.wenthost.org/record/2033"; ?></h3>
	<?php echo "2";
	$page = file_get_contents("http://www.ex.ua/ru/video/foreign?r=23775");
	print_r($page);
		
	
	?>
</body>
</html>