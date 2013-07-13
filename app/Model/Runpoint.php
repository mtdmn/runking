<?php
class Runpoint extends AppModel {
	public $virtualFields = array('latlngtxt' => 'AsText(latlng)');

	function replaceinto($data=null, $validate=true, $fieldList=array()) {
		$sql = "REPLACE INTO `runking`.`runpoints`
			(
				`create_timestamp`, `latlng`, `user`) VALUES 

			(
				".$data['create_timestamp'].",
				PointFromText('".$data['latlng']."') ,
				".$data['user']."
			)";

		return $this->query($sql);
	}
}
?>
