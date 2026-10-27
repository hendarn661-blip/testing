<?php
	#Production
    $db_database = 'db_539bc5a7';
    $db_hostname = 'db.fr-roub1.bengt.wasmernet.com';
    $db_username = 'user_eba1f0b5';
    $db_password = 'pw_YejpuJPuRwDoVZvDpSyXPIWgi5aTVpT4';
    
    #dev
	// $db_database = 'testing';
 //    $db_hostname = 'localhost';
 //    $db_username = 'rms';
 //    $db_password = 'curoot';
    $conn = mysqli_connect($db_hostname, $db_username, $db_password);
    if(!$conn) die("Unable to connect to MYSQL: ". mysqli_error());
    mysqli_select_db($conn, $db_database)
    or die("Unable to connect to database: " . mysqli_error());
?>
