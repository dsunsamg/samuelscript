<?php
$filename = "visits.txt";

// Read current count
$count = file_get_contents($filename);

// Increase by 1
$count++;

// Save back to file
file_put_contents($filename, $count);

// Output count
echo $count;
?>