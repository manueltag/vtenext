<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Import/resources/Utils.php';

class Import_File_Reader {

	var $status='success';
	var $numberOfRecordsRead = 0;
	var $errorMessage='';
	var $user;
	var $userInputObject;

	public $detectedEncoding = ''; // crmv@92218

	public function  __construct($userInputObject, $user) {
		$this->userInputObject = $userInputObject;
		$this->user = $user;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

	public function getNumberOfRecordsRead() {
		return $this->numberOfRecordsRead;
	}

	public function hasHeader() {
		if($this->userInputObject->get('has_header') == 'on'
				|| $this->userInputObject->get('has_header') == 1
				|| $this->userInputObject->get('has_header') == true) {
			return true;
		}
		return false;
	}

	public function getFirstRowData($hasHeader=true) {
		return null;
	}

	public function getFilePath() {
		return Import_Utils::getImportFilePath($this->user);
	}

	public function getFileHandler() {
		$filePath = $this->getFilePath();
		if(!file_exists($filePath)) {
			$this->status = 'failed';
			$this->errorMessage = "ERR_FILE_DOESNT_EXIST";
			return false;
		}

		$fileHandler = fopen($filePath, 'r');
		if(!$fileHandler) {
			$this->status = 'failed';
			$this->errorMessage = "ERR_CANT_OPEN_FILE";
			return false;
		}
		return $fileHandler;
	}

	// crmv@56463 crmv@92218
	public function convertCharacterEncoding($value, $fromCharset, $toCharset) {
		$detectedEncoding = '';
		
		if (!empty($this->detectedEncoding) && $fromCharset == 'AUTO') {
			$fromCharset = $this->detectedEncoding;
		}

		if ($fromCharset == 'AUTO') {
			$decValue = correctEncoding($value, $toCharset, '', $detectedEncoding);
			// ascii is a subset of all the other encodings, so this is safe
			if (!in_array($detectedEncoding, array('UTF-8', 'ASCII'))) {
				// it's a 8bit enconding, so I don't know which one of them, try to guess
				$guessedEnc = $this->guessEncoding($value);
				if (!empty($guessedEnc) && $guessedEnc != $detectedEncoding) {
					// decode again with the specified encoding
					$decValue = $this->convertCharacterEncoding($value, $guessedEnc, $toCharset);
					$detectedEncoding = $guessedEnc;
				}
			} else {
				$detectedEncoding = '';
			}
			$value = $decValue;
		} elseif (function_exists("iconv")) {
			$value = iconv($fromCharset, $toCharset, $value);
			$detectedEncoding = $fromCharset;
		} elseif (function_exists("mb_convert_encoding")) {
			$value = mb_convert_encoding($value, $toCharset, $fromCharset);
			$detectedEncoding = $fromCharset;
		}
		$this->detectedEncoding = $detectedEncoding;
		return $value;
	}
	// crmv@56463e
	
	/**
	 * Try to guess the file encoding using the language
	 */
	public function guessEncoding($value) {
		global $current_language, $default_language;
		
		$lang = $current_language ?: $default_language;
		
		$mapping = array(
			'Windows-1250' => array('pl_pl', 'cs_cs', 'sk_sk', 'hu_hu', 'sl_sl', 'bs_bs', 'hr_hr', 'sr_sr'),
			'ISO-8859-1' => array('it_it', 'es_es', 'pt_pt', 'pt_br', 'de_de', 'nl_nl'),
			// add here more encodings. Thy must exist in the Utils.php file
		);
		
		// search for a matching code
		foreach ($mapping as $encoding => $langs) {
			if (in_array($lang, $langs)) {
				return $encoding;
			}
		}
		
		// otherwise return the default
		return 'ISO-8859-1';
	}
	
	public function getDetectedEncoding() {
		return $this->detectedEncoding;
	}
	// crmv@92218e

	public function read() {
		// Sub-class need to implement this
	}

	public function deleteFile() {
		$filePath = $this->getFilePath();
		@unlink($filePath);
	}

	public function createTable() {
		$adb = PearDatabase::getInstance();

		$tableName = Import_Utils::getDbTableName($this->user);
		$fieldMapping = $this->userInputObject->get('field_mapping');

		//crmv@57238
		if ($adb->isMySQL()) {
			$primaryKey  = "PRIMARY KEY AUTO_INCREMENT";
		}
		elseif($adb->isMssql()) {
			$primaryKey = "IDENTITY PRIMARY KEY" ;
		}
		elseif($adb->isOracle()) {
			$primaryKey = "IDENTITY PRIMARY KEY" ;
		}
		$columnsListQuery = 'id INT ' . $primaryKey .', status INT DEFAULT 0, recordid INT NULL';
		//crmv@57238e

		foreach($fieldMapping as $fieldName => $index) {
			//crmv@57238
			if ($adb->isMssql()) {
				$columnsListQuery .= ','.$fieldName.' TEXT NULL';
			}
			else{
				$columnsListQuery .= ','.$fieldName.' TEXT';
			}
			//crmv@57238e
		}
		$createTableQuery = 'CREATE TABLE '. $tableName . ' ('.$columnsListQuery.')';
		$adb->query($createTableQuery);
		return true;
	}

	public function addRecordToDB($columnNames, $fieldValues) {
		$adb = PearDatabase::getInstance();

		$tableName = Import_Utils::getDbTableName($this->user);
		$adb->pquery('INSERT INTO '.$tableName.' ('. implode(',', $columnNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);
		$this->numberOfRecordsRead++;
	}
}
?>
