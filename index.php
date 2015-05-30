<?php
require "config.php";
chdir($GLOBALS['config']['path']);
$files = glob("*.".$GLOBALS['config']['ext']);
$files = array_reverse($files);

?>
<html>
<head>
<title>Error Log</title>
<style type="text/css">
	body {
		font-family:Arial;
		line-height:1.5;
		background-color:#555555;
	}
	
	.err {
		border:2px solid #AAAAAA;
		padding: 5px;
		background-color:#EEEEEE;
	}
	
	pre {
		background-color:#CDCDCD;;
		margin-left: 7px;
	}
</style>
</head>
<body>
<div>
<form action="index.php" method="post">
	<select name="file">
		<?php
			foreach($files as $file) {
				if(isset($_POST['file']) && $_POST['file'] == $file) $selected = " selected=\"selected\" "; else $selected = "";
				echo "<option $selected value=\"$file\">$file</option>";
			}
		?>
	</select>
	<input type="submit" value="OK" name="show" />
	<input type="submit" value="clear" name="clear" />
</form>
<div>


<?php 
if(isset($_POST['file'], $_POST['clear'])) {
	$filename = pathinfo($_POST['file'], PATHINFO_FILENAME);
	for($c=1;is_file($filename."-$c.txt");$c++);
	rename($_POST['file'], $filename."-$c.txt");
	$_POST['file'] = $filename."-$c.txt";
	?>
	<script type="text/javascript">this.location.href = this.location.href</script>
	<?php
}


if(isset($_POST['file'], $_POST['show'])) {
$lines = file($_POST['file']);
$errors = array();
$c = 0;
foreach($lines as $line) {
	if(trim($line) == "") continue;
	if(strpos($line, 'on line')) $c++;
	$time = substr($line, 1, 20);
	$msg = substr($line, 26);
	if(!isset($errors[$c]['time'])) {	
		$errors[$c]['time'] = $time;
		$errors[$c]['descr'] = $msg;
	} else {
		$errors[$c][] = $msg;
	}
}

?>

	<div style="margin-top: 30px">
		<?php
		foreach($errors as $error) {
			$time = $error['time'];
			$descr = $error['descr'];
			unset($error['time']);
			unset($error['descr']);
			echo "<div class=\"err\" style=\"margin-top:25px\">";
			echo "<b>$time</b>&nbsp;&nbsp;<i>$descr</i><br />";
			foreach($error as $line) {
				if(unserialize(trim($line))) {
					$var = unserialize(trim($line));
					echo "<pre>";
					var_dump($var);
					echo "</pre>";
				} elseif(json_decode(trim($line))) {
					$var = json_decode(trim($line));
					echo "<pre>";
					var_dump($var);
					echo "</pre>";
				} else {
					echo $line;
				}
				echo "<br />";
			}
			echo "</div>";
		}
		?>
	</div>
	
<?php
}
?>


</body>
</html>
