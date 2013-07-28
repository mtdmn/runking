<?php

include_once('runkeeper_settings.php');

class UsersController extends AppController {
	private $RK_access_token;
	private $RK_user_json;
	private $RK_profile_json;
	private $RK_API_URL = RK_API_URL;

	public function index() {
		$this->set('users', $this->User->find('all'));
	}

    public function authorize() {
        $this->redirect('https://runkeeper.com/apps/authorize?'.
			RK_APP_ID.
			'&redirect_uri='.
			'http://' . $_SERVER['HTTP_HOST'].'/cakephp/users/callback'.
			'&response_type=code'
			);
    }

    public function callback() {
		if (array_key_exists('error', $this->request->query)) {
			$this->set('contents', "authorization failed: ".$this->request->query['error']);
			return;
		}

		if ($this->request->is('get')) {
			$code = $this->request->query['code'];
		}

		$data = array(
			'grant_type' => 'authorization_code',
			'code' => $code,
			'client_id' => RK_APP_ID,
			'client_secret' => RK_APP_SECRET,
			'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'].'/cakephp/users/callback'
		);

		$headers = array(
		    'Content-Type: application/x-www-form-urlencoded',
		    'Content-Length: '.strlen(http_build_query($data))
        );

		$options = array(
		    'http' => array(
		        'method' => 'POST',
		        'content' => http_build_query($data),
		        'header' => implode("\r\n", $headers),
		    )
		);
						 
		$url = 'https://runkeeper.com/apps/token';
		$token_json = file_get_contents($url, false, stream_context_create($options));
		$obj = json_decode($token_json);
		$this->RK_access_token = $obj->{'access_token'};

		$this->loadRkUserData();

		// duplication check
		$count = $this->User->find('count', array(
			'conditions' => array('rkid' => $this->RK_user_json->{'userID'})
		));
		if ($count > 0) {
			$this->set('contents', "this user is already registered.");
		} else {

			// insert into db
			$data = array(
				'User' => array(
					'type' => 'runkeeper',
					'rkid' => $this->RK_user_json->{'userID'},
					'rkname' => $this->RK_profile_json->{'name'},
					'rktoken' => $this->RK_access_token
				)
			);
			// optional fields
			if (property_exists($this->RK_profile_json, 'gender'))
				$data['User']['rkgender'] = $this->RK_profile_json->{'gender'};
			if (property_exists($this->RK_profile_json, 'normal_picture'))
				$data['User']['rkpicture'] = $this->RK_profile_json->{'normal_picture'};
			$this->User->save($data);
			$this->set('contents', $this->RK_user_json->{'userID'});
		}
    }

	private function loadRkUserData() {
		$token = $this->RK_access_token;
		$url = $this->RK_API_URL.'/user?access_token='.$token;
		$file = file_get_contents($url);

		$this->RK_user_json = json_decode($file);
		$url = $this->RK_API_URL.$this->RK_user_json->{'profile'}.'?access_token='.$token;
		$file = file_get_contents($url);
		$this->RK_profile_json = json_decode($file);
	}
}

?>
