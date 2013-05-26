<?php
include_once('RkLib.php');

class UsersController extends AppController {
	private $RK_access_token;
	private $RK_user_json;
	private $RK_profile_json;
	private $RK_API_URL = 'http://api.runkeeper.com';

	public function index() {
		$this->set('users', $this->User->find('all'));
	}

    public function authorize() {
        $this->redirect('https://runkeeper.com/apps/authorize?'.
			'client_id=fa85c607244c491f825f66e8dcf704ef'.
			'&redirect_uri='.
			'http://' . $_SERVER['HTTP_HOST'].'/cakephp/users/callback'.
			'&response_type=code'
			);
    }

    public function callback() {
		if ($this->request->is('get')) {
			$code = $this->request->query['code'];
		}

		$data = array(
			'grant_type' => 'authorization_code',
			'code' => $code,
			'client_id' => 'fa85c607244c491f825f66e8dcf704ef',
			'client_secret' => 'c2f2397af19143338a5c2776dcc59f3c',
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
					'rkgender' => $this->RK_profile_json->{'gender'},
					'rkpicture' => $this->RK_profile_json->{'normal_picture'},
					'rktoken' => $this->RK_access_token
				)
			);
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
