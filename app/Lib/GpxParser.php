<?php
include_once('geoPHP/geoPHP.inc');

# class and function definition
class Dot {
        public $x;
        public $y;
	
        public function __construct($X, $Y) {
                $this->x = $X;
                $this->y = $Y;
        }
}

class GpxParser {
	public $value;
	public $type;
	public $GEOUNIT;
	public $GEOPOW;
	public $x_max = false;
	public $y_max = false;
	public $x_min = false;
	public $y_min = false;
	public $outdots;
	public $pathdots;

	public function __construct($value, $type) {
		// constructor
		$this->value = $value;
		$this->type = $type;
		$this->GEOUNIT = 3;
		$this->GEOPOW = pow(10,$this->GEOUNIT);
	}

	public function getRunpoints() {
		$geometry = geoPHP::load($this->value, $this->type);
		$geos = $geometry->geos();
		$simp = $geos->simplify(.0005);

		$geometry2 = geoPHP::load($simp,'wkt');
		foreach ( $geometry2->getComponents() as $a ) {
			$newarray[] =  round($a->getX(),$this->GEOUNIT)." ".round($a->getY(),$this->GEOUNIT);
		}
		$geo3 = geoPHP::load('LINESTRING('.join($newarray,",").')','wkt');

		$last_point = false;
		$dots = array();

		$this->x_max = false;
		$this->y_max = false;
		$this->x_min = false;
		$this->y_min = false;

		# convert path to dot, and get bounding box points.
		foreach ( $geo3->getComponents() as $p ) {
		        if ( $last_point == false ) {
		                $this->x_max = $p->x() * $this->GEOPOW;
		                $this->y_max = $p->y() * $this->GEOPOW;
		                $this->x_min = $p->x() * $this->GEOPOW;
		                $this->y_min = $p->y() * $this->GEOPOW;
		                $last_point = $p;
		                continue;
		        }
		        if ($this->x_max < $p->x() * $this->GEOPOW)
					{ $this->x_max = $p->x() * $this->GEOPOW; }
		        if ($this->y_max < $p->y() * $this->GEOPOW) 
					{ $this->y_max = $p->y() * $this->GEOPOW; }
		        if ($this->x_min > $p->x() * $this->GEOPOW) 
					{ $this->x_min = $p->x() * $this->GEOPOW; }
		        if ($this->y_min > $p->y() * $this->GEOPOW) 
					{ $this->y_min = $p->y() * $this->GEOPOW; }
		
		        $dot_array = $this->getPointsInPath($last_point, $p);
		        $last_point = $p;
		
		        $dots = array_merge($dots, $dot_array);
		}
		
		# ring search
		$this->outdots = array();
		$this->pathdots = array();
		foreach ( $dots as $d ) {
		        $this->pathdots[ $d->x ][ $d->y ] = 1;
		}
		$this->explore( (int)$this->x_min,  (int)$this->y_max );
		$this->explore( (int)$this->x_min,  (int)$this->y_min );
		$this->explore( (int)$this->x_max,  (int)$this->y_max );
		$this->explore( (int)$this->x_max,  (int)$this->y_min );

		# invert ring
		$indots = array();
		for ($x = $this->x_min; $x <= $this->x_max; $x++) {
		        for ($y = $this->y_min; $y <= $this->y_max; $y++) {
		                if ( array_key_exists((int)$x,$this->outdots) &&
		                        array_key_exists((int)$y,$this->outdots[$x])) {
		                                continue;
	                    } else {
							$indots[$x][$y] = 1;
						}
		        }
		}
		
		foreach ( $indots as $x => $yarray ) {
			foreach ( $yarray as $y => $v ) {
		        $wkt[] = $y/$this->GEOPOW." ".$x/$this->GEOPOW;
			}
		}
		return $wkt;
	}


	private function getPointsInPath ($p, $q) {     # path from Point:p to Point:q
	        $GEOUNIT = $this->GEOUNIT;
			$GEOPOW = $this->GEOPOW;

	        $ret = array();
	#       print "p:".$p->x() ." ". $p->y()."\n";
	#       print "q:".$q->x() ." ". $q->y()."\n";
	
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

	private function explore($x,$y) {
#	       print "explore:($x,$y)\n";
	
	        if ($x > $this->x_max ) { return; }
	        if ($x < $this->x_min ) { return; }
	        if ($y < $this->y_min ) { return; }
	        if ($y > $this->y_max ) { return; }
	        if (array_key_exists($x,$this->outdots)) {
	                if (array_key_exists($y,$this->outdots[$x])) { return; }
	        }
	        if (array_key_exists($x,$this->pathdots)) {
	                if (array_key_exists($y,$this->pathdots[$x])) { return; }
	        }
	        $this->outdots[$x][$y] = 1;
	        $this->explore($x-1,$y);
	        $this->explore($x+1,$y);
	        $this->explore($x,$y-1);
	        $this->explore($x,$y+1);
	}
	
}
?>
