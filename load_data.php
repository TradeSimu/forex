<?php

$symbols = array('GBPUSD', 'USDCAD', 'USDCHF', 'USDJPY');
$all_periods = array(60, 300, 600, 1800, 3600, 10800);


$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'forex';

$db_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db_conn->connect_errno)
{
	echo "Failed to connect " . $db_conn->connect_error;
}

foreach ($symbols as $symbol) {
    foreach ($all_periods as $period_seconds) {
        echo "Load $symbol $period_seconds\n";
        $create_sql = "
        CREATE TABLE IF NOT EXISTS $symbol (
            period_seconds int(11) NOT NULL DEFAULT '0',
            ds datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            bid_open double DEFAULT NULL,
            bid_high double DEFAULT NULL,
            bid_low double DEFAULT NULL,
            bid_close double DEFAULT NULL,
            ask_open double DEFAULT NULL,
            ask_high double DEFAULT NULL,
            ask_low double DEFAULT NULL,
            ask_close double DEFAULT NULL,
            PRIMARY KEY (period_seconds,ds)
        );
        ";
        //print($create_sql);

        if ($db_conn->query($create_sql) === TRUE) {
            echo "create tabke successful.\n";
        } else {
            echo "create table failed. " . $conn->error . " \n";
        }

        $load_sql ="
        LOAD DATA INFILE '/Users/dexin/work/forex/formatted/${period_seconds}_${symbol}_2013-05-12.csv' 
        INTO TABLE $symbol FIELDS TERMINATED BY ',' 
        (ds, bid_open, bid_high, bid_low, bid_close, ask_open, ask_high, ask_low, ask_close)
        SET period_seconds=$period_seconds;
        ";
        print($load_sql);

        if ($db_conn->query($load_sql) === TRUE) {
            echo "load successful.";
        } else {
            echo "load failed. " . $conn->error;
        }
    }
}
$db_conn->close();
