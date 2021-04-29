<?php

session_start(); //Start session for global variables

// Save the zone information
$_SESSION["Zone"] = $_POST["Zone"];
$_SESSION["ZoneID"] = $_POST["ZoneID"];


// Get the IP address for the host system
$command = "/sbin/ifconfig wlan0 | grep 'inet ' | cut -d: -f2 | awk '{print $2}'";
$_SESSION["LocalIP"] = exec($command);
// Create a meta-tag for the return
echo "<meta http-equiv=\"Refresh\" content=\"0; url='http://" . $_SESSION["LocalIP"] . "/controller.php'\" />";
?>
