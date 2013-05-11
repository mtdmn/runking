<?php
class Runpoint extends AppModel {
	public $virtualFields = array('latlngtxt' => 'AsText(latlng)');

/*
	function set($params = array()) {
		if (isset($params['latlng'])) {
			$params['latlng'] = DboSource::expression('PointFromText("'. $params['latlng'].'")');
		}

		return parent::set($params);
	}

	// override
	function save($data=null, $validate=true, $fieldList=array()) {

		if (isset($data['Runpoint']['latlng'])) {
			$data['Runpoint']['latlng'] = DboSource::expression('PointFromText("'. $data['Runpoint']['latlng'].'")');
		}

		return parent::save($data, $validate, $fieldList);
	}
	*/

	function replaceinto($data=null, $validate=true, $fieldList=array()) {
		$sql = "REPLACE INTO `runking`.`runpoints` (`create_timestamp`, `latlng`) VALUES (".$data['create_timestamp'].", PointFromText('".$data['latlng']."'))";

		return $this->query($sql);
	}
}
?>
