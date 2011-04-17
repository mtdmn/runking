<?php
/**
 * Email Component
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake.libs.controller.components
 * @since         CakePHP(tm) v 1.2.0.3467
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');
App::uses('Multibyte', 'I18n');
App::uses('CakeEmail', 'Network');

/**
 * EmailComponent
 *
 * This component is used for handling Internet Message Format based
 * based on the standard outlined in http://www.rfc-editor.org/rfc/rfc2822.txt
 *
 * @package       cake.libs.controller.components
 * @link http://book.cakephp.org/view/1283/Email
 *
 */
class EmailComponent extends Component {

/**
 * Recipient of the email
 *
 * @var string
 * @access public
 */
	public $to = null;

/**
 * The mail which the email is sent from
 *
 * @var string
 * @access public
 */
	public $from = null;

/**
 * The email the recipient will reply to
 *
 * @var string
 * @access public
 */
	public $replyTo = null;

/**
 * The read receipt email
 *
 * @var string
 * @access public
 */
	public $readReceipt = null;

/**
 * The mail that will be used in case of any errors like
 * - Remote mailserver down
 * - Remote user has exceeded his quota
 * - Unknown user
 *
 * @var string
 * @access public
 */
	public $return = null;

/**
 * Carbon Copy
 *
 * List of email's that should receive a copy of the email.
 * The Recipient WILL be able to see this list
 *
 * @var array
 * @access public
 */
	public $cc = array();

/**
 * Blind Carbon Copy
 *
 * List of email's that should receive a copy of the email.
 * The Recipient WILL NOT be able to see this list
 *
 * @var array
 * @access public
 */
	public $bcc = array();

/**
 * The date to put in the Date: header.  This should be a date
 * conformant with the RFC2822 standard.  Leave null, to have
 * today's date generated.
 *
 * @var string
 */
	var $date = null;

/**
 * The subject of the email
 *
 * @var string
 * @access public
 */
	public $subject = null;

/**
 * Associative array of a user defined headers
 * Keys will be prefixed 'X-' as per RFC2822 Section 4.7.5
 *
 * @var array
 * @access public
 */
	public $headers = array();

/**
 * List of additional headers
 *
 * These will NOT be used if you are using safemode and mail()
 *
 * @var string
 * @access public
 */
	public $additionalParams = null;

/**
 * Layout for the View
 *
 * @var string
 * @access public
 */
	public $layout = 'default';

/**
 * Template for the view
 *
 * @var string
 * @access public
 */
	public $template = null;

/**
 * as per RFC2822 Section 2.1.1
 *
 * @var integer
 * @access public
 */
	public $lineLength = 70;

/**
 * Line feed character(s) to be used when sending using mail() function
 * By default PHP_EOL is used.
 * RFC2822 requires it to be CRLF but some Unix
 * mail transfer agents replace LF by CRLF automatically
 * (which leads to doubling CR if CRLF is used).
 *
 * @var string
 * @access public
 */
	var $lineFeed = PHP_EOL;

/**
 * @deprecated see lineLength
 */
	protected $_lineLength = null;

/**
 * What format should the email be sent in
 *
 * Supported formats:
 * - text
 * - html
 * - both
 *
 * @var string
 * @access public
 */
	public $sendAs = 'text';

/**
 * What method should the email be sent by
 *
 * Supported methods:
 * - mail
 * - smtp
 * - debug
 *
 * @var string
 * @access public
 */
	public $delivery = 'mail';

/**
 * charset the email is sent in
 *
 * @var string
 * @access public
 */
	public $charset = 'utf-8';

/**
 * List of files that should be attached to the email.
 *
 * Can be both absolute and relative paths
 *
 * @var array
 * @access public
 */
	public $attachments = array();

/**
 * What mailer should EmailComponent identify itself as
 *
 * @var string
 * @access public
 */
	public $xMailer = 'CakePHP Email Component';

/**
 * The list of paths to search if an attachment isnt absolute
 *
 * @var array
 * @access public
 */
	public $filePaths = array();

/**
 * List of options to use for smtp mail method
 *
 * Options is:
 * - port
 * - host
 * - timeout
 * - username
 * - password
 * - client
 *
 * @var array
 * @access public
 * @link http://book.cakephp.org/view/1290/Sending-A-Message-Using-SMTP
 */
	public $smtpOptions = array();

/**
 * Placeholder for any errors that might happen with the
 * smtp mail methods
 *
 * @var string
 * @access public
 */
	public $smtpError = null;

/**
 * Contains the rendered plain text message if one was sent.
 *
 * @var string
 * @access public
 */
	public $textMessage = null;

/**
 * Contains the rendered HTML message if one was sent.
 *
 * @var string
 * @access public
 */
	public $htmlMessage = null;

/**
 * Whether to generate a Message-ID header for the
 * e-mail. True to generate a Message-ID, False to let
 * it be handled by sendmail (or similar) or a string
 * to completely override the Message-ID.
 *
 * If you are sending Email from a shell, be sure to set this value.  As you
 * could encounter delivery issues if you do not.
 *
 * @var mixed
 * @access public
 */
	public $messageId = true;

/**
 * Temporary store of message header lines
 *
 * @var array
 * @access protected
 */
	protected $_header = array();

/**
 * If set, boundary to use for multipart mime messages
 *
 * @var string
 * @access protected
 */
	protected $_boundary = null;

/**
 * Temporary store of message lines
 *
 * @var array
 * @access protected
 */
	protected $_message = array();

/**
 * Variable that holds SMTP connection
 *
 * @var resource
 * @access protected
 */
	protected $_smtpConnection = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->Controller = $collection->getController();
		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * @param object $controller Instantiating controller
 */
	public function initialize($controller) {
		if (Configure::read('App.encoding') !== null) {
			$this->charset = Configure::read('App.encoding');
		}
	}

/**
 * Startup component
 *
 * @param object $controller Instantiating controller
 */
	public function startup($controller) {}

/**
 * Send an email using the specified content, template and layout
 *
 * @param mixed $content Either an array of text lines, or a string with contents
 *  If you are rendering a template this variable will be sent to the templates as `$content`
 * @param string $template Template to use when sending email
 * @param string $layout Layout to use to enclose email body
 * @return boolean Success
 */
	public function send($content = null, $template = null, $layout = null) {
		$lib = new CakeEmail();
		$lib->charset = $this->charset;

		$lib->from($this->_formatAddresses((array)$this->from));
		if (!empty($this->to)) {
			$lib->to($this->_formatAddresses((array)$this->to));
		}
		if (!empty($this->cc)) {
			$lib->cc($this->_formatAddresses((array)$this->cc));
		}
		if (!empty($this->bcc)) {
			$lib->bcc($this->_formatAddresses((array)$this->bcc));
		}
		if (!empty($this->replyTo)) {
			$lib->replyTo($this->_formatAddresses((array)$this->replyTo));
		}
		if (!empty($this->return)) {
			$lib->returnPath($this->_formatAddresses((array)$this->return));
		}
		if (!empty($readReceipt)) {
			$lib->readReceipt($this->_formatAddresses((array)$this->readReceipt));
		}

		$lib->subject($this->subject)->messageID($this->messageId);

		$headers = array();
		foreach ($this->headers as $key => $value) {
			$headers['X-' . $key] = $value;
		}
		if ($this->date != false) {
			$headers['Date'] = $this->date;
		}
		$lib->setHeaders($headers);

		if ($template) {
			$this->template = $template;
		}
		if ($layout) {
			$this->layout = $layout;
		}
		$lib->layout($this->layout, $this->template)->emailFormat($this->sendAs);

		if (!empty($this->attachments)) {
			$lib->attachment($this->_formatAttachFiles());
		}

		$transport = $lib->transport($this->delivery)->transportClass();
		if ($this->delivery === 'mail') {
			$transport->config(array('eol' => $this->lineFeed));
		} elseif ($this->delivery === 'smtp') {
			$transport->config($this->smtpOptions);
		}

		$sent = $lib->send($content);

		$this->_header = array();
		$this->_message = array();

		return $sent;
	}

/**
 * Reset all EmailComponent internal variables to be able to send out a new email.
 *
 * @link http://book.cakephp.org/view/1285/Sending-Multiple-Emails-in-a-loop
 */
	public function reset() {
		$this->template = null;
		$this->to = array();
		$this->from = null;
		$this->replyTo = null;
		$this->return = null;
		$this->cc = array();
		$this->bcc = array();
		$this->subject = null;
		$this->additionalParams = null;
		$this->date = null;
		$this->smtpError = null;
		$this->attachments = array();
		$this->htmlMessage = null;
		$this->textMessage = null;
		$this->messageId = true;
		$this->_header = array();
		$this->_boundary = null;
		$this->_message = array();
	}

/**
 * Render the contents using the current layout and template.
 *
 * @param string $content Content to render
 * @return array Email ready to be sent
 * @access private
 */
	function _render($content) {
		$viewClass = $this->Controller->view;

		if ($viewClass != 'View') {
			list($plugin, $viewClass) = pluginSplit($viewClass, true);
			$viewClass = $viewClass . 'View';
			App::uses($viewClass, $plugin . 'View');
		}

		$View = new $viewClass($this->Controller);
		$View->layout = $this->layout;
		$msg = array();

		$content = implode("\n", $content);

		if ($this->sendAs === 'both') {
			$htmlContent = $content;
			if (!empty($this->attachments)) {
				$msg[] = '--' . $this->_boundary;
				$msg[] = 'Content-Type: multipart/alternative; boundary="alt-' . $this->_boundary . '"';
				$msg[] = '';
			}
			$msg[] = '--alt-' . $this->_boundary;
			$msg[] = 'Content-Type: text/plain; charset=' . $this->charset;
			$msg[] = 'Content-Transfer-Encoding: 7bit';
			$msg[] = '';

			$content = $View->element('email' . DS . 'text' . DS . $this->template, array('content' => $content), true);
			$View->layoutPath = 'email' . DS . 'text';
			$content = explode("\n", $this->textMessage = str_replace(array("\r\n", "\r"), "\n", $View->renderLayout($content)));

			$msg = array_merge($msg, $content);

			$msg[] = '';
			$msg[] = '--alt-' . $this->_boundary;
			$msg[] = 'Content-Type: text/html; charset=' . $this->charset;
			$msg[] = 'Content-Transfer-Encoding: 7bit';
			$msg[] = '';

			$htmlContent = $View->element('email' . DS . 'html' . DS . $this->template, array('content' => $htmlContent), true);
			$View->layoutPath = 'email' . DS . 'html';
			$htmlContent = explode("\n", $this->htmlMessage = str_replace(array("\r\n", "\r"), "\n", $View->renderLayout($htmlContent)));
			$msg = array_merge($msg, $htmlContent);
			$msg[] = '';
			$msg[] = '--alt-' . $this->_boundary . '--';
			$msg[] = '';

			ClassRegistry::removeObject('view');
			return $msg;
		}

		if (!empty($this->attachments)) {
			if ($this->sendAs === 'html') {
				$msg[] = '';
				$msg[] = '--' . $this->_boundary;
				$msg[] = 'Content-Type: text/html; charset=' . $this->charset;
				$msg[] = 'Content-Transfer-Encoding: 7bit';
				$msg[] = '';
			} else {
				$msg[] = '--' . $this->_boundary;
				$msg[] = 'Content-Type: text/plain; charset=' . $this->charset;
				$msg[] = 'Content-Transfer-Encoding: 7bit';
				$msg[] = '';
			}
		}

		$content = $View->element('email' . DS . $this->sendAs . DS . $this->template, array('content' => $content), true);
		$View->layoutPath = 'email' . DS . $this->sendAs;
		$content = explode("\n", $rendered = str_replace(array("\r\n", "\r"), "\n", $View->renderLayout($content)));

		if ($this->sendAs === 'html') {
			$this->htmlMessage = $rendered;
		} else {
			$this->textMessage = $rendered;
		}

		$msg = array_merge($msg, $content);
		ClassRegistry::removeObject('view');

		return $msg;
	}

/**
 * Create unique boundary identifier
 *
 * @access private
 */
	function _createboundary() {
		$this->_boundary = md5(uniqid(time()));
	}

/**
 * Sets headers for the message
 *
 * @access public
 * @param array Associative array containing headers to be set.
 */
	function header($headers) {
		foreach ($headers as $header => $value) {
			$this->_header[] = sprintf('%s: %s', trim($header), trim($value));
		}
	}
/**
 * Create emails headers including (but not limited to) from email address, reply to,
 * bcc and cc.
 *
 * @access private
 */
	function _createHeader() {
        $headers = array();

		if ($this->delivery == 'smtp') {
			$headers['To'] = implode(', ', array_map(array($this, '_formatAddress'), (array)$this->to));
		}
		$headers['From'] = $this->_formatAddress($this->from);

		if (!empty($this->replyTo)) {
			$headers['Reply-To'] = $this->_formatAddress($this->replyTo);
		}
		if (!empty($this->return)) {
			$headers['Return-Path'] = $this->_formatAddress($this->return);
		}
		if (!empty($this->readReceipt)) {
			$headers['Disposition-Notification-To'] = $this->_formatAddress($this->readReceipt);
		}

		if (!empty($this->cc)) {
			$headers['Cc'] = implode(', ', array_map(array($this, '_formatAddress'), (array)$this->cc));
		}

		if (!empty($this->bcc) && $this->delivery != 'smtp') {
			$headers['Bcc'] = implode(', ', array_map(array($this, '_formatAddress'), (array)$this->bcc));
		}
		if ($this->delivery == 'smtp') {
			$headers['Subject'] = $this->_encode($this->subject);
		}

		if ($this->messageId !== false) {
			if ($this->messageId === true) {
				$headers['Message-ID'] = '<' . String::UUID() . '@' . env('HTTP_HOST') . '>';
			} else {
				$headers['Message-ID'] = $this->messageId;
			}
		}

		$date = $this->date;
		if ($date == false) {
			$date = date(DATE_RFC2822);
		}
		$headers['Date'] = $date;

		$headers['X-Mailer'] = $this->xMailer;

		if (!empty($this->headers)) {
			foreach ($this->headers as $key => $val) {
				$headers['X-' . $key] = $val;
			}
		}

		if (!empty($this->attachments)) {
			$this->_createBoundary();
			$headers['MIME-Version'] = '1.0';
			$headers['Content-Type'] = 'multipart/mixed; boundary="' . $this->_boundary . '"';
			$headers[] = 'This part of the E-mail should never be seen. If';
			$headers[] = 'you are reading this, consider upgrading your e-mail';
			$headers[] = 'client to a MIME-compatible client.';
		} elseif ($this->sendAs === 'text') {
			$headers['Content-Type'] = 'text/plain; charset=' . $this->charset;
		} elseif ($this->sendAs === 'html') {
			$headers['Content-Type'] = 'text/html; charset=' . $this->charset;
		} elseif ($this->sendAs === 'both') {
			$headers['Content-Type'] = 'multipart/alternative; boundary="alt-' . $this->_boundary . '"';
		}

		$headers['Content-Transfer-Encoding'] = '7bit';

        $this->header($headers);
	}

/**
 * Format the message by seeing if it has attachments.
 *
 * @param string $message Message to format
 * @access private
 */
	function _formatMessage($message) {
		if (!empty($this->attachments)) {
			$prefix = array('--' . $this->_boundary);
			if ($this->sendAs === 'text') {
				$prefix[] = 'Content-Type: text/plain; charset=' . $this->charset;
			} elseif ($this->sendAs === 'html') {
				$prefix[] = 'Content-Type: text/html; charset=' . $this->charset;
			} elseif ($this->sendAs === 'both') {
				$prefix[] = 'Content-Type: multipart/alternative; boundary="alt-' . $this->_boundary . '"';
			}
			$prefix[] = 'Content-Transfer-Encoding: 7bit';
			$prefix[] = '';
			$message = array_merge($prefix, $message);
		}
		return $message;
	}

/**
 * Format the attach array
 *
 * @return array
 */
	protected function _formatAttachFiles() {
		$files = array();
		foreach ($this->attachments as $filename => $attachment) {
			$file = $this->_findFiles($attachment);
			if (!empty($file)) {
				if (is_int($filename)) {
					$filename = basename($file);
				}
				$files[$filename] = $file;
			}
		}
		return $files;
	}

/**
 * Find the specified attachment in the list of file paths
 *
 * @param string $attachment Attachment file name to find
 * @return string Path to located file
 * @access private
 */
	function _findFiles($attachment) {
		if (file_exists($attachment)) {
			return $attachment;
		}
		foreach ($this->filePaths as $path) {
			if (file_exists($path . DS . $attachment)) {
				$file = $path . DS . $attachment;
				return $file;
			}
		}
		return null;
	}

/**
 * Wrap the message using EmailComponent::$lineLength
 *
 * @param string $message Message to wrap
 * @param integer $lineLength Max length of line
 * @return array Wrapped message
 * @access protected
 */
	function _wrap($message, $lineLength = null) {
		$message = $this->_strip($message, true);
		$message = str_replace(array("\r\n","\r"), "\n", $message);
		$lines = explode("\n", $message);
		$formatted = array();

		if ($this->_lineLength !== null) {
			trigger_error(__d('cake_dev', '_lineLength cannot be accessed please use lineLength'), E_USER_WARNING);
			$this->lineLength = $this->_lineLength;
		}

		if (!$lineLength) {
			$lineLength = $this->lineLength;
		}

		foreach ($lines as $line) {
			if (substr($line, 0, 1) == '.') {
				$line = '.' . $line;
			}
			$formatted = array_merge($formatted, explode("\n", wordwrap($line, $lineLength, "\n", true)));
		}
		$formatted[] = '';
		return $formatted;
	}

/**
 * Encode the specified string using the current charset
 *
 * @param string $subject String to encode
 * @return string Encoded string
 * @access private
 */
	function _encode($subject) {
		$subject = $this->_strip($subject);

		$nl = "\r\n";
		if ($this->delivery == 'mail') {
			$nl = '';
		}
		$internalEncoding = function_exists('mb_internal_encoding');
		if ($internalEncoding) {
			$restore = mb_internal_encoding();
			mb_internal_encoding($this->charset);
		}
		$return = mb_encode_mimeheader($subject, $this->charset, 'B', $nl);
		if ($internalEncoding) {
			mb_internal_encoding($restore);
		}
		return $return;
	}

/**
 * Format a string as an email address
 *
 * @param string $string String representing an email address
 * @return string Email address suitable for email headers or smtp pipe
 * @access private
 */
	function _formatAddress($string, $smtp = false) {
		$hasAlias = preg_match('/((.*))?\s?<(.+)>/', $string, $matches);
		if ($smtp && $hasAlias) {
			return $this->_strip('<' .  $matches[3] . '>');
		} elseif ($smtp) {
			return $this->_strip('<' . $string . '>');
		}

		if ($hasAlias && !empty($matches[2])) {
			return $this->_encode($matches[2]) . $this->_strip(' <' . $matches[3] . '>');
		}
		return $this->_strip($string);
	}

/**
 * Format addresses to be an array with email as key and alias as value
 *
 * @param array $addresses
 * @return array
 */
	protected function _formatAddresses($addresses) {
		$formatted = array();
		foreach ($addresses as $address) {
			if (preg_match('/((.*))?\s?<(.+)>/', $address, $matches) && !empty($matches[2])) {
				$formatted[$this->_strip($matches[3])] = $this->_encode($matches[2]);
			} else {
				$address = $this->_strip($address);
				$formatted[$address] = $address;
			}
		}
		return $formatted;
	}

/**
 * Remove certain elements (such as bcc:, to:, %0a) from given value.
 * Helps prevent header injection / mainipulation on user content.
 *
 * @param string $value Value to strip
 * @param boolean $message Set to true to indicate main message content
 * @return string Stripped value
 * @access private
 */
	function _strip($value, $message = false) {
		$search  = '%0a|%0d|Content-(?:Type|Transfer-Encoding)\:';
		$search .= '|charset\=|mime-version\:|multipart/mixed|(?:[^a-z]to|b?cc)\:.*';

		if ($message !== true) {
			$search .= '|\r|\n';
		}
		$search = '#(?:' . $search . ')#i';
		while (preg_match($search, $value)) {
			$value = preg_replace($search, '', $value);
		}
		return $value;
	}

/**
 * Wrapper for PHP mail function used for sending out emails
 *
 * @return bool Success
 * @access private
 */
	function _mail() {
		$header = implode($this->lineFeed, $this->_header);
		$message = implode($this->lineFeed, $this->_message);
		if (is_array($this->to)) {
			$to = implode(', ', array_map(array($this, '_formatAddress'), $this->to));
		} else {
			$to = $this->to;
		}
		if (ini_get('safe_mode')) {
			return @mail($to, $this->_encode($this->subject), $message, $header);
		}
		return @mail($to, $this->_encode($this->subject), $message, $header, $this->additionalParams);
	}


/**
 * Helper method to get socket, overridden in tests
 *
 * @param array $config Config data for the socket.
 * @return void
 * @access protected
 */
	function _getSocket($config) {
		$this->_smtpConnection = new CakeSocket($config);
	}

/**
 * Sends out email via SMTP
 *
 * @return bool Success
 * @access private
 */
	function _smtp() {
		App::uses('CakeSocket', 'Network');

		$defaults = array(
			'host' => 'localhost',
			'port' => 25,
			'protocol' => 'smtp',
			'timeout' => 30
		);
		$this->smtpOptions = array_merge($defaults, $this->smtpOptions);
		$this->_getSocket($this->smtpOptions);

		if (!$this->_smtpConnection->connect()) {
			$this->smtpError = $this->_smtpConnection->lastError();
			return false;
		} elseif (!$this->_smtpSend(null, '220')) {
			return false;
		}

		$httpHost = env('HTTP_HOST');

		if (isset($this->smtpOptions['client'])) {
			$host = $this->smtpOptions['client'];
		} elseif (!empty($httpHost)) {
			list($host) = explode(':', $httpHost);
		} else {
			$host = 'localhost';
		}

		if (!$this->_smtpSend("EHLO {$host}", '250') && !$this->_smtpSend("HELO {$host}", '250')) {
			return false;
		}

		if (isset($this->smtpOptions['username']) && isset($this->smtpOptions['password'])) {
			$authRequired = $this->_smtpSend('AUTH LOGIN', '334|503');
			if ($authRequired == '334') {
				if (!$this->_smtpSend(base64_encode($this->smtpOptions['username']), '334')) {
					return false;
				}
				if (!$this->_smtpSend(base64_encode($this->smtpOptions['password']), '235')) {
					return false;
				}
			} elseif ($authRequired != '503') {
				return false;
			}
		}

		if (!$this->_smtpSend('MAIL FROM: ' . $this->_formatAddress($this->from, true))) {
			return false;
		}

		if (!is_array($this->to)) {
			$tos = array_map('trim', explode(',', $this->to));
		} else {
			$tos = $this->to;
		}
		foreach ($tos as $to) {
			if (!$this->_smtpSend('RCPT TO: ' . $this->_formatAddress($to, true))) {
				return false;
			}
		}

		foreach ($this->cc as $cc) {
			if (!$this->_smtpSend('RCPT TO: ' . $this->_formatAddress($cc, true))) {
				return false;
			}
		}
		foreach ($this->bcc as $bcc) {
			if (!$this->_smtpSend('RCPT TO: ' . $this->_formatAddress($bcc, true))) {
				return false;
			}
		}

		if (!$this->_smtpSend('DATA', '354')) {
			return false;
		}

		$header = implode("\r\n", $this->_header);
		$message = implode("\r\n", $this->_message);
		if (!$this->_smtpSend($header . "\r\n\r\n" . $message . "\r\n\r\n\r\n.")) {
			return false;
		}
		$this->_smtpSend('QUIT', false);

		$this->_smtpConnection->disconnect();
		return true;
	}

/**
 * Protected method for sending data to SMTP connection
 *
 * @param string $data data to be sent to SMTP server
 * @param mixed $checkCode code to check for in server response, false to skip
 * @return bool Success
 * @access protected
 */
	function _smtpSend($data, $checkCode = '250') {
		if (!is_null($data)) {
			$this->_smtpConnection->write($data . "\r\n");
		}
		while ($checkCode !== false) {
			$response = '';
			$startTime = time();
			while (substr($response, -2) !== "\r\n" && ((time() - $startTime) < $this->smtpOptions['timeout'])) {
				$response .= $this->_smtpConnection->read();
			}
			if (substr($response, -2) !== "\r\n") {
				$this->smtpError = 'timeout';
				return false;
			}
			$response = end(explode("\r\n", rtrim($response, "\r\n")));

			if (preg_match('/^(' . $checkCode . ')(.)/', $response, $code)) {
				if ($code[2] === '-') {
					continue;
				}
				return $code[1];
			}
			$this->smtpError = $response;
			return false;
		}
		return true;
	}

/**
 * Set as controller flash message a debug message showing current settings in component
 *
 * @return boolean Success
 * @access private
 */
	function _debug() {
		$nl = "\n";
		$header = implode($nl, $this->_header);
		$message = implode($nl, $this->_message);
		$fm = '<pre>';

		if (is_array($this->to)) {
			$to = implode(', ', array_map(array($this, '_formatAddress'), $this->to));
		} else {
			$to = $this->to;
		}
		$fm .= sprintf('%s %s%s', 'To:', $to, $nl);
		$fm .= sprintf('%s %s%s', 'From:', $this->from, $nl);
		$fm .= sprintf('%s %s%s', 'Subject:', $this->_encode($this->subject), $nl);
		$fm .= sprintf('%s%3$s%3$s%s', 'Header:', $header, $nl);
		$fm .= sprintf('%s%3$s%3$s%s', 'Parameters:', $this->additionalParams, $nl);
		$fm .= sprintf('%s%3$s%3$s%s', 'Message:', $message, $nl);
		$fm .= '</pre>';

		if (isset($this->Controller->Session)) {
			$this->Controller->Session->setFlash($fm, 'default', null, 'email');
			return true;
		}
		return $fm;
	}
}
