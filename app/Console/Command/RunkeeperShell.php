<?php
include_once('GpxParser.php');

// dbに登録されている全runkeeperユーザの最新のworkoutをrunkeeperから引っ張ってきて、それをdbに登録する。

class RunkeeperShell extends AppShell {
	public $RK_API_URL = 'http://api.runkeeper.com';
	public $uses = array('User', 'Workout');

	public function main() {
		$users = $this->User->find('all', array(
			'conditions' => array( 'type' => 'runkeeper' )
		));
//		var_dump($users);
		foreach ($users as $u) {
			$this->fetchactivity($u['User']['rktoken']);
		}
	}

	private function fetchactivity($token) {
		$url = $this->RK_API_URL.'/fitnessActivities?access_token='.$token;
		$file = file_get_contents($url);

		$RK_activity_json = json_decode($file);
		$path = array();
		foreach ($RK_activity_json->{'items'} as $i) {
			if ($i->{'has_path'}==true) {
				$url = $this->RK_API_URL.$i->{'uri'}.'?access_token='.$token;
				$file = file_get_contents($url);
				$RK_activity_detail_json = json_decode($file);
				foreach ($RK_activity_detail_json->{'path'} as $p) {
					$path[] = $p->{'longitude'}. ' '. $p->{'latitude'};
				}
				$wkt = 'LINESTRING('. join(',', $path) .')';
				$gpxp = new GpxParser($wkt, 'wkt');
				$points = $gpxp->getRunpoints();
				echo "latitude,longitude\n";
				foreach ($points as $p) {
			        preg_match('/(\S+) (\S+)/', $p, $matches);
				    echo $matches[1].",".$matches[2]."\n";
				}
			}
		}
	}
}

?>
