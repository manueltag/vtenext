<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
 
/* crmv@10445 crmv@105538 crmv@104562 */

class EntityColorUtils extends SDKExtendableUniqueClass {

	/**
	 * Simplified function which returns the color for the specified record
	 */
	public function getEntityColor($module,$crmid, &$fieldvalue = null, $blend=false) {
		static $colors = array();
		if (!isset($colors[$module][$crmid])) {
			$used_status_field = $this->getUsedStatusField($module);
			if ($used_status_field) {
				$fieldvalue = $this->getEntityStatus($module,$used_status_field,$crmid);
				$color = $this->getValueColor($module,$fieldvalue);
				$colors[$module][$crmid]['original'] = $color;
				$colors[$module][$crmid]['blend'] = $this->color_blend_by_opacity($color,60);
			}
		}
		if ($blend)
			return $colors[$module][$crmid]['blend'];
		else
			return $colors[$module][$crmid]['original'];
	}

	/**
	 * Return the status for the specified entity
	 */
	public function getEntityStatus($module,$used_status_field,$crmid) {
		global $adb, $table_prefix;
		
		$tabid = getTabid($module);
		if($used_status_field != "") {
			$query = "select tablename,columnname from ".$table_prefix."_field where tabid = $tabid and fieldname = '$used_status_field' ";
			$result = $adb->query($query);
			if($result && $adb->num_rows($result)>0) {
				$tablename = $adb->query_result_no_html($result,0,"tablename");
				$columnname = $adb->query_result_no_html($result,0,"columnname");
				if($tablename != "") {
					$obj = CRMEntity::getInstance($module);
					$key = $obj->tab_name_index[$tablename];
					if ($key) {
						$query = "SELECT $columnname FROM $tablename WHERE $key = ?";
						$result = $adb->pquery($query, array($crmid));
						if ($result && $adb->num_rows($result)>0) {
							return $adb->query_result_no_html($result,0,$columnname);
						}
					}
				}
			}
		}
		return null;
	}
	
	public function getModuleColors($module, $blend=false) {
		global $adb;
		$colors = array();
		$tabid = getTabid($module);
		$result = $adb->pquery('select fieldvalue, color from tbl_s_lvcolors where tabid = ?',array($tabid));
		if($result && $adb->num_rows($result)>0) {
			while($row=$adb->fetchByAssoc($result)){
				($blend) ? $html = $this->color_blend_by_opacity($row['color'],60) : $html = $row['color'];
				$hex = str_replace('#','',$html);
				if(strlen($hex) == 3) {
					$r = hexdec(substr($hex,0,1).substr($hex,0,1));
					$g = hexdec(substr($hex,1,1).substr($hex,1,1));
					$b = hexdec(substr($hex,2,1).substr($hex,2,1));
				} else {
					$r = hexdec(substr($hex,0,2));
					$g = hexdec(substr($hex,2,2));
					$b = hexdec(substr($hex,4,2));
				}				
				$brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 255000;
				if ($brightness >= 0.5) {
					$text_color = "#000000";
				} else {
					$text_color = "#ffffff";
				}				
				$colors[$row['fieldvalue']] = array(
					'html' => $html,
					'hex' => $hex,
					'rgb' => array($r,$g,$b),
					'brightness' => $brightness,
					'text_color' => $text_color,
				);
			}
		}
		return $colors;
	}

	/**
	 * Return the color for this value
	 */
	public function getValueColor($module,$fieldvalue) {
		global $adb;
		
		if($fieldvalue != '') {
			$tabid = getTabid($module);
			$result = $adb->pquery('select color from tbl_s_lvcolors where tabid = ? and fieldvalue = ?',array($tabid,$fieldvalue));	//crmv@52299
			if($result && $adb->num_rows($result)>0) {
				return $adb->query_result_no_html($result,0,'color');
			}
		}
		return null;
	}

	/**
	 * Get a list of modules for which the coloring is available
	 * Disabled modules and fields are ignored
	 */
	public function getSupportedModules() {
		global $adb,$table_prefix;

		//crmv@36562
		$query = 
			'SELECT DISTINCT '.$table_prefix.'_field.fieldname,'.$table_prefix.'_field.tabid, '.$table_prefix.'_tab.name as tabname, uitype
			FROM '.$table_prefix.'_field
			INNER JOIN '.$table_prefix.'_tab on '.$table_prefix.'_tab.tabid='.$table_prefix.'_field.tabid 
			WHERE uitype IN (15,16, 111,115,300,55,56,1115,1015) and '.$table_prefix.'_field.tabid not in (29,9,16) and '.$table_prefix.'_tab.presence <> 1 and '.$table_prefix.'_field.presence in (0,2)
			ORDER by '.$table_prefix.'_field.tabid ASC';
		//crmv@36562e	

		$result = $adb->pquery($query, array());
		while ($row = $adb->fetch_array($result)) {
			$modules[$row['tabname']] = getTranslatedString($row['tabname'], $row['tabname']); 
		}
		// sort by label
		asort($modules);
		
		return $modules;
	}

	/**
	 * Retrieve fields defining a "status" on entity of type $module, like picklist and checkboxes
	 */
	public function getStatusFields($module) {
		global $adb, $table_prefix;

		$tabid = getTabid($module);
		//crmv@29752
		$query = 
			"select
				".$table_prefix."_field.fieldid,
				".$table_prefix."_field.fieldname,
				".$table_prefix."_field.fieldlabel,
				uitype
			FROM ".$table_prefix."_field
			INNER JOIN ".$table_prefix."_tab on ".$table_prefix."_tab.tabid=".$table_prefix."_field.tabid
			WHERE
			uitype IN (15,16, 111,115,300,55,56,1115,1015)
			and ".$table_prefix."_field.tabid = ?
			AND ".$table_prefix."_field.fieldname NOT IN ('hdnTaxType')
			order by ".$table_prefix."_field.fieldid ASC";
		$result = $adb->pquery($query, array($tabid));
		//crmv@29752e
		
		if ($result && $adb->num_rows($result)>0) {
			$retval = Array();
			$numrows = $adb->num_rows($result);
			for ($i=0;$i<$numrows;$i++) {
				$fieldid = $adb->query_result_no_html($result,$i,"fieldid");
				$uitype = $adb->query_result_no_html($result,$i,"uitype");
				$fieldname = $adb->query_result_no_html($result,$i,"fieldname");
				if ($uitype == 55) {
					if ($fieldname != 'salutationtype')	continue;
				}
				$values = $this->getStatusFieldValues($module,$fieldname,$uitype,$tabid);	//crmv@54254
				$retval[$fieldid] = Array(
					'fieldname' => $fieldname,
					'fieldlabel' => $adb->query_result($result,$i,"fieldlabel"),
					'uitype' => $uitype,
					'values' => $values
				);
			}
			return $retval;
		}
		return null;
	}
	
	/**
	 * Retrieve "possible" values for fieldname on type uitype
	 */
	public function getStatusFieldValues($module,$fieldname,$uitype,$tabid) { //crmv@54254
		global $adb, $table_prefix;
		global $current_language;
		
		switch($uitype) {
			case 56:
				$query = "select fieldvalue,color from tbl_s_lvcolors where fieldname = ? and tabid = ?";
				$res = $adb->pquery($query,array($fieldname,getTabid($module)));
				if ($res){
					while ($row = $adb->fetch_array($res)){
						if ($row['fieldvalue'] == '1')
							$color[0] = $row['color'];
						elseif ($row['fieldvalue'] == '0')
							$color[1] = $row['color'];
					}
				}
				return Array(Array("id"=>"0",'value'=>'yes','value_display'=>'yes','color'=>$color[0]),Array('id'=>"1",'value'=>'no','value_display'=>'no','color'=>$color[1]));
				break;
			case in_array($uitype,array(15,16, 111,115,300,55,1115)):
				$tablename = $table_prefix."_".$fieldname;
					$query = "select
								$tablename.*,
								tbl_s_lvcolors.color
								from ".$tablename."
								left join tbl_s_lvcolors on tbl_s_lvcolors.fieldname = '{$fieldname}' and $tablename.$fieldname = tbl_s_lvcolors.fieldvalue
								where (fieldname = ".$adb->quote($fieldname)." and tabid = {$tabid}) OR (fieldname IS NULL AND tabid IS NULL)";	//crmv@52621  //crmv@54254
				$result = $adb->query($query);
				if($result && $adb->num_rows($result)>0) {
					$retval = Array();
					$numrows = $adb->num_rows($result);
					for($i=0;$i<$numrows;$i++) {
						$value = $adb->query_result($result,$i,$fieldname);
						$retval[] = Array(
							'id'=> $adb->query_result($result,$i,0),
							"value" => $value,
							"value_display" => $value,
							"color"=>$adb->query_result($result,$i,"color")
						);
					}
					return $retval;
				}
				break;
			case 1015:
				$tablename = 'tbl_s_picklist_language';

				//crmv@73178
				$query = "select
							$tablename.code,$tablename.value,
							tbl_s_lvcolors.color
							from ".$tablename."
							left join tbl_s_lvcolors on $tablename.code = tbl_s_lvcolors.fieldvalue and tbl_s_lvcolors.fieldname = ".$adb->quote($fieldname)." and tbl_s_lvcolors.tabid = ".$adb->quote(getTabId($module))."
							";
				//crmv@73178e
				$query.=" where $tablename.field = ".$adb->quote($fieldname);
				
				$query.=" and $tablename.language = ".$adb->quote($current_language);
				$result = $adb->query($query);
				if($result && $adb->num_rows($result)>0) {
					$retval = Array();
					$numrows = $adb->num_rows($result);
					for($i=0;$i<$numrows;$i++) {
						$retval[] = Array(
							'id'=> $adb->query_result($result,$i,0),
							"value_display" => $adb->query_result($result,$i,'value'),
							"value" => $adb->query_result($result,$i,'code'),
							"color"=>$adb->query_result($result,$i,"color")
						);
					}
					return $retval;
				}
				break;
			default:
				break;
		}
		return null;
	}

	/**
	 * Returns (if exists) the current used status field
	 */
	public function getUsedStatusField($module) {
		global $adb, $table_prefix;
		
		static $usedStatusCache = array();
		
		if (!array_key_exists($module, $usedStatusCache)) {
		
			$tabid = getTabid($module);
			$query = "select
					".$table_prefix."_field.fieldname
					from ".$table_prefix."_field
					inner join ".$table_prefix."_tab on ".$table_prefix."_tab.tabid=".$table_prefix."_field.tabid
					inner join tbl_s_lvcolors on ".$table_prefix."_tab.tabid =  tbl_s_lvcolors.tabid and ".$table_prefix."_field.fieldname = tbl_s_lvcolors.fieldname
					where
					uitype IN (15,16, 111,115,55,56,1115,1015)
					and ".$table_prefix."_field.tabid != 29
					and ".$table_prefix."_field.tabid = ?
					order by ".$table_prefix."_field.tabid ASC";
			$result = $adb->pquery($query, array($tabid));
			if($result && $adb->num_rows($result)>0) {
				$usedStatusCache[$module] = $adb->query_result_no_html($result,0,"fieldname");
			} else {
				$usedStatusCache[$module] = null;
			}
			
		}
		
		return $usedStatusCache[$module];
	}
	
	
	/**
	 * Find the resulting colour by blending 2 colours
	 * and setting an opacity level for the foreground colour.
	 *
	 * @author J de Silva
	 * @link http://www.gidnetwork.com/b-135.html
	 * @param string $foreground Hexadecimal colour value of the foreground colour.
	 * @param integer $opacity Opacity percentage (of foreground colour). A number between 0 and 100.
	 * @param string $background Optional. Hexadecimal colour value of the background colour. Default is: <code>FFFFFF</code> aka white.
	 * @return string Hexadecimal colour value. <code>false</code> on errors.
	 */
	public function color_blend_by_opacity($foreground, $opacity, $background=null) {
		$foreground = substr($foreground, 1);
		static $colors_rgb=array(); // stores colour values already passed through the hexdec() functions below.

		if( is_null($background) )
			$background = 'FFFFFF'; // default background.

		$pattern = '~^[a-f0-9]{6,6}$~i'; // accept only valid hexadecimal colour values.
		if( !@preg_match($pattern, $foreground)  or  !@preg_match($pattern, $background) )
		{
			// trigger_error( "Invalid hexadecimal colour value(s) found", E_USER_WARNING );
			return false;
		}

		$opacity = intval( $opacity ); // validate opacity data/number.
		if( $opacity>100  || $opacity<0 )
		{
			// trigger_error( "Opacity percentage error, valid numbers are between 0 - 100", E_USER_WARNING );
			return false;
		}

		if( $opacity==100 )    // $transparency == 0
			return strtoupper( $foreground );
		if( $opacity==0 )    // $transparency == 100
			return strtoupper( $background );
		// calculate $transparency value.
		$transparency = 100-$opacity;

		if( !isset($colors_rgb[$foreground]) )
		{ // do this only ONCE per script, for each unique colour.
			$f = array(  'r'=>hexdec($foreground[0].$foreground[1]),
						'g'=>hexdec($foreground[2].$foreground[3]),
						'b'=>hexdec($foreground[4].$foreground[5])    );
			$colors_rgb[$foreground] = $f;
		}
		else
		{ // if this function is used 100 times in a script, this block is run 99 times.  Efficient.
			$f = $colors_rgb[$foreground];
		}

		if( !isset($colors_rgb[$background]) )
		{ // do this only ONCE per script, for each unique colour.
			$b = array(  'r'=>hexdec($background[0].$background[1]),
						'g'=>hexdec($background[2].$background[3]),
						'b'=>hexdec($background[4].$background[5])    );
			$colors_rgb[$background] = $b;
		}
		else
		{ // if this FUNCTION is used 100 times in a SCRIPT, this block will run 99 times.  Efficient.
			$b = $colors_rgb[$background];
		}

		$add = array(    'r'=>( $b['r']-$f['r'] ) / 100,
						'g'=>( $b['g']-$f['g'] ) / 100,
						'b'=>( $b['b']-$f['b'] ) / 100    );

		$f['r'] += intval( $add['r'] * $transparency );
		$f['g'] += intval( $add['g'] * $transparency );
		$f['b'] += intval( $add['b'] * $transparency );

		return "#".sprintf( '%02X%02X%02X', $f['r'], $f['g'], $f['b'] );
	}
	
}