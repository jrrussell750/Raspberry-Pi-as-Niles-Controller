<?php
session_start();
?>

<html> 
<head> 
<title>Niles Controller</title> 
<style type="text/css">

textarea {
width: 250px;
height: 5em;
}

input, select, textarea {
font-size: 300%;
}

.f2{
      color: #FFFFFF;
      border: none;
      background-color: green;
      opacity:1.0;
}

</style>
</head> 
<body> 

<?php

// Create a page title that contains the zone name //

echo "<h1 align=\"center\">" . $_SESSION["Zone"] . "<h1>";

//  Get current status from GXR2status.txt file, if it exists.

if (file_exists("/var/www/html/GXR2status.txt")) {
     $myfile = fopen("/var/www/html/GXR2status.txt", "r");    
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

//  Save the selected zone and device status and volume in session variables so that they can be read by other pages

$i=$_SESSION["ZoneID"]-1;
$_SESSION["deviceID"] = $array[$i];
$_SESSION["Volume"] = $array[$i+6];

?>

<!-- Create a table that resembles the front panel of a Niles single controller -->
<!-- Each cell in the table has a form of type "submit".  When a button associated with a form is pushed, it causes information to be passed to a -->
<!-- designated php page for processing -->  

<table id="table1" border="1" width = "30%" height = "50%" align = "center">
       <tr> 

<!-- The first six entries in the table are input controls.  Input control is processed by niles.php -->

           <td align="Center" valign="middle" <?php if ($_SESSION["deviceID"] == 1) echo "bgcolor=\"green\">"; else echo "bgcolor=\"white\">";?> 
               <form name="f1" action="./niles.php" method="post">
                <input <?php if ($_SESSION["deviceID"] == 1) echo "class=\"f2\"";?>id="Input1" type="submit" name="InputSelector" value="Input1">
               </form>
           <td align="Center" <?php if ($_SESSION["deviceID"] == 2) echo "bgcolor=\"green\">"; else echo "bgcolor=\"white\">";?> 
               <form name="f2" action="./niles.php" method="post">
                <input <?php if ($_SESSION["deviceID"] == 2) echo "class=\"f2\"";?>id="Input2" type="submit" name="InputSelector" value="Input2">
               </form>
       </tr>
       <tr> 
           <td align="Center" <?php if ($_SESSION["deviceID"] == 3) echo "bgcolor=\"green\">"; else echo "bgcolor=\"white\">";?> 
               <form name="f3" action="./niles.php" method="post">
                <input <?php if ($_SESSION["deviceID"] == 3) echo "class=\"f2\"";?>id="Input3" type="submit" name="InputSelector" value="Input3">
               </form>
           <td align="Center" <?php if ($_SESSION["deviceID"] == 4) echo "bgcolor=\"green\">"; else echo "bgcolor=\"white\">";?> 
               <form name="f4" action="./niles.php" method="post">
                <input <?php if ($_SESSION["deviceID"] == 4) echo "class=\"f2\"";?>id="Input4" type="submit" name="InputSelector" value="Input4">
               </form>
       </tr>
       <tr> 
           <td align="Center" <?php if ($_SESSION["deviceID"] == 5) echo "bgcolor=\"green\">"; else echo "bgcolor=\"white\">";?> 
               <form name="f5" action="./niles.php" method="post">
                <input <?php if ($_SESSION["deviceID"] == 5) echo "class=\"f2\"";?>id="Input5" type="submit" name="InputSelector" value="Input5">
               </form>

           <td align="Center" <?php if ($_SESSION["deviceID"] == 6) echo "bgcolor=\"green\">"; else echo "bgcolor=\"white\">";?> 
               <form name="f6" action="./niles.php" method="post">
                <input <?php if ($_SESSION["deviceID"] == 6) echo "class=\"f2\"";?>id="Input6" type="submit" name="InputSelector" value="Input6">
               </form>
       </tr>
       <tr> 
 
 <!--  The next two entries in the table are volume controls.  Volume is processed by vol.php  -->
 
           <td align="Center">
               <form name="f7" action="./vol.php" method="post">
                <input id="Volume Dn" type="submit" name="InputSelector" value="Vol-">
               </form>

           <td align="Center">
               <form name="f8" action="./vol.php" method="post">
                <input id="Volume Up" type="submit" name="InputSelector" value="Vol+">
               </form>
       </tr>
       <tr> 
           <td align="Center">
                
<!-- The next two entries in the table are navigation controls (Prev and Next).  They are processed by .nav.php -->
                
               <form name="f9" action="./nav.php" method="post">
                <input id="Prev" type="submit" name="InputSelector" value="Prev">
               </form>

           <td align="Center">
               <form name="f10" action="./nav.php" method="post">
                <input id="Next" type="submit" name="InputSelector" value="Next">
               </form>
       </tr>
       <tr> 
            
<!-- This cell is for the "Off" command.  The "Off" command is processed by niles.php.-->

           <td align="Center">
               <form name="f11" action="./niles.php" method="post">
                <input id="Off" type="submit" name="InputSelector" value="Off">
               </form>
           <td align="Center">
                
<!--  This cell returns to the main screen  -->              
                
               <form name="f12" action="./index.html" method="post">
                <input id="Zones" type="submit" name="InputSelector" value="Zones">

               </form>
 
 
       </tr>
</table>


<h1 align="center">Volume Level = <?php echo $_SESSION["Volume"] ?></h1>

<table align="Center">
       <tr>
          <td align="Center">  

<!-- This cell is for the "AllOff" command, which turns off all the zones.  
<!-- The "AllOff" command is processed by niles.php.-->

                <form name="f13" action="./alloff.php" method="post">
                <input id="All zones Off" type="submit" name="InputSelector" value="All Zones Off">
               </form>
          </td>
       </tr>
</table>

<!-- Refresh the screen every 5 seconds to update status -->

<meta http-equiv="refresh" content="5">


</body>
</html>
