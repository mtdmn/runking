<?php
include_once('GpxParser.php');

$value = file_get_contents($argv[1]);
$gpxp = new GpxParser($value, 'gpx');
$points = $gpxp->getRunpoints();
echo "latitude,longitude\n";
foreach ($points as $p) {
	preg_match('/(\S+) (\S+)/', $p, $matches);
    echo $matches[1].",".$matches[2]."\n";
}


