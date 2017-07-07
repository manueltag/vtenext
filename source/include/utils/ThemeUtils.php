<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

// crmv@119414

require_once('include/BaseClasses.php');
require_once('include/utils/Cache/CacheStorage.php');

class ThemeUtils extends SDKExtendableUniqueClass {
	
	protected $rcache;
	
	protected $themeDir = 'themes';
	protected $themeFile = 'theme.php';
	
	protected $default_values = array(
		'primary_menu_position' => 'top', // top, left
		'secondary_menu_position' => 'top', // top, right
		'tpl_overrides' => array(), // overrides the default templates (old tpl => new tpl)
	);
	
	public function __construct($theme) {
		$this->rcache = new CacheStorageVar();

		$this->initDefaultProperties();

		$this->overrideDefaultProperties($theme);
	}
	
	protected function initDefaultProperties() {
		foreach ($this->default_values as $prop => $value) {
			$oldVal = $this->getProperty($prop);
			if ($oldVal === null) {
				$this->setProperty($prop, $value);
			}
		}
	}
	
	protected function overrideDefaultProperties($theme) {
		$filename = $this->getThemeConfigFile($theme);

		if (!empty($filename)) {
			if (!class_exists('ThemeConfig')) require($filename);
			if (!class_exists('ThemeConfig')) return false;
			
			$themeConfig = new ThemeConfig();
			if (!$themeConfig instanceof OptionableClass) return false;
			
			foreach ($this->default_values as $prop => $value) {
				$newVal = $themeConfig->getOption($prop);
				if (!empty($newVal)) {
					$this->setProperty($prop, $newVal);
				}
			}
		}
		
		return false;
	}
	
	protected function getThemeConfigFile($theme) {
		if (empty($theme)) return false;
		if (!is_dir($this->themeDir)) return false;
		if (!is_dir($this->themeDir.'/'.$theme)) return false;

		$filePath = $this->themeDir.'/'.$theme.'/'.$this->themeFile;

		if (!is_readable($filePath)) return false;
		
		return $filePath;
	}
	
	/**
	 * Alias for getProperty
	 */
	public function get($property) {
		return $this->getProperty($property);
	}
	
	/**
	 * Get all properties
	 */
	public function getAll() {
		$values = $this->rcache->getAll();
		return $values;
	}
	
	/**
	 * Return a stored value
	 */
	protected function getProperty($property) {
		$value = $this->rcache->get($property);
		if ($value !== null) return $value;
		
		return null;
	}
	
	/**
	 * Alias for setProperty
	 */
	public function set($property, $value) {
		return $this->setProperty($property, $value);
	}
	
	/**
	 * Set property value
	 */
	protected function setProperty($property, $value) {
		$this->rcache->set($property, $value);
	}
	
}
