<?php
set_badge {

// Deze moet ik nog ophalen uit de database van HJB
$availability = date("d-m-Y", strtotime("2022-01-15 23:00:00"));
$newbadge = "vanaf ". (int) date("d", strtotime($availability))."-". (int) date("m", strtotime($availability));  

if (date("Y-m-d H:i:s") > $availability) { 
    $newbadge = "Direct";
    echo "badge gaan we aanpassen naar" .$badge ."<br>";   
}
else
{
    echo "Badge blijft hetzelfde ".$newbadge. "<br>";
}
    
}
 ?>