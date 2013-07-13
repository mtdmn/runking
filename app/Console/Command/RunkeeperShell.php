<?php
include_once('GpxParser.php');

// dbに登録されている全runkeeperユーザの最新のworkoutをrunkeeperから引っ張ってきて、それをdbに登録する。

class RunkeeperShell extends AppShell {
	public $RK_API_URL = 'http://api.runkeeper.com';
	public $uses = array('User', 'Workout', 'Runpoint');

	public function main() {
		$activities;
		$users = $this->User->find('all', array(
			'conditions' => array( 'type' => 'runkeeper' )
		));
		foreach ($users as $u) {
			$activities = $this->fetchactivity($u['User']['rktoken']);
			foreach ($activities as $act) {
				$time = strtotime($act['start_time']);
				$mytime = date('Y-m-d H:i:s', $time);
				$nowtime = date('Y-m-d H:i:s');
				$result = $this->Workout->find('first', array(
				        'conditions' => array('rkactid' => $act['actid'])));
				if ($result) continue;
				$this->Workout->create( array("Workout"=> array(
					"userid" => $u['User']['id'],
					"rkactid" => $act['actid'],
					"starttime" => $mytime,
					"importtime" => $nowtime,
					"type" => $act['type'] 
				)));
				$this->Workout->save();
				$workoutid = $this->Workout->id;

				foreach ($act['points'] as $wkt) {
					$this->Runpoint->create();
					$this->Runpoint->replaceinto(
						array(
							'change_timestamp' => "'".$mytime."'",
							'latlng' => 'POINT('.$wkt.')',
							'workoutid' => $workoutid,
							'userid' => $u['User']['id']
						)
					);
				}
			}
		}
	}

	private function fetchactivity($token) {
		$url = $this->RK_API_URL.'/fitnessActivities?access_token='.$token;
		$file = file_get_contents($url);

		$RK_activity_json = json_decode($file);
		$activities = array();
		foreach ($RK_activity_json->{'items'} as $i) {
			if ($i->{'has_path'}==true) {
				$path = array();
				$url = $this->RK_API_URL.$i->{'uri'}.'?access_token='.$token;
				$file = file_get_contents($url);
				$RK_activity_detail_json = json_decode($file);
				foreach ($RK_activity_detail_json->{'path'} as $p) {
					$path[] = $p->{'longitude'}. ' '. $p->{'latitude'};
				}
				$wkt = 'LINESTRING('. join(',', $path) .')';
				$gpxp = new GpxParser($wkt, 'wkt');
				$points = $gpxp->getRunpoints();
				$activities[] = array(
					"points"=> $points,
					"start_time"=> $RK_activity_detail_json->{'start_time'},
					"type" => $RK_activity_detail_json->{'type'},
					"actid" => $RK_activity_detail_json->{'uri'}
				);
			}
		}
		return $activities;
	}
}

?>
