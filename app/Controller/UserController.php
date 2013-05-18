<?php

class UserController extends AppController {
    public function authorize() {
        $this->redirect('https://runkeeper.com/apps/authorize?'.
			'client_id=fa85c607244c491f825f66e8dcf704ef'.
			'&redirect_uri='.
			'http://' . $_SERVER['HTTP_HOST'].'/cakephp/user/callback'.
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
			'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'].'/cakephp/user/callback'
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
		$contents = file_get_contents($url, false, stream_context_create($options));
// {"token_type":"Bearer","access_token":"d3819b92bb874523b9c0e18ba9351c51"}
    }
}

?>
