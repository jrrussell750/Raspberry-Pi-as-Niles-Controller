<!-- This page displays the zone selection menu and passes the selected zone name to setzone.php -->
<!-- You should modify the zone names on the display to match your own GXR2 configuration.       -->

<html>
<head>
<title>Niles Zones</title>
<style type="text/css">

textarea {
width: 250px;
height: 5em;
}

input, select, textarea {
font-size: 150%;
}

</style>

</head>
<body>
<?php

//  Get current status from GXR2status.txt file, if it exists.

if (file_exists("/home/pi/GXR2status.txt")) {
     $myfile = fopen("/home/pi/GXR2status.txt", "r");    
    $i=0;
    while(!feof($myfile)) {
         $array[$i] = fgets($myfile);  // The first 6 elements in the array contain the status of each of the 6 zones
         $i = $i + 1;                  // The second 6 elements contain the volume levels for each zone
    }
     fclose($myfile);
} else {
  
//
//  If the GXR2status.txt file doesn's exist, populate the status array with zeroes.
//
     $i=0;
     while($i<12) {
          $array[$i] = "0";  // if the file doesn't exist, initialize the status array with zeros
          $i = $i + 1;
     }   

}

?>

<h1 align="center">Zones<br><h1>

<table id="table1" border="1" onclick="handleClick(event) width="30%" height="50%" align="center">
   

       <tr> 
           <td align="center">
                <form name="f1" action="./setzone.php" method="post">
                <input id="zone 1" type="submit" name="Zone" value="zone 1">
                <input id="1" type="hidden" name="ZoneID" value="1">
                </form>
		<?php echo "Device = ".$array[0]; ?>
                <?php echo "<br>Volume<br>level = ".$array[6]; ?>
           <td align="center">
                <form name="f2" action="./setzone.php" method="post">
                <input id="zone 2" type="submit" name="Zone" value="zone 2">
                <input id="2" type="hidden" name="ZoneID" value="2">
                </form>
		<?php echo "Device = ".$array[1]; ?>
                <?php echo "<br>Volume<br>level = ".$array[7]; ?>
       </tr>
       <tr>
           <td align="center">
                <form name="f3" action="./setzone.php" method="post">
                <input id="zone 3" type="submit" name="Zone" value="zone 3">
                <input id="3" type="hidden" name="ZoneID" value="3">
                </form>
                <?php echo "Device = ".$array[2]; ?>
                <?php echo "<br>Volume<br>level = ".$array[8]; ?>
           <td align="center">
                <form name="f4" action="./setzone.php" method="post">
                <input id="zone 4" type="submit" name="Zone" value="zone 4">
                <input id="4" type="hidden" name="ZoneID" value="4">
                </form>
                <?php echo "Device = ".$array[3]; ?>
                <?php echo "<br>Volume<br>level = ".$array[9]; ?>
       </tr>
       <tr>
           <td align="center">
                <form name="f5" action="./setzone.php" method="post">
                <input id="zone 5" type="submit" name="Zone" value="zone 5">
                <input id="5" type="hidden" name="ZoneID" value="5">
                </form>
                <?php echo "Device = ".$array[4]; ?>
                <?php echo "<br>Volume<br>level = ".$array[10]; ?>
           <td align="center">
                <form name="f6" action="./setzone.php" method="post">
                <input id="zone 6" type="submit" name="Zone" value="zone 6">
                <input id="6" type="hidden" name="ZoneID" value="6">
                </form>
                <?php echo "Device = ".$array[5]; ?>
                <?php echo "<br>Volume<br>level = ".$array[11]; ?>

        </tr>
</table>
</body>
</html>

