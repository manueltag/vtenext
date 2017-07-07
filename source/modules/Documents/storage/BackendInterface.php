<?php

/* crmv@95157 */

interface VTEStorageBackend {

	public function getLabel();
	
	public function saveFile($parentfocus, $options = array(), &$attid = null);
	
	public function saveFileRevision($oldkey, $parentfocus, $options = array(), &$attid = null);
	
	public function retrieveFile($attid, $key, $options = array());
	
	public function deleteFile($attid, $key, $options = array());
	
	public function checkIntegrity($attid, $key, $options = array());
	
	public function incrementDownloadCount($key);
	
	public function readMetadata($attid, $key, $options = array());
	
	public function updateMetadata($attid, $key, $data, $options = array());

}