<?php
/* crmv@94125 */

require_once('include/BaseClasses.php');
require_once('include/utils/Cache.php');

/**
 * This class create versioned versions of resources (js, css, images...)
 * so when files are changed or after an upgrade, they can be regenerated
 * to ensure browsers will download the latest version
 */
class ResourceVersion extends SDKExtendableUniqueClass {

	public $table = '';
	public $enabled = false;
	public $autoRefresh = false;
	
	public function __construct() {
		global $table_prefix;
		
		$this->table = $table_prefix.'_resource_version';
		$this->enabled = PerformancePrefs::getBoolean('VERSION_RESOURCES', true) && Vtiger_Utils::CheckTable($this->table);
		$this->autoRefresh = PerformancePrefs::getBoolean('VERSION_RESOURCES_AUTOREFRESH', false);
	}
	
	/**
	 * function createResources : check and recreate symliks to resources
	 * params: file
	 * step1: get resources with update_revision = 1
	 * step2: increase revision
	 * step3: check original resource if is readable and new symlink is not created yet
	 * step4: create directory containing new symlink, create symlink, update database with new symlink, delete old symlink
	 */
	public function createResources($file=''){
		global $adb,$root_directory;
		
		$cache_method = PerformancePrefs::get('VERSION_RESOURCES_METHOD','link');
		$update_date = date('Y-m-d H:i:s');
		
		$columns = array('resource', 'revision', 'versioned_resource');
		$adb->format_columns($columns);
		$sql = "SELECT ".implode(', ', $columns)." FROM {$this->table} WHERE update_revision = 1";
		$params = Array();
		if (!empty($file)){
			$sql.= " AND {$columns[0]} = ?";
			$params[] = $file;
		}
		$res = $adb->pquery($sql,$params);
		if ($res){
			while($row=$adb->fetchByAssoc($res,-1,false)){
				$initial_revision = false;
				$update_cache = false;
				$revision = (int)$row['revision'];
				if ($revision == -1){
					$initial_revision = true;
				}
				$revision++; //new revision
				$sql_update = "UPDATE {$this->table} SET versioned_resource=?, update_revision=?, revision=?, filemtime=?, last_update=?, type=? WHERE {$columns[0]} = ?";
				if ($initial_revision) {
					$upd_params = Array($row['resource'],0,$revision,filemtime($row['resource']),$update_date,'original',$row['resource']);
					$res_update = $adb->pquery($sql_update,$upd_params);
					$update_cache = true;
					$newresource = $row['resource'];				
				} elseif ($cache_method == 'link' || $cache_method == 'copy') {
					$newresource = $this->generateName($row['resource'], $revision);
					$newresource_exists = $this->isVersionValid($row['resource'], $newresource, $cache_method);
					if ((empty($row['versioned_resource']) || !$newresource_exists) && file_exists($row['resource'])) {
						$ok = $this->generateVersion($row['resource'], $newresource, $cache_method);
						if ($ok) {							
							$upd_params = Array($newresource,0,$revision,filemtime($row['resource']),$update_date,$cache_method,$row['resource']);
							$res_update = $adb->pquery($sql_update,$upd_params);
							if (!empty($row['versioned_resource']) && $row['versioned_resource'] != $row['resource']) {
								// remove old version
								@unlink($row['versioned_resource']);
							}
							$update_cache = true;
						} else {
							// unable to copy, use the original file
							$upd_params = Array($row['resource'],0,$revision,filemtime($row['resource']),$update_date,'original',$row['resource']);
							$res_update = $adb->pquery($sql_update,$upd_params);
							$update_cache = true;
							$newresource = $row['resource'];
						}
					} else {
						// some problem, maybe the original file doesn't exist
						if (!file_exists($row['resource'])) {
							$this->removeResource($row['resource']);
						}
					}
				}
			}
		}
		
		if (!empty($file)) {
			if ($update_cache && !empty($newresource)){
				$resources_cache_obj = $this->cacheResources();
				$resources_cache = $resources_cache_obj->get();
				$resources_cache[$file] = array($newresource, filemtime($file));
				$resources_cache_obj->set($resources_cache);
				return $newresource;
			} 
		} else {
			//reset cache
			$resources_cache_obj = $this->cacheResources(true);
			$cache_arr = $resources_cache_obj->get();
			$resources_cache_obj->set($cache_arr);
		}
	}
	
	/**
	 * Generates the name for the versioned file (either link or real copy)
	 */
	protected function generateName($file, $revision) {
		$path_info = pathinfo($file);
		$newpath = $path_info['dirname'];
		$newresource = $newpath."/".$path_info['filename']."_v".$revision.".".$path_info['extension'];
		return $newresource;
	}
	
	protected function isResourceChanged($file, $versioned, $timestamp = null) {
		$changed = (empty($timestamp) || ($timestamp < filemtime($file)));
		// you can also add some kind of hash to check for changes, but might slow down things badly!
		return $changed;
	}
	
	/**
	 * Return true if the versioned file is valid
	 */
	protected function isVersionValid($file, $versioned, $type) {
		if ($type == 'link') {
			$path_info = pathinfo($file);
			$valid = (!empty($versioned) && is_link($versioned) && @readlink($versioned) && file_exists($path_info['dirname']."/".@readlink($versioned)));
		} elseif ($type == 'copy') {
			$valid = (!empty($versioned) && is_file($versioned));
		} else {
			throw new Exception("Unknown versioning type");
		}
		return $valid;
	}
	
	/**
	 * Physically create the link or copy the file
	 */
	protected function generateVersion($file, $versioned, $type) {
		$path_info = pathinfo($file);
		if ($type == 'link') {
			$ok = @symlink($path_info['basename'],$versioned);
		} elseif ($type == 'copy') {
			$ok = @copy($path_info['basename'],$versioned);
		} else {
			throw new Exception("Unknown versioning type");
		}
		return $ok;
	}
	
	/**
	 * function createResource : create or update cache resource
	 * params: $file: path of the original resource, $force_create: default false, force call createResources function
	 */	
	public function createResource($file,$force_create=false){
		global $adb;
		$column = "resource";
		$adb->format_columns($column);
		$sql_check = "SELECT $column FROM {$this->table} WHERE $column = ?";
		$res_check = $adb->pquery($sql_check,Array($file));
		if ($res_check) {
			if ($adb->num_rows($res_check)>0){
				$sql_update = "UPDATE {$this->table} SET update_revision = 1 WHERE $column = ?";
				$res_update = $adb->pquery($sql_update,Array($file));
			} else {
				$sql_ins = "INSERT INTO {$this->table} ($column,update_revision) VALUES (?,?)";
				$res_ins = $adb->pquery($sql_ins,Array($file,1));
			}
		}
		if ($force_create){
			$this->createResources($file);
		}	
	}
	
	/**
	 * function cacheResources : create or update cached resources object
	 * params: $recreate: boolean to force rebuild cache
	 */		
	public function cacheResources($recreate=false){
		global $adb;
		
		$cache = Cache::getInstance('cacheResources');
		$resources_cache = $cache->get();

		if ($resources_cache === false || $recreate){
			$resources_cache = Array();
			$columns = array('resource', 'versioned_resource', 'type', 'filemtime');
			$adb->format_columns($columns);
			$sql = "SELECT ".implode(', ', $columns)." FROM {$this->table}";
			$res = $adb->pquery($sql,Array());
			if ($res){
				while($row=$adb->fetchByAssoc($res,-1,false)){
					switch($row['type']){
						case 'original':
							$resources_cache[$row['resource']] = array($row['versioned_resource'], $row['filemtime']);
							break;
						case 'link':
						case 'copy':
							if ($this->isVersionValid($row['resource'], $row['versioned_resource'],  $row['type'])) {
								$resources_cache[$row['resource']] = array($row['versioned_resource'], $row['filemtime']);
							}
							break;
						default:
							break;	
					}
				}
			}
			$cache->set($resources_cache);
		}
		return $cache;
	}
	
	/**
	 * function getResource : get versioned resource
	 * params: $file: path of the original resource
	 * step1: create Array with versioned resources if it is not present
	 * step2: return versioned resource or $file if it is not present
	 */		
	public function getResource($file) {
		global $adb;
		
		if (!$this->enabled) return $file;	
		
		$resources_cache = $this->cacheResources()->get();
		if (!empty($resources_cache) && isset($resources_cache[$file])) {
			$newfile = $resources_cache[$file][0];
			if ($this->autoRefresh) {
				// check for changes
				$filets = $resources_cache[$file][1];
				if ($this->isResourceChanged($file, $newfile, $filets)) {
					$newfile = $this->createResource($file,true);
				}
			}
		} else {
			$newfile = $this->createResource($file,true);
		}
		
		if (!empty($newfile)){
			return $newfile;
		} else {
			return $file;
		}
	}
	
	/**
	 * function updateCacheResources : update versioned resources
	 * params: none
	 * step1: for every resource not in pending state control last modified date if it is newer than the versioned one put into creation queue (call createResource)
	 * step2: call createResources (create or update versioned resources that are in pending state)
	 */			
	public function updateResources() {
		global $adb;
		$sql = "SELECT resource,versioned_resource,filemtime FROM {$this->table} WHERE update_revision = 0";
		$res = $adb->query($sql);
		if ($res){
			while($row = $adb->fetchByAssoc($res,-1,false)){
				if (file_exists($row['resource'])){
					if ($this->isResourceChanged($row['resource'], $row['versioned_resource'], $row['filemtime'])) {
						$this->createResource($row['resource']);
					}
				} else {
					// file deleted, remove the versioning line
					$this->removeResource($row['resource']);
				}
			}
		}
		$this->createResources();
	}
	
	/**
	 *
	 */
	public function removeResource($filename) {
		global $adb;
		$sqldel = "DELETE FROM {$this->table} WHERE resource = ?";
		$adb->pquery($sqldel, array($filename));
	}
	
	/**
	 * function resetResources : reset versioned resources
	 * params: none
	 * for every resource delete entry and try to delete copy/link
	 */			
	public function resetResources(){
		global $adb;
		$sql = "SELECT resource,versioned_resource,type FROM {$this->table}";
		$res = $adb->query($sql);
		if ($res){
			while($row = $adb->fetchByAssoc($res,-1,false)){
				switch($row['type']){
					case 'original':
						//do nothing
						break;
					case 'link':
					case 'copy':
						if ($this->isVersionValid($row['resource'], $row['versioned_resource'],  $row['type'])) {
							@unlink($row['versioned_resource']);
						}
						break;
					default:
						break;
				}
			}
		}
		$sql = "DELETE FROM {$this->table}";
		$adb->query($sql);
	}
	
}

/**
 * Handy function to get the resource versioned name
 * Just a wrapper to the ResourceVersion::getResource method
 */
function resourcever($filename) {
	$RV = ResourceVersion::getInstance();
	$filename = $RV->getResource($filename);
	return $filename;
}