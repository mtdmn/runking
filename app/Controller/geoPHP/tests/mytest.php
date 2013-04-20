<?php
include_once('../geoPHP.inc');

$GEOUNIT = 3;
$GEOPOW = pow(10,$GEOUNIT);

$value = file_get_contents('./sample.gpx');
$geometry = geoPHP::load($value, 'gpx');
# print "numPoints:".$geometry->numPoints()."\n";
$geos = $geometry->geos();
$simp = $geos->simplify(.0005);

$geometry2 = geoPHP::load($simp,'wkt');
foreach ( $geometry2->getComponents() as $a ) {
	$newarray[] =  round($a->getX(),$GEOUNIT)." ".round($a->getY(),$GEOUNIT);
}
$geo3 = geoPHP::load('LINESTRING('.join($newarray,",").')','wkt');
# print $geo3->out('gpx');

$last_point = false;
$dots = array();

$x_max = false;
$y_max = false;
$x_min = false;
$y_min = false;

# convert path to dot, and get bounding box points.
foreach ( $geo3->getComponents() as $p ) {
	if ( $last_point == false ) {
		$x_max = $p->x() * $GEOPOW;
		$y_max = $p->y() * $GEOPOW;
		$x_min = $p->x() * $GEOPOW;
		$y_min = $p->y() * $GEOPOW;
		$last_point = $p;
		continue;
	}
	if ($x_max < $p->x() * $GEOPOW) { $x_max = $p->x() * $GEOPOW; }
	if ($y_max < $p->y() * $GEOPOW) { $y_max = $p->y() * $GEOPOW; }
	if ($x_min > $p->x() * $GEOPOW) { $x_min = $p->x() * $GEOPOW; }
	if ($y_min > $p->y() * $GEOPOW) { $y_min = $p->y() * $GEOPOW; }

	$dot_array = getPointsInPath($last_point, $p);
	$last_point = $p;

	$dots = array_merge($dots, $dot_array);
}

# ring search
$outdots = array();
$pathdots = array();
foreach ( $dots as $d ) {
	$pathdots[ $d->x ][ $d->y ] = 1;
}
explore( (int)$x_min,  (int)$y_max );
explore( (int)$x_min,  (int)$y_min );
explore( (int)$x_max,  (int)$y_max );
explore( (int)$x_max,  (int)$y_min );

function explore($x,$y) {
	global $x_max, $x_min, $y_max, $y_min, $outdots, $pathdots;
#	print "explore:($x,$y)\n";

	if ($x > $x_max ) { return; }
	if ($x < $x_min ) { return; }
	if ($y < $y_min ) { return; }
	if ($y > $y_max ) { return; }
	if (array_key_exists($x,$outdots)) {
		if (array_key_exists($y,$outdots[$x])) { return; }
	}
	if (array_key_exists($x,$pathdots)) {
		if (array_key_exists($y,$pathdots[$x])) { return; }
	}
	$outdots[$x][$y] = 1;
	explore($x-1,$y);
	explore($x+1,$y);
	explore($x,$y-1);
	explore($x,$y+1);
}

# invert ring
$indots = array();
for ($x = $x_min; $x <= $x_max; $x++) {
	for ($y = $y_min; $y <= $y_max; $y++) {
		if (array_key_exists((int)$x,$outdots)) {
			if (array_key_exists((int)$y,$outdots[$x])) {
				continue;
			} else {
				$indots[$x][$y] = 1;
			}
		}
	}
}

# output
foreach ( $indots as $x => $yarray ) {
	foreach ( $yarray as $y => $v ) {
		print $x/$GEOPOW.",".$y/$GEOPOW."\n";
	}
}

# output gpx
foreach ( $dots as $d ) {
	$hoge[] = $d->x/$GEOPOW." ".$d->y/$GEOPOW;
}
$geo4 = geoPHP::load('LINESTRING('.join($hoge,",").')','wkt');
# print $geo4->out('gpx');

# class and function definition
class Dot {
	public $x;
	public $y;

	public function __construct($X, $Y) {
		$this->x = $X;
		$this->y = $Y;
	}
}

function getPointsInPath ($p, $q) {	# path from Point:p to Point:q
	global $GEOUNIT,$GEOPOW;
	$ret = array();
#	print "p:".$p->x() ." ". $p->y()."\n";
#	print "q:".$q->x() ." ". $q->y()."\n";

	// case: same point
	if ( $p->x() == $q->x() and $p->y() == $q->y() ) {
		$ret[] = new Dot($GEOPOW*$p->x(), $GEOPOW*$p->y() );
		#print "same point\n";
		return $ret;
	}
	// case: dec=0
	if ($p->y() == $q->y()) {
		# print "dec = zero\n";
		$plus = 1;
		if ( $q->x() <  $p->x() ) {
			$plus = -1;
		}
		for ($a = $p->x()*$GEOPOW; $a*$plus <= $q->x()*$plus*$GEOPOW; $a+=$plus ) {
			$ret[] = new Dot($a, $p->y()*$GEOPOW);
		}
		return $ret;
	}
	// case: dec=infinite
	if ($p->x() == $q->x()) {
		# print "dec = infinite\n";
		$plus = 1;
		if ( $q->y() < $p->y() ) {
			$plus = -1;
		}
		for ($a = $p->y()*$GEOPOW; $a*$plus <= $q->y()*$plus*$GEOPOW; $a+=$plus ) {
			$ret[] = new Dot($p->x()*$GEOPOW, $a);
		}
		return $ret;
	}
	// case: dec=normal
	# print "dec = normal\n";

	// katamuki
	$dec = ($q->y() - $p->y()) / ($q->x() - $p->x());
	$y0 = $p->y()*$GEOPOW  - $dec * $p->x()*$GEOPOW;
	# print "dec:".$dec."\n";
	# print "y0:".$y0."\n";
	$xplus = 1;
	if ($q->x() < $p->x()) {
		$xplus = -1;
	}
	$yplus = 1;
	if ($q->y() < $p->y()) {
		$yplus = -1;
	}

	for($i = $p->x()*$GEOPOW;; $i+=$xplus)  {
		$y_left = $dec * ($i-0.5)+ $y0;
		$y_right = $dec * ($i+0.5) + $y0;
		# print "y_left:$y_left, y_right:$y_right\n";
		for($j = $p->y()*$GEOPOW;; $j+=$yplus)  {
			# print "i:".$i.",j:".$j;
			if ($y_left >= $j-0.5 && $y_left <= $j+0.5) {
				$ret[] = new Dot($i,$j);
			} else if ($y_right >= $j-0.5 && $y_right <= $j+0.5) {
				$ret[] = new Dot($i,$j);
			} else if ($y_right <= $j-0.5 && $y_left >= $j+0.5) {
				$ret[] = new Dot($i,$j);
			} else if ($y_right >= $j+0.5 && $y_left <= $j-0.5) {
				$ret[] = new Dot($i,$j);
			} else {
				# print "->OUT\n";
			}

			if ($j == $q->y() * $GEOPOW) {
				break;
			}
		}
		if ($i == $q->x() * $GEOPOW) {
			break;
		}
	}
	return $ret;
}

?>
