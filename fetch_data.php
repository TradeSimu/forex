<?php
//fetch_data.php?symbol=EURUSD&start=2013-01-01&end=2013-03-01

$symbol = $_GET['symbol'];
$start = $_GET['start'];
$end = $_GET['end'];

/*
$symbol = $argv[1];
$start = $argv[2];
$end = $argv[3];
*/

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'forex';

$db_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db_conn->connect_errno)
{
	echo "Failed to connect " . $db_conn->connect_error;
}

$start_ts = strtotime($start);
$end_ts = strtotime($end);
$time_span = $end_ts - $start_ts;
//print($time_span);
$available_period_sizes = array(60, 300, 600, 1800, 3600, 10800);
$num_points = 1000;
$choose_period_size = 60;
foreach ($available_period_sizes as $size)
{
	$choose_period_size = $size;
	if ($size * $num_points > $time_span)
		break;
}

$sql = "
	SELECT
		ds, bid_open, bid_high, bid_low, bid_close
	FROM $symbol
	WHERE ds >= '$start'
		AND ds <= '$end'
		AND period_seconds = $choose_period_size
";
//print($sql);
$result = $db_conn->query($sql);
while ($row = $result->fetch_assoc())
{
	$line = join(",", array_values($row));
	print($line);
	print("\n");
}
$db_conn->close();