<?php
/**
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
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<h2><?php printf(__('Private Method in %s'), $controller); ?></h2>
<p class="error">
	<strong><?php echo __('Error'); ?>: </strong>
	<?php printf(__('%s%s cannot be accessed directly.'), '<em>' . $controller . '::</em>', '<em>' . $action . '()</em>'); ?>
</p>
<p class="notice">
	<strong><?php echo __('Notice'); ?>: </strong>
	<?php printf(__('If you want to customize this error message, create %s'), APP_DIR . DS . 'views' . DS . 'errors' . DS . 'private_action.ctp'); ?>
</p>

<?php echo $this->element('exception_stack_trace'); ?>