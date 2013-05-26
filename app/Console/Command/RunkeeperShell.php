<?php

class RunkeeperShell extends AppShell {
	public $RK_API_URL = 'http://api.runkeeper.com';
	public $uses = array('User', 'Workout');

	public function main() {
		echo "hoge";
		$users = $this->User->find('all', array(
			'conditions' => array( 'type' => 'runkeeper' )
		));
		var_dump($users);
		foreach ($users as $u) {
			$this->fetchactivity($u['User']['rktoken']);
		}
	}

	private function fetchactivity($token) {
		$url = $this->RK_API_URL.'/fitnessActivities?access_token='.$token;
		$file = file_get_contents($url);

		$RK_activity_json = json_decode($file);
		foreach ($RK_activity_json->{'items'} as $i) {
			if ($i->{'has_path'}==true) {
				$url = $this->RK_API_URL.$i->{'uri'}.'?access_token='.$token;
				$file = file_get_contents($url);
				$RK_activity_detail_json = json_decode($file);
				var_dump($RK_activity_detail_json);
			}
		}




	}
}

?>
