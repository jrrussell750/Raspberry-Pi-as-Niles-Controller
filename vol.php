<?php

session_start();

$server_ip = '10.100.0.1';  //IP address of the Niles GXR2 
$server_port = 6001;  // UDP port number of the Niles GXR2
$InputSelector = $_POST["InputSelector"]; 
$zoneid = $_SESSION["ZoneID"]; 
$index = $zoneid - 1;
$zone_code = array("\x21", "\x22", "\x23", "\x24", "\x25", "\x26");

// Select input ID based on the value passed from the controller.php module

if ($InputSelector == 'Vol+')
{
  $inputid = "\x0c"; 
}
elseif ($InputSelector == 'Vol-')
{
  $inputid = "\x0d";
}

//Compose a volume up or down control message for the Niles GXR2

$message = "\x00\x12\x00" . $zone_code[$index] . "\x00\x0b\x61\x06" . $inputid . "\x00\xff";

// Open a udp socket to the GXR2

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, $_SESSION["LocalIP"], 6001);

// Send the volume up or down message five times.
// Five messages are needed because a single message by itself causes an incremental change
// that can barely be heard.

for ($x=1; $x<=5; $x++){
    socket_sendto($socket, $message, strlen($message), 0, $server_ip, $server_port);
}

socket_close($socket);
echo "<meta http-equiv = \"refresh\" content = \"0; URL=http://" . $_SESSION["LocalIP"] . "/controller.php\">";

?>
