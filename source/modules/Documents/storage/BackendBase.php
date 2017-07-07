<?php

/* crmv@95157 */

require_once('BackendInterface.php');

abstract class VTEBackendBase extends SDKExtendableUniqueClass implements VTEStorageBackend {

	public $name;
	public $hasMetadata = true;
	
	public function getLabel() {
		$key = 'LBL_STORAGE_BACKEND_'.strtoupper($this->name);
		return getTranslatedString($key, 'Documents');
	}
	
	public function incrementDownloadCount($key) {
		// do nothing by default
		return true;
	}
	
}