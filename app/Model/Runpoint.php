<?php
class Runpoint extends AppModel {
	public $virtualFields = array('latlngtxt' => 'AsText(latlng)');

	function replaceinto($data=null, $validate=true, $fieldList=array()) {
		$sql = "REPLACE INTO `runking`.`runpoints`
			(
				`change_timestamp`, `latlng`, `userid`, `workoutid`) VALUES 

			(
				".$data['change_timestamp'].",
				PointFromText('".$data['latlng']."') ,
				".$data['userid'].",
				".$data['workoutid']."
			)";

		return $this->query($sql);
	}
}
?>
