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
				foreach ($act['points'] as $wkt) {
					preg_match('/, (\S+)/', $act['start_time'], $matches);
					$this->Runpoint->create();
					$this->Runpoint->replaceinto(
						array(
							'create_timestamp'=> "NOW()",
							'latlng'=>'POINT('.$wkt.')',
							'user'=>$u['User']['id']
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
				$activities[] = array("points"=> $points,
					"start_time"=> $RK_activity_detail_json->{'start_time'}
				);
			}
		}
		return $activities;
	}
}

?>
