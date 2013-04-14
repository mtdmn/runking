<?php
class Runpoint extends AppModel {
	public $virtualFields = array('latlngtxt' => 'AsText(latlng)');
}
?>
