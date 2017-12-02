<?php
namespace micro\cache\system;

/**
 * This class is responsible for storing Arrays in PHP files.
 */
class ApcuCache extends AbstractDataCache{
	/**
	 * Initializes the apcu cache-provider
	 */
	public function __construct($root,$postfix="") {
		parent::__construct($root,$postfix);
	}

	/**
	 * Check if annotation-data for the key has been stored.
	 * @param string $key cache key
	 * @return boolean true if data with the given key has been stored; otherwise false
	 */
	public function exists($key) {
		return \apcu_exists($this->getRealKey($key));
	}

	public function store($key, $code, $php=true) {
		$this->storeContent($key, $code);
	}

	/**
	 * Caches the given data with the given key.
	 * @param string $key cache key
	 * @param string $content the source-code to be cached
	 */
	protected function storeContent($key,$content) {
		\apcu_store($this->getRealKey($key), $content);
	}

	protected function getRealKey($key){
		return \md5($key);
	}

	/**
	 * Fetches data stored for the given key.
	 * @param string $key cache key
	 * @return mixed the cached data
	 */
	public function fetch($key) {
		$result=\apcu_fetch($this->getRealKey($key));
		return eval($result);
	}

	/**
	 * return data stored for the given key.
	 * @param string $key cache key
	 * @return mixed the cached data
	 */
	public function file_get_contents($key) {
		return \apcu_fetch($this->getRealKey($key));
	}

	/**
	 * Returns the timestamp of the last cache update for the given key.
	 *
	 * @param string $key cache key
	 * @return int unix timestamp
	 */
	public function getTimestamp($key) {
			$key=$this->getRealKey($key);
			$cache = \apc_cache_info();
			if (empty($cache['cache_list'])) {
				return false;
			}
			foreach ($cache['cache_list'] as $entry) {
				if ($entry['info'] != $key) {
					continue;
				}
				$creationTime = $entry['creation_time'];
				return $creationTime;
			}
			return \time();
	}

	public function remove($key) {
		\apcu_delete($this->getRealKey($key));
	}

	public function clear() {
		\apcu_clear_cache();
	}
}