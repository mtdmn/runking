<?php
/**
 * Send mail using SMTP protocol
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake.libs.email
 * @since         CakePHP(tm) v 2.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Core', 'CakeSocket');

/**
 * SendEmail class
 *
 * @package       cake.libs.email
 */
class SmtpTransport extends AbstractTransport {

/**
 * Config
 *
 * @var array
 */
	protected $_config;

/**
 * Socket to SMTP server
 *
 * @var object CakeScoket
 */
	protected $_socket;

/**
 * CakeEmail
 *
 * @var object CakeEmail
 */
	protected $_cakeEmail;

/**
 * Send mail
 *
 * @params object $email CakeEmail
 * @return boolean
 * @thrown SocketException
 */
	public function send(CakeEmail $email) {
		$config = array(
			'host' => 'localhost',
			'port' => 25,
			'timeout' => 30,
			'username' => null,
			'password' => null,
			'client' => null
		);
		$userConfig = Configure::read('Email.Smtp');
		if (is_array($userConfig)) {
			$config = array_merge($config, array_change_key_case($userConfig, CASE_LOWER));
		}
		$this->_config = $config;
		$this->_cakeEmail = $email;

		$this->_connect();
		$this->_auth();
		$this->_sendRcpt();
		$this->_sendData();
		$this->_disconnect();

		return true;
	}

/**
 * Connect to SMTP Server
 *
 * @return void
 * @thrown SocketException
 */
	protected function _connect() {
		$this->_generateSocket();
		if (!$this->_socket->connect()) {
			throw new SocketException(__('Unable to connect in SMTP server.'));
		}
		$this->_smtpSend(null, '220');

		if (isset($this->_config['client'])) {
			$host = $this->_config['client'];
		} elseif ($httpHost = env('HTTP_HOST')) {
			list($host) = explode(':', $httpHost);
		} else {
			$host = 'localhost';
		}

		try {
			$this->_smtpSend("EHLO {$host}", '250');
		} catch (SocketException $e) {
			try {
				$this->_smtpSend("HELO {$host}", '250');
			} catch (SocketException $e2) {
				throw new SocketException(__('SMTP server not accepted the connection.'));
			}
		}
	}

/**
 * Send authentication
 *
 * @return void
 * @thrown SocketException
 */
	protected function _auth() {
		if (isset($this->_config['username']) && isset($this->_config['password'])) {
			$authRequired = $this->_smtpSend('AUTH LOGIN', '334|503');
			if ($authRequired == '334') {
				if (!$this->_smtpSend(base64_encode($this->_config['username']), '334')) {
					throw new SocketException(__('SMTP server not accepted the username.'));
				}
				if (!$this->_smtpSend(base64_encode($this->_config['password']), '235')) {
					throw new SocketException(__('SMTP server not accepted the password.'));
				}
			} elseif ($authRequired != '503') {
				throw new SocketException(__('SMTP do not require authentication.'));
			}
		}
	}

/**
 * Send emails
 *
 * @return void
 * @thrown SocketException
 */
	protected function _sendRcpt() {
		$from = $this->_cakeEmail->getFrom();
		$this->_smtpSend('MAIL FROM: ' . key($from));

		$to = $this->_cakeEmail->getTo();
		$cc = $this->_cakeEmail->getCc();
		$bcc = $this->_cakeEmail->getBcc();
		$emails = array_merge(array_keys($to), array_keys($cc), array_keys($bcc));
		foreach ($emails as $email) {
			$this->_smtpSend('RCPT TO: ' . $email);
		}
	}

/**
 * Send Data
 *
 * @return void
 * @thrown SocketException
 */
	protected function _sendData() {
		$this->_smtpSend('DATA', '354');

		$header = $this->_headersToString($this->_cakeEmail->getHeaders(true, false, true));
		$message = implode("\r\n", $this->_cakeEmail->getMessage());
		$this->_smtpSend($header . "\r\n\r\n" . $message . "\r\n\r\n\r\n.");
	}

/**
 * Disconnect
 *
 * @return void
 * @thrown SocketException
 */
	protected function _disconnect() {
		$this->_smtpSend('QUIT', false);
		$this->_socket->disconnect();
	}

/**
 * Helper method to generate socket
 *
 * @return void
 * @thrown SocketException
 */
	protected function _generateSocket() {
		$this->_socket = new CakeSocket($this->_config);
	}

/**
 * Protected method for sending data to SMTP connection
 *
 * @param string $data data to be sent to SMTP server
 * @param mixed $checkCode code to check for in server response, false to skip
 * @return void
 * @thrown SocketException
 */
	function _smtpSend($data, $checkCode = '250') {
		if (!is_null($data)) {
			$this->_socket->write($data . "\r\n");
		}
		while ($checkCode !== false) {
			$response = '';
			$startTime = time();
			while (substr($response, -2) !== "\r\n" && ((time() - $startTime) < $this->smtpOptions['timeout'])) {
				$response .= $this->_socket->read();
			}
			if (substr($response, -2) !== "\r\n") {
				throw new SocketException(__('SMTP timeout.'));
			}
			$response = end(explode("\r\n", rtrim($response, "\r\n")));

			if (preg_match('/^(' . $checkCode . ')(.)/', $response, $code)) {
				if ($code[2] === '-') {
					continue;
				}
				return $code[1];
			}
			throw new SocketException(__('SMTP Error: %s', $response));
		}
	}

}
