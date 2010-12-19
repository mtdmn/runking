<?php
/**
 * Paginator Component
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
 * @subpackage    cake.cake.libs.controller.components
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * This component is used to handle automatic model data pagination.  The primary way to use this
 * component is to call the paginate() method. There is a convience wrapper on Controller as well.
 *
 * ### Configuring pagination
 *
 * You configure pagination using the PaginatorComponent::$settings.  This allows you to configure
 * the default pagination behavior in general or for a specific model. General settings are used when there
 * are no specific model configuration, or the model you are paginating does not have specific settings.
 *
 * {{{
 *	$this->Paginator->settings = array(
 *		'limit' => 20,
 *		'maxLimit' => 100
 *	);
 * }}}
 *
 * The above settings will be used to paginate any model.  You can configure model specific settings by
 * keying the settings with the model name.
 *
 * {{{
 *	$this->Paginator->settings = array(
 *		'Post' => array(
 *			'limit' => 20,
 *			'maxLimit' => 100
 *		),
 *		'Comment' => array( ... )
 *	);
 * }}}
 *
 * This would allow you to have different pagination settings for `Comment` and `Post` models.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller.components
 */
class PaginatorComponent extends Component {

/**
 * Pagination settings.  These settings control pagination at a general level.
 * You can also define sub arrays for pagination settings for specific models.
 *
 * - `maxLimit` The maximum limit users can choose to view. Defaults to 100
 * - `limit` The initial number of items per page.  Defaults to 20.
 * - `page` The starting page, defaults to 1.
 * - `paramType` What type of parameters you want pagination to use?
 *      - `named` Use named parameters.
 *      - `querystring` Use query string parameters.
 *      - `route` Use routed parameters, these require you to setup routes that include the pagination params
 *
 * @var array
 */
	public $settings = array(
		'page' => 1,
		'limit' => 20,
		'maxLimit' => 100,
		'paramType' => 'named'
	);

/**
 * A list of request parameters users are allowed to set.  Modifying
 * this list will allow users to have more influence over pagination,
 * be careful with what you permit.
 *
 * @var array
 */
	public $whitelist = array(
		'limit', 'sort', 'page', 'direction'
	);

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$settings = array_merge($this->settings, (array)$settings);
		$this->Controller = $collection->getController();
		parent::__construct($collection, $settings);
	}

/**
 * Handles automatic pagination of model records.
 *
 * @param mixed $object Model to paginate (e.g: model instance, or 'Model', or 'Model.InnerModel')
 * @param mixed $scope Additional find conditions to use while paginating
 * @param array $whitelist List of allowed options for paging
 * @return array Model query results
 */
	public function paginate($object = null, $scope = array(), $whitelist = array()) {
		if (is_array($object)) {
			$whitelist = $scope;
			$scope = $object;
			$object = null;
		}

		$object = $this->_getObject($object);

		if (!is_object($object)) {
			throw new MissingModelException($object);
		}

		$options = $this->mergeOptions($object->alias, $whitelist);
		$options = $this->validateSort($object, $options);
		$options = $this->checkLimit($options);

		$conditions = $fields = $order = $limit = $page = $recursive = null;

		if (!isset($options['conditions'])) {
			$options['conditions'] = array();
		}

		$type = 'all';

		if (isset($options[0])) {
			$type = $options[0];
			unset($options[0]);
		}

		extract($options);

		if (is_array($scope) && !empty($scope)) {
			$conditions = array_merge($conditions, $scope);
		} elseif (is_string($scope)) {
			$conditions = array($conditions, $scope);
		}
		if ($recursive === null) {
			$recursive = $object->recursive;
		}

		$extra = array_diff_key($options, compact(
			'conditions', 'fields', 'order', 'limit', 'page', 'recursive'
		));
		if ($type !== 'all') {
			$extra['type'] = $type;
		}

		if (method_exists($object, 'paginateCount')) {
			$count = $object->paginateCount($conditions, $recursive, $extra);
		} else {
			$parameters = compact('conditions');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$count = $object->find('count', array_merge($parameters, $extra));
		}
		$pageCount = intval(ceil($count / $limit));

		if ($page === 'last' || $page >= $pageCount) {
			$options['page'] = $page = $pageCount;
		} elseif (intval($page) < 1) {
			$options['page'] = $page = 1;
		}
		$page = $options['page'] = (int)$page;

		if (method_exists($object, 'paginate')) {
			$results = $object->paginate(
				$conditions, $fields, $order, $limit, $page, $recursive, $extra
			);
		} else {
			$parameters = compact('conditions', 'fields', 'order', 'limit', 'page');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$results = $object->find($type, array_merge($parameters, $extra));
		}
		$defaults = $this->getDefaults($object->alias);
		unset($defaults[0]);

		$paging = array(
			'page' => $page,
			'current' => count($results),
			'count' => $count,
			'prevPage' => ($page > 1),
			'nextPage' => ($count > ($page * $limit)),
			'pageCount' => $pageCount,
			'order' => $order,
			'options' => Set::diff($options, $defaults),
			'paramType' => $options['paramType']
		);
		if (!isset($this->Controller->request['paging'])) {
			$this->Controller->request['paging'] = array();
		}
		$this->Controller->request['paging'] = array_merge(
			(array)$this->Controller->request['paging'],
			array($object->alias => $paging)
		);

		if (
			!in_array('Paginator', $this->Controller->helpers) &&
			!array_key_exists('Paginator', $this->Controller->helpers)
		) {
			$this->Controller->helpers[] = 'Paginator';
		}
		return $results;
	}

/**
 * Get the object pagination will occur on.
 *
 * @param mixed $object The object you are looking for.
 * @return mixed The model object to paginate on.
 */
	protected function _getObject($object) {
		if (is_string($object)) {
			$assoc = null;
			if (strpos($object, '.')  !== false) {
				list($object, $assoc) = pluginSplit($object);
			}

			if ($assoc && isset($this->Controller->{$object}->{$assoc})) {
				$object = $this->Controller->{$object}->{$assoc};
			} elseif (
				$assoc && isset($this->Controller->{$this->Controller->modelClass}) &&
				isset($this->Controller->{$this->Controller->modelClass}->{$assoc}
			)) {
				$object = $this->Controller->{$this->Controller->modelClass}->{$assoc};
			} elseif (isset($this->Controller->{$object})) {
				$object = $this->Controller->{$object};
			} elseif (
				isset($this->Controller->{$this->Controller->modelClass}) && isset($this->Controller->{$this->Controller->modelClass}->{$object}
			)) {
				$object = $this->Controller->{$this->Controller->modelClass}->{$object};
			}
		} elseif (empty($object) || $object === null) {
			if (isset($this->Controller->{$this->Controller->modelClass})) {
				$object = $this->Controller->{$this->Controller->modelClass};
			} else {
				$className = null;
				$name = $this->Controller->uses[0];
				if (strpos($this->Controller->uses[0], '.') !== false) {
					list($name, $className) = explode('.', $this->Controller->uses[0]);
				}
				if ($className) {
					$object = $this->Controller->{$className};
				} else {
					$object = $this->Controller->{$name};
				}
			}
		}
		return $object;
	}

/**
 * Merges the various options that Pagination uses.
 * Pulls settings together from the following places:
 *
 * - General pagination settings
 * - Model specific settings.
 * - Request parameters
 * - $options argument.
 *
 * The result of this method is the aggregate of all the option sets combined together.
 *
 * @param string $alias Model alias being paginated, if the general settings has a key with this value
 *   that key's settings will be used for pagination instead of the general ones.
 * @param string $whitelist A whitelist of options that are allowed from the request parameters.  Modifying
 *   this array will allow you to permit more or less input from the user.
 * @return array Array of merged options.
 */
	public function mergeOptions($alias, $whitelist = array()) {
		$defaults = $this->getDefaults($alias);
		switch ($defaults['paramType']) {
			case 'named':
				$request = $this->Controller->request->params['named'];
				break;
			case 'querystring':
				$request = $this->Controller->request->query;
				break;
			case 'route':
				$request = $this->Controller->request->params;
				unset($request['pass'], $request['named']);
		}

		$whitelist = array_flip(array_merge($this->whitelist, $whitelist));
		$request = array_intersect_key($request, $whitelist);

		return array_merge($defaults, $request);
	}

/**
 * Get the default settings for a $model.  If there are no settings for a specific model, the general settings
 * will be used.
 *
 * @param string $alias Model name to get default settings for.
 * @return array
 */
	public function getDefaults($alias) {
		if (isset($this->settings[$alias])) {
			$defaults = $this->settings[$alias];
		} else {
			$defaults = $this->settings;
		}
		return array_merge(
			array('page' => 1, 'limit' => 20, 'maxLimit' => 100, 'paramType' => 'named'),
			$defaults
		);
	}

/**
 * Validate that the desired sorting can be performed on the $object.  Only fields or 
 * virtualFields can be sorted on.  The direction param will also be sanitized.  Lastly
 * sort + direction keys will be converted into the model friendly order key.
 *
 * @param Model $object The model being paginated.
 * @param array $options The pagination options being used for this request.
 * @return array An array of options with sort + direction removed and replaced with order if possible.
 */
	public function validateSort($object, $options) {
		if (isset($options['sort'])) {
			$direction = null;
			if (isset($options['direction'])) {
				$direction = strtolower($options['direction']);
			}
			if ($direction != 'asc' && $direction != 'desc') {
				$direction = 'asc';
			}
			$options['order'] = array($options['sort'] => $direction);
		}
	
		if (!empty($options['order']) && is_array($options['order'])) {
			$alias = $object->alias ;
			$key = $field = key($options['order']);

			if (strpos($key, '.') !== false) {
				list($alias, $field) = explode('.', $key);
			}
			$value = $options['order'][$key];
			unset($options['order'][$key]);

			if ($object->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
			} elseif ($object->hasField($field, true)) {
				$options['order'][$field] = $value;
			} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
			}
		}
	
		return $options;
	}

/**
 * Check the limit parameter and ensure its within the maxLimit bounds.
 *
 * @param array $options An array of options with a limit key to be checked.
 * @return array An array of options for pagination
 */
	public function checkLimit($options) {
		$options['limit'] = (int) $options['limit'];
		if (empty($options['limit']) || $options['limit'] < 1) {
			$options['limit'] = 1;
		}
		$options['limit'] = min((int)$options['limit'], $options['maxLimit']);
		return $options;
	}
}