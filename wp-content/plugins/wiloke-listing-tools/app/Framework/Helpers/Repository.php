<?php
namespace WilokeListingTools\Framework\Helpers;


class Repository{
	/*
	 * All of configurations of items
	 *
	 * @var array
	 */
	protected  $aItems = array();

	protected  $fileName;

	protected  $fileNameIncExt;

	protected  $dir;

	protected  $arrKey;

	protected  $value;

	protected  $oInstance = null;

	public function __construct(array $aItems = []) {
		$this->setConfigDir();
		$this->aItems = $aItems;
	}

	/*
	 * Set the directory to config folder
	 * @return directory
	 */
	public function setConfigDir($dir=''){
		$this->dir = !empty($dir) ? $dir : plugin_dir_path(dirname(dirname(dirname(__FILE__))))   . 'config/';
		return $this;
	}

	/*
	 * Get all configurations of the specify file
	 *
	 * @param string $fileName
	 * @return array
	 */
	protected function fileGetConfigurations(){
		if ( isset($this->aItems[$this->fileName]) ){
			return $this->aItems[$this->fileName];
		}

		if ( !file_exists($this->dir.$this->fileNameIncExt) ){
			$this->aItems[$this->fileName] = array();
			return $this->aItems[$this->fileName];
		}
		$this->aItems[$this->fileName] = include $this->dir.$this->fileNameIncExt;
		return $this->aItems[$this->fileName];
	}

	/**
	 * Parse key to get the file name and the array key
	 *
	 * @return void
	 */
	public function parseKey($key){
		$aParseKey = explode(':', $key);
		$this->fileName = $aParseKey[0];
		$this->fileNameIncExt = $this->fileName . '.php';

		$this->arrKey   = isset($aParseKey[1]) ? $aParseKey[1] : '';
	}

	/*
	 * Determine if the give configuration value exists
	 *
	 * @param string $key. This file name is the following structure: configFileName:arrKey
	 * @return bool
	 */
	public function has($key){
		self::parseKey($key);
		self::fileGetConfigurations();

		return isset($this->aItems[$key]);
	}

	/**
	 * Get the specified configuration value
	 *
	 * @param string $key
	 * @param bool $isChainingAble
	 * @return mixed
	 */
	public function get($key, $isChainingAble=false){
		self::parseKey($key);
		self::fileGetConfigurations();

		if ( empty($this->arrKey) ){
			$this->value = isset($this->aItems[$this->fileName]) ? $this->aItems[$this->fileName] : '';
		}else{
			$this->value =  isset($this->aItems[$this->fileName][$this->arrKey]) ? $this->aItems[$this->fileName][$this->arrKey] : '';
		}

		return $isChainingAble ? $this : $this->value;
	}

	/**
	 * Get Sub value
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function sub($key, $isChainingAble=false){
		$this->value = isset($this->value[$key]) ? $this->value[$key] : '';
		return $isChainingAble ? $this : $this->value;
	}

	/**
	 * Get all configs of the specified file
	 *
	 * @param string $key
	 * @return array
	 */
	public function getAllFileConfigs($key){
		self::parseKey($key);
		self::fileGetConfigurations();

		if ( isset($this->aItems[$this->fileName]) ){
			return $this->aItems[$this->fileName];
		}

		return array();
	}
}