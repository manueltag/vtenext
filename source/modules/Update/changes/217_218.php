<?php
global $adb;
$adb->query("ALTER TABLE com_vtiger_wft_entitymeth 
			ADD UNIQUE KEY com_vtiger_wft_entitymeth_idx(workflowtasks_entitymethod_id), 
			DROP KEY com_vtiger_workflowtasks_entitymethod_idx, COMMENT=''");
 $adb->query("ALTER TABLE com_vtiger_workflows 
			CHANGE workflow_id workflow_id INT(11)   NOT NULL FIRST, 
			CHANGE summary summary VARCHAR(400)  COLLATE utf8_general_ci NULL AFTER module_name, 
			ADD COLUMN defaultworkflow INT(1)   NULL AFTER execution_condition, COMMENT=''");
 $adb->query("ALTER TABLE com_vtiger_workflowtask_queue 
			ADD UNIQUE KEY com_vtiger_wftask_queue_idx(task_id,entity_id), 
			DROP KEY com_vtiger_workflowtask_queue_idx, COMMENT='';");
 $adb->query("ALTER TABLE com_vtiger_workflowtasks 
			CHANGE task_id task_id INT(11)   NOT NULL FIRST, 
			CHANGE summary summary VARCHAR(400)  COLLATE utf8_general_ci NULL AFTER workflow_id, COMMENT=''");
 $adb->query("ALTER TABLE com_vtiger_workflowtemplates 
			CHANGE template_id template_id INT(11)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE crmv_budget 
			CHANGE budget budget DECIMAL(30,0)   NULL AFTER year, ENGINE=INNODB, COMMENT='', DEFAULT CHARSET='utf8'");
 $adb->query("ALTER TABLE crmv_potential_line_rel 
			CHANGE linename linename VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER lineid, 
			DROP KEY inventoryproductrel_id_idx, 
			ADD KEY inventoryproductrel_pid_idx(lineid), 
			DROP KEY inventoryproductrel_productid_idx, 
			ADD KEY potline_id_idx(id), COMMENT='', DEFAULT CHARSET='utf8'");
 $adb->query("ALTER TABLE erpbaseaccount ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_advancedrule 
			CHANGE advrule_id advrule_id INT(11)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_advancedrule_rel 
			CHANGE advrule_id advrule_id INT(11)   NULL FIRST, 
			CHANGE entity_type entity_type VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER advrule_id, 
			CHANGE id id VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER entity_type, 
			CHANGE permission permission TINYINT(4)   NULL AFTER id, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_advancedrulefilters 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER columnindex, 
			CHANGE comparator comparator VARCHAR(10)  COLLATE utf8_general_ci NULL AFTER columnname, 
			CHANGE value value VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER comparator, 
			ADD KEY advfilter_cvid_idx(advrule_id), 
			DROP KEY cvadvfilter_cvid_idx, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_advrule_relmod 
			DROP KEY fk_1_tbl_s_advancedrule_relmodules, 
			ADD KEY fk_1_tbl_s_advr_relmodules(rel_tabid), COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_advrule_relmodlist 
			ADD KEY advrule_relmod_tabid_idx(tabid), 
			ADD KEY datash_relmod_relto_tabid_idx(relatedto_tabid), 
			DROP KEY datashare_relatedmodules_relatedto_tabid_idx, 
			DROP KEY datashare_relatedmodules_tabid_idx, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_cvorderby 
			ADD KEY tbl_s_cvorderby_columnidx_idx(columnindex), 
			DROP KEY tbl_s_cvorderby_columnindex_idx, ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_faxservertype 
			CHANGE presence presence INT(1)   NULL DEFAULT '0' AFTER server_type, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_ldap_config ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_lvcolors ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_mailconnector 
			CHANGE id id INT(11)   NOT NULL FIRST, ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_s_picklist_language 
			CHANGE code code VARCHAR(20)  COLLATE utf8_general_ci NOT NULL AFTER code_system, 
			CHANGE field field VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER code, 
			CHANGE language language VARCHAR(5)  COLLATE utf8_general_ci NOT NULL AFTER field, 
			CHANGE value value TEXT  COLLATE utf8_general_ci NULL AFTER language, 
			DROP KEY NewIndex1, 
			DROP KEY NewIndex2, 
			DROP KEY NewIndex3, 
			ADD UNIQUE KEY picklist_code_field_lang_idx(code,field,language), 
			ADD KEY picklist_codes_lang_idx(code_system,code,field), 
			ADD KEY picklist_field_idx(field), 
			DROP KEY PRIMARY, ADD PRIMARY KEY(code_system,code,field,language), ENGINE=INNODB, COMMENT='', DEFAULT CHARSET='utf8'");
 $adb->query("DROP TABLE tbl_s_picklist_language2");
 $adb->query("ALTER TABLE tbl_s_smsservertype 
			CHANGE presence presence INT(1)   NULL DEFAULT '0' AFTER server_type, COMMENT=''");
 $adb->query("DROP TABLE tbl_s_workflow_function");
 $adb->query("ALTER TABLE tbl_v_account ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_v_contactdetails ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_v_leaddetails ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE tbl_v_leadimportemails 
			DROP KEY NewIndex1, 
			ADD KEY tbl_v_leadimp_mailid_idx(mailid), ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_accountdepstatus 
			CHANGE deploymentstatusid deploymentstatusid INT(19)   NOT NULL FIRST, 
			ADD UNIQUE KEY accdep_deploymentstatus_idx(deploymentstatus), 
			DROP KEY accountdepstatus_deploymentstatus_idx, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_accountownership 
			CHANGE acctownershipid acctownershipid INT(19)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_accountrating 
			CHANGE accountratingid accountratingid INT(19)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_accountregion 
			CHANGE accountregionid accountregionid INT(19)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_act_reminder_popup 
			CHANGE reminderid reminderid INT(19)   NOT NULL FIRST, 
			CHANGE status status INT(2)   NULL AFTER time_start, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_activity 
			ADD KEY activity_activityid_subj_idx(activityid,subject), 
			DROP KEY activity_activityid_subject_idx, 
			DROP KEY activity_activitytype_date_start_idx, 
			DROP KEY activity_date_start_due_date_idx, 
			DROP KEY activity_date_start_time_start_idx, 
			ADD KEY activitytype_date_start_idx(activitytype,date_start), 
			ADD KEY date_start_due_date_idx(date_start,due_date), 
			ADD KEY date_start_time_start_idx(date_start,time_start), COMMENT=''");
 $adb->query("ALTER TABLE vtiger_activityproductrel 
			ADD KEY activityproductrel_actid_idx(activityid), 
			DROP KEY activityproductrel_activityid_idx, 
			ADD KEY activityproductrel_prodid_idx(productid), 
			DROP KEY activityproductrel_productid_idx, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_activsubtype 
			CHANGE activesubtypeid activesubtypeid INT(19)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_announcement 
			CHANGE time time TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER title, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_asteriskincomingcalls 
			ADD COLUMN refuid VARCHAR(255)  COLLATE utf8_general_ci NULL AFTER timer, COMMENT=''");
 $adb->query("CREATE TABLE vtiger_asteriskincomingevents(
				uid VARCHAR(255) COLLATE utf8_general_ci NOT NULL  , 
				channel VARCHAR(100) COLLATE utf8_general_ci NULL  , 
				from_number BIGINT(20) NULL  , 
				from_name VARCHAR(100) COLLATE utf8_general_ci NULL  , 
				to_number BIGINT(20) NULL  , 
				callertype VARCHAR(100) COLLATE utf8_general_ci NULL  , 
				timer INT(20) NULL  , 
				flag VARCHAR(3) COLLATE utf8_general_ci NULL  , 
				pbxrecordid INT(19) NULL  , 
				relcrmid INT(19) NULL  , 
				PRIMARY KEY (uid) 
			) ENGINE=MYISAM DEFAULT CHARSET='utf8'");
 $adb->query("DROP TABLE vtiger_asteriskoutgoingcalls");
 $adb->query("ALTER TABLE vtiger_audit_trial 
			CHANGE actiondate actiondate TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER recordid, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_blocks 
			CHANGE blocklabel blocklabel VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER tabid, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_businesstype 
			CHANGE businesstypeid businesstypeid INT(19)   NOT NULL FIRST, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_campaignaccountrel 
			CHANGE campaignrelstatusid campaignrelstatusid INT(19)   NULL AFTER accountid, 
			DROP KEY campaignaccrel_accountid_idx, 
			ADD KEY campaigncontrel_accid_idx(accountid,campaignid), COMMENT=''");
 $adb->query("ALTER TABLE vtiger_campaigncontrel 
			ADD COLUMN campaignrelstatusid INT(19)   NULL AFTER contactid, 
			DROP KEY campaigncontrel_contractid_idx, ADD KEY campaigncontrel_contractid_idx(contactid,campaignid), COMMENT=''");
 $adb->query("ALTER TABLE vtiger_campaignleadrel 
			ADD COLUMN campaignrelstatusid INT(19)   NULL AFTER leadid, 
			ADD KEY campaignleadrel_ldid_cid_idx(leadid,campaignid), 
			DROP KEY campaignleadrel_leadid_campaignid_idx, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_campaignstatus 
			DROP KEY campaignstatus_campaignstatus_idx, 
			ADD KEY campaignstatus_cstat_idx(campaignstatus), COMMENT=''");
 $adb->query("ALTER TABLE vtiger_category 
			CHANGE categoryid categoryid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, ENGINE=INNODB, COMMENT=''");
 $adb->query("ALTER TABLE vtiger_chat_msg 
			CHANGE id id INT(20)   NOT NULL FIRST, 
			CHANGE born born TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER chat_to, COMMENT=''");
$adb->query("ALTER TABLE vtiger_chat_pchat 
			CHANGE id id INT(20)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_chat_pvchat 
			CHANGE id id INT(20)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_chat_users 
			CHANGE id id INT(20)   NOT NULL FIRST, 
			CHANGE ping ping TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER ip, COMMENT=''");
$adb->query("ALTER TABLE vtiger_contacttype 
			CHANGE contacttypeid contacttypeid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_contpotentialrel 
			DROP KEY contpotentialrel_contactid_idx, 
			DROP KEY contpotentialrel_potentialid_idx, 
			ADD KEY contpotrel_contactid_idx(contactid), 
			ADD KEY contpotrel_potentialid_idx(potentialid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_contract_priority 
			CHANGE contract_priorityid contract_priorityid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_contract_status 
			CHANGE contract_statusid contract_statusid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_contract_type 
			CHANGE contract_typeid contract_typeid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_crmentity 
			CHANGE createdtime createdtime TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER description, 
			CHANGE modifiedtime modifiedtime TIMESTAMP   NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER createdtime, 
			CHANGE viewedtime viewedtime TIMESTAMP   NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER modifiedtime, 
			ADD KEY crmentity_owner_del_idx(smownerid,deleted), 
			DROP KEY crmentity_smownerid_deleted_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_currencies 
			CHANGE currency_symbol currency_symbol VARCHAR(20)  COLLATE utf8_general_ci NULL AFTER currency_code, COMMENT=''");
$adb->query("ALTER TABLE vtiger_currency 
			CHANGE currencyid currencyid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_currency_info 
			CHANGE id id INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_customerportal_prefs 
			ADD KEY cust_prefs_tabid_idx(tabid), 
			ADD PRIMARY KEY(tabid), 
			DROP KEY tabid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_customerportal_tabs 
			ADD KEY cust_tabs_tabid_idx(tabid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_customview 
			ADD COLUMN status INT(1)   NULL DEFAULT '1' AFTER crmv_user_id, 
			ADD COLUMN userid INT(19)   NULL DEFAULT '1' AFTER status, COMMENT=''");
$adb->query("UPDATE vtiger_customview SET status = 0 WHERE viewname = 'All'");
$adb->query("ALTER TABLE vtiger_cvadvfilter 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER columnindex, 
			CHANGE comparator comparator VARCHAR(10)  COLLATE utf8_general_ci NULL AFTER columnname, 
			CHANGE value value VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER comparator, COMMENT=''");
$adb->query("ALTER TABLE vtiger_cvcolumnlist 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER columnindex, COMMENT=''");
$adb->query("ALTER TABLE vtiger_cvstdfilter 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER cvid, 
			CHANGE stdfilter stdfilter VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER columnname, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_grp2grp 
			ADD KEY datashare_grp2grp_share_gr_idx(share_groupid), 
			DROP KEY datashare_grp2grp_share_groupid_idx, 
			ADD KEY datashare_grp2grp_to_gr_idx(to_groupid), 
			DROP KEY datashare_grp2grp_to_groupid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_grp2role 
			ADD KEY idx_datashare_grp2role_gr(share_groupid), 
			ADD KEY idx_datashare_grp2role_rl(to_roleid), 
			DROP KEY idx_datashare_grp2role_share_groupid, 
			DROP KEY idx_datashare_grp2role_to_roleid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_grp2rs 
			ADD KEY datashare_grp2rs_groupid_idx(share_groupid), 
			DROP KEY datashare_grp2rs_share_groupid_idx, 
			ADD KEY datashare_grp2rs_to_rlsub_idx(to_roleandsubid), 
			DROP KEY datashare_grp2rs_to_roleandsubid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_mod_rel 
			ADD KEY idx_datashare_mod_rel_tabid(tabid), 
			DROP KEY idx_datashare_module_rel_tabid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_relmod 
			DROP KEY datashare_relatedmodules_relatedto_tabid_idx, 
			DROP KEY datashare_relatedmodules_tabid_idx, 
			ADD KEY datashare_relmod_reltabid_idx(relatedto_tabid), 
			ADD KEY datashare_relmod_tabid_idx(tabid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_relmod_perm 
			DROP KEY datashare_relatedmodule_permission_shareid_permissions_idx, 
			ADD KEY datashare_relmod_perm_id_idx(shareid,permission), COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_role2group 
			ADD KEY idx_datashare_rl2group_grid(to_groupid), 
			ADD KEY idx_datashare_rl2group_rlid(share_roleid), 
			DROP KEY idx_datashare_role2group_share_roleid, 
			DROP KEY idx_datashare_role2group_to_groupid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_role2role 
			ADD KEY datashare_rl2rl_roleid_idx(share_roleid), 
			ADD KEY datashare_rl2rl_to_roleid_idx(to_roleid), 
			DROP KEY datashare_role2role_share_roleid_idx, 
			DROP KEY datashare_role2role_to_roleid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_role2rs 
			ADD KEY datashare_role2s_roleid_idx(share_roleid), 
			DROP KEY datashare_role2s_share_roleid_idx, 
			ADD KEY datashare_role2s_to_rlsub_idx(to_roleandsubid), 
			DROP KEY datashare_role2s_to_roleandsubid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_rs2grp 
			ADD KEY datashare_rs2grp_rlsub_idx(share_roleandsubid), 
			DROP KEY datashare_rs2grp_share_roleandsubid_idx, 
			ADD KEY datashare_rs2grp_to_grid_idx(to_groupid), 
			DROP KEY datashare_rs2grp_to_groupid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_rs2role 
			ADD KEY datashare_rs2r_to_rl_idx(to_roleid), 
			ADD KEY datashare_rs2role_rlsubid_idx(share_roleandsubid), 
			DROP KEY datashare_rs2role_share_roleandsubid_idx, 
			DROP KEY datashare_rs2role_to_roleid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_rs2rs 
			ADD KEY datashare_rs2rs_rlandsub_idx(share_roleandsubid), 
			DROP KEY datashare_rs2rs_share_roleandsubid_idx, 
			DROP KEY idx_datashare_rs2rs_to_roleandsubid_idx, 
			ADD KEY idx_rs2rs_to_rlandsub_idx(to_roleandsubid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_datashare_usr2usr 
			ADD KEY data_usr2usr_to_usrid_idx(to_userid), 
			DROP KEY datashare_usr2usr_share_userid_idx, 
			DROP KEY datashare_usr2usr_to_userid_idx, 
			ADD KEY datashare_usr2usr_userid_idx(share_userid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_def_org_field 
			DROP KEY def_org_field_tabid_fieldid_idx, 
			ADD KEY def_org_field_tabid_fldid_idx(tabid,fieldid), 
			DROP KEY def_org_field_visible_fieldid_idx, 
			ADD KEY deforgfield_visible_fldid_idx(visible,fieldid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_downloadpurpose 
			CHANGE downloadpurposeid downloadpurposeid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_durationhrs 
			CHANGE hrsid hrsid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_durationmins 
			CHANGE minsid minsid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_email_access 
			CHANGE accesstime accesstime TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER accessdate, COMMENT=''");
$adb->query("ALTER TABLE vtiger_email_track 
			ADD UNIQUE KEY emaillink_tabidtype_idx(crmid,mailid), 
			DROP KEY link_tabidtype_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_emaildetails 
			CHANGE assigned_user_email assigned_user_email VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER emailid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_emailtemplates 
			CHANGE body body LONGTEXT  COLLATE utf8_general_ci NULL AFTER description, 
			DROP KEY emailtemplates_foldernamd_templatename_subject_idx, 
			ADD KEY emailtemplates_subject_idx(foldername,templatename,subject), COMMENT=''");
$adb->query("ALTER TABLE vtiger_evaluationstatus 
			CHANGE evalstatusid evalstatusid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_eventstatus 
			CHANGE history history INT(1)   NULL DEFAULT '0' AFTER picklist_valueid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_failtype 
			CHANGE failtypeid failtypeid INT(11)   NULL FIRST, 
			CHANGE failtype failtype VARCHAR(255)  COLLATE utf8_general_ci NULL AFTER failtypeid, 
			CHANGE presence presence INT(11)   NULL AFTER failtype, 
			DROP KEY PRIMARY, COMMENT=''");
$adb->query("ALTER TABLE vtiger_failtype_permisions ENGINE=INNODB, COMMENT=''");
$adb->query("ALTER TABLE vtiger_faq 
			CHANGE id id INT(11)   NOT NULL FIRST, 
			CHANGE category category VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER answer, 
			CHANGE status status VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER category, COMMENT=''");
$adb->query("ALTER TABLE vtiger_faqcomments 
			CHANGE commentid commentid INT(19)   NOT NULL FIRST, 
			CHANGE createdtime createdtime TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER comments, COMMENT=''");
$adb->query("ALTER TABLE vtiger_faxdetails 
			CHANGE from_number from_number VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER faxid, 
			CHANGE assigned_user_number assigned_user_number VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER to_number, 
			CHANGE idlists idlists VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER assigned_user_number, 
			CHANGE fax_flag fax_flag VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER idlists, COMMENT=''");
$adb->query("ALTER TABLE vtiger_files 
			CHANGE date_entered date_entered TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER deleted, 
			DROP KEY files_assigned_user_id_name_deleted_idx, 
			ADD KEY files_name_deleted_idx(assigned_user_id,name,deleted), COMMENT=''");
$adb->query("ALTER TABLE vtiger_freetagged_objects 
			CHANGE tagged_on tagged_on TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER object_id, 
			CHANGE module module VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER tagged_on, 
			ADD KEY freetagged_object_id_idx(tag_id,tagger_id,object_id), 
			DROP KEY freetagged_objects_tag_id_tagger_id_object_id_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_freetags 
			CHANGE tag tag VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER id, 
			CHANGE raw_tag raw_tag VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER tag, COMMENT=''");
$adb->query("ALTER TABLE vtiger_headers 
			CHANGE fileid fileid INT(3)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_homedashbd 
			ADD KEY homedashbd_stuffid_idx(stuffid), 
			DROP KEY stuff_stuffid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_homedefault 
			ADD KEY stuff_stuffid_def_idx(stuffid), 
			DROP KEY stuff_stuffid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_homemodule 
			ADD KEY homemodule_stuffid_idx(stuffid), 
			DROP KEY stuff_stuffid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_homemoduleflds 
			ADD KEY homemoduleflds_stuffid_idx(stuffid), 
			DROP KEY stuff_stuffid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_homerss 
			ADD KEY homerss_stuffid_idx(stuffid), 
			DROP KEY stuff_stuffid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_homewidget_url ENGINE=INNODB, COMMENT=''");
$adb->query("ALTER TABLE vtiger_import_maps 
			CHANGE content content LONGTEXT  COLLATE utf8_general_ci NULL AFTER module, 
			ADD KEY im_usr_mod_nam_del_idx(assigned_user_id,module,name,deleted), 
			DROP KEY import_maps_assigned_user_id_module_name_deleted_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_insufficient_stock ENGINE=INNODB, COMMENT=''");
$adb->query("ALTER TABLE vtiger_inventorynotify 
			CHANGE notificationbody notificationbody LONGTEXT  COLLATE utf8_general_ci NULL AFTER notificationsubject, COMMENT=''");
$adb->query("ALTER TABLE vtiger_inventoryproductrel 
			CHANGE lineitem_id lineitem_id INT(11)   NOT NULL AFTER incrementondel, 
			DROP KEY inventoryproductrel_productid_idx, 
			ADD KEY invprodrel_prdid_idx(productid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_invoice 
			CHANGE discount_percent discount_percent DECIMAL(25,0)   NULL AFTER taxtype, 
			CHANGE discount_amount discount_amount DECIMAL(25,0)   NULL AFTER discount_percent, COMMENT=''");
$adb->query("ALTER TABLE vtiger_invoicestatus 
			DROP KEY invoicestatus_invoiestatus_idx, 
			ADD UNIQUE KEY invoicestatus_invstat_idx(invoicestatus), COMMENT=''");
$adb->query("ALTER TABLE vtiger_invoicestatushistory 
			CHANGE historyid historyid INT(19)   NOT NULL FIRST, 
			CHANGE lastmodified lastmodified TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER invoicestatus, 
			DROP KEY invoicestatushistory_invoiceid_idx, 
			ADD KEY invstatushistory_invid_idx(invoiceid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_language 
			CHANGE lastupdated lastupdated TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER label, COMMENT=''");
$adb->query("ALTER TABLE vtiger_leaddetails 
			ADD KEY lead_conv_leadstatus_idx(converted,leadstatus), 
			DROP KEY leaddetails_converted_leadstatus_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_leadstage 
			CHANGE leadstageid leadstageid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_loginhistory 
			CHANGE login_id login_id INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_mailscanner 
			CHANGE scannerid scannerid INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_mailscanner_actions 
			CHANGE actionid actionid INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_mailscanner_folders 
			CHANGE folderid folderid INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_mailscanner_rules 
			CHANGE ruleid ruleid INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_manufacturer 
			ADD UNIQUE KEY manufacturer_idx(manufacturer), 
			DROP KEY manufacturer_manufacturer_idx, COMMENT=''");
$adb->query("DROP TABLE vtiger_memory_center"); 
$adb->query("CREATE TABLE vtiger_mobile_alerts(
				id INT(11) NOT NULL  , 
				handler_path VARCHAR(500) COLLATE utf8_general_ci NULL  , 
				handler_class VARCHAR(50) COLLATE utf8_general_ci NULL  , 
				sequence INT(11) NULL  , 
				deleted INT(11) NOT NULL  DEFAULT '0' , 
				PRIMARY KEY (id) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
$adb->query("ALTER TABLE vtiger_modentity_num 
			CHANGE prefix prefix VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER semodule, COMMENT=''");
$adb->query("ALTER TABLE vtiger_moduleowners 
			DROP KEY moduleowners_tabid_user_id_idx, 
			ADD KEY moduleowners_tabid_usrid_idx(tabid,user_id), COMMENT=''");
$adb->query("ALTER TABLE vtiger_notes 
			CHANGE filesize filesize DECIMAL(10,0)   NULL AFTER filetype, 
			CHANGE filedownloadcount filedownloadcount DECIMAL(10,0)   NULL AFTER filesize, 
			CHANGE folderid folderid DECIMAL(10,0)   NULL AFTER fileversion, COMMENT=''");
$adb->query("ALTER TABLE vtiger_notifyscheduler 
			CHANGE notificationbody notificationbody LONGTEXT  COLLATE utf8_general_ci NULL AFTER notificationsubject, 
			DROP KEY notificationscheduler_schedulednotificationname_idx, 
			ADD UNIQUE KEY notifyschedulername_idx(schedulednotificationname), COMMENT=''");
$adb->query("ALTER TABLE vtiger_opportunity_type 
			ADD UNIQUE KEY opportunity_type_idx(opportunity_type), 
			DROP KEY opportunity_type_opportunity_type_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_opportunitystage 
			CHANGE potstageid potstageid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_org_share_action2tab 
			ADD KEY fk_2_vtiger_org_action2tab(tabid), 
			DROP KEY fk_2_vtiger_org_share_action2tab, COMMENT=''");
$adb->query("ALTER TABLE vtiger_parenttab 
			ADD KEY parenttab_label_visible_idx(parenttabid,parenttab_label,visible), 
			DROP KEY parenttab_parenttabid_parenttabl_label_visible_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_parenttabrel 
			ADD KEY parenttabrel_parenttabid_idx(tabid,parenttabid), 
			DROP KEY parenttabrel_tabid_parenttabid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_portalinfo 
			CHANGE last_login_time last_login_time TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP AFTER type, 
			CHANGE login_time login_time TIMESTAMP   NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER last_login_time, 
			CHANGE logout_time logout_time TIMESTAMP   NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER login_time, COMMENT=''");
$adb->query("ALTER TABLE vtiger_postatushistory 
			CHANGE lastmodified lastmodified TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER postatus, 
			ADD KEY postatush_purchaseorderid_idx(purchaseorderid), 
			DROP KEY postatushistory_purchaseorderid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_potcompetitorrel 
			DROP KEY potcompetitorrel_competitorid_idx, 
			ADD KEY potcompetitorrel_compid_idx(competitorid), 
			DROP KEY potcompetitorrel_potentialid_idx, 
			ADD KEY potcompetitorrel_potid_idx(potentialid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_potstagehistory 
			CHANGE historyid historyid INT(19)   NOT NULL FIRST, 
			CHANGE lastmodified lastmodified TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER closedate, 
			DROP KEY potstagehistory_potentialid_idx, 
			ADD KEY potstagehistory_potid_idx(potentialid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_pricebook 
			CHANGE conversion_rate conversion_rate DECIMAL(10,0)   NULL AFTER currency_id, COMMENT=''");
$adb->query("ALTER TABLE vtiger_pricebookproductrel 
			ADD KEY pricebookproductrel_pbid_idx(pricebookid), 
			DROP KEY pricebookproductrel_pricebookid_idx, 
			ADD KEY pricebookproductrel_prid_idx(productid), 
			DROP KEY pricebookproductrel_productid_idx, COMMENT=''");
$adb->query("DROP TABLE vtiger_priority"); 
$adb->query("ALTER TABLE vtiger_product_lines 
			CHANGE product_lines product_lines VARCHAR(200)  COLLATE utf8_general_ci NOT NULL AFTER product_linesid, 
			CHANGE budget budget DECIMAL(30,3)   NULL AFTER presence, ENGINE=INNODB, COMMENT='', DEFAULT CHARSET='utf8'");
$adb->query("ALTER TABLE vtiger_productcategory 
			ADD UNIQUE KEY productcategory_prdcat_idx(productcategory), 
			DROP KEY productcategory_productcategory_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_productcollaterals 
			CHANGE date_entered date_entered TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER productid, 
			ADD KEY productcollaterals_pidf_idx(productid,filename), 
			DROP KEY productcollaterals_productid_filename_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_productcurrencyrel 
			CHANGE converted_price converted_price DECIMAL(25,3)   NULL AFTER currencyid, 
			CHANGE actual_price actual_price DECIMAL(25,3)   NULL AFTER converted_price, 
			ADD KEY FK_vtiger_productcurrencyrel(productid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_products 
			CHANGE product_no product_no VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER productid, 
			CHANGE productname productname VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER product_no, 
			CHANGE productcode productcode VARCHAR(40)  COLLATE utf8_general_ci NULL AFTER productname, 
			CHANGE productcategory productcategory VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER productcode, 
			CHANGE manufacturer manufacturer VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER productcategory, 
			CHANGE product_description product_description TEXT  COLLATE utf8_general_ci NULL AFTER manufacturer, 
			CHANGE qty_per_unit qty_per_unit DECIMAL(11,2)   NULL DEFAULT '0.00' AFTER product_description, 
			CHANGE unit_price unit_price DECIMAL(25,2)   NULL AFTER qty_per_unit, 
			CHANGE weight weight DECIMAL(11,3)   NULL AFTER unit_price, 
			CHANGE pack_size pack_size INT(11)   NULL AFTER weight, 
			CHANGE sales_start_date sales_start_date DATE   NULL AFTER pack_size, 
			CHANGE sales_end_date sales_end_date DATE   NULL AFTER sales_start_date, 
			CHANGE start_date start_date DATE   NULL AFTER sales_end_date, 
			CHANGE expiry_date expiry_date DATE   NULL AFTER start_date, 
			CHANGE cost_factor cost_factor INT(11)   NULL AFTER expiry_date, 
			CHANGE commissionrate commissionrate DECIMAL(7,3)   NULL AFTER cost_factor, 
			CHANGE commissionmethod commissionmethod VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER commissionrate, 
			CHANGE discontinued discontinued INT(1)   NOT NULL DEFAULT '0' AFTER commissionmethod, 
			CHANGE usageunit usageunit VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER discontinued, 
			CHANGE handler handler INT(11)   NULL AFTER usageunit, 
			CHANGE currency currency VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER handler, 
			CHANGE reorderlevel reorderlevel INT(11)   NULL AFTER currency, 
			CHANGE website website VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER reorderlevel, 
			CHANGE taxclass taxclass VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER website, 
			CHANGE mfr_part_no mfr_part_no VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER taxclass, 
			CHANGE vendor_part_no vendor_part_no VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER mfr_part_no, 
			CHANGE serialno serialno VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER vendor_part_no, 
			CHANGE qtyinstock qtyinstock DECIMAL(25,3)   NULL AFTER serialno, 
			CHANGE productsheet productsheet VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER qtyinstock, 
			CHANGE qtyindemand qtyindemand INT(11)   NULL AFTER productsheet, 
			CHANGE glacct glacct VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER qtyindemand, 
			CHANGE vendor_id vendor_id INT(11)   NULL AFTER glacct, 
			CHANGE imagename imagename TEXT  COLLATE utf8_general_ci NULL AFTER vendor_id, 
			CHANGE associated associated INT(1)   NULL DEFAULT '0' AFTER imagename, 
			CHANGE currency_id currency_id INT(19)   NOT NULL DEFAULT '1' AFTER associated, COMMENT=''");
$adb->query("ALTER TABLE vtiger_profile 
			CHANGE profileid profileid INT(10)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_profile2field 
			ADD KEY profile2field_pid_tabid_idx(profileid,tabid), 
			DROP KEY profile2field_profileid_tabid_fieldname_idx, 
			ADD KEY profile2field_tabid_pid_idx(tabid,profileid), 
			DROP KEY profile2field_tabid_profileid_idx, 
			ADD KEY profile2field_visible_pid_idx(visible,profileid), 
			DROP KEY profile2field_visible_profileid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_profile2standardperm 
			ADD KEY profile2standardperm_op_idx(profileid,tabid,Operation), 
			DROP KEY profile2standardpermissions_profileid_tabid_operation_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_profile2tab 
			ADD KEY profile2tab_pid_tabid_idx(profileid,tabid), 
			DROP KEY profile2tab_profileid_tabid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_profile2utility 
			ADD KEY profile2utility_pid_tabid_idx(profileid,tabid,activityid), 
			DROP KEY profile2utility_profileid_tabid_activityid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_projects_failhour 
			CHANGE work_hour work_hour DECIMAL(10,0)   NOT NULL AFTER type_fail, 
			DROP KEY worker_userid, 
			ADD KEY worker_userid_fail(worker_userid,work_date,type_fail,work_hour,status), ENGINE=INNODB, COMMENT=''");
$adb->query("ALTER TABLE vtiger_projects_hours 
			CHANGE hour hour DECIMAL(11,0)   NOT NULL AFTER work_date, 
			DROP KEY projectid, 
			ADD KEY projectid_hours(projectid,worker_userid,work_date,hour,status), ENGINE=INNODB, COMMENT=''");
$adb->query("DROP TABLE vtiger_projectsgrouprelation"); 
$adb->query("ALTER TABLE vtiger_projectworkers 
			CHANGE id id INT(11)   NOT NULL FIRST, 
			CHANGE hour hour DECIMAL(10,0)   NOT NULL AFTER worker_userid, ENGINE=INNODB, COMMENT=''");
$adb->query("ALTER TABLE vtiger_quotestagehistory 
			CHANGE historyid historyid INT(19)   NOT NULL FIRST, 
			CHANGE lastmodified lastmodified TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER quotestage, COMMENT=''");
$adb->query("ALTER TABLE vtiger_recurringevents 
			CHANGE recurringid recurringid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_relatedlists 
			CHANGE actions actions VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_relcriteria 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER columnindex, 
			CHANGE comparator comparator VARCHAR(10)  COLLATE utf8_general_ci NULL AFTER columnname, 
			CHANGE value value VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER comparator, 
			ADD COLUMN groupid INT(11)   NULL DEFAULT '1' AFTER value, 
			ADD COLUMN column_condition VARCHAR(256)  COLLATE utf8_general_ci NULL DEFAULT 'and' AFTER groupid, COMMENT=''");
//crmv@21833
$result = $adb->query('SELECT queryid,groupid FROM vtiger_relcriteria GROUP BY queryid');
while($row=$adb->fetchByAssoc($result)) {
	$adb->query("DELETE FROM vtiger_relcriteria where queryid = ".$row['queryid']." and columnname = ''");
	$adb->pquery('INSERT INTO vtiger_relcriteria_grouping(groupid,queryid,group_condition,condition_expression) VALUES (?,?,?,?)',array($row['groupid'],$row['queryid'],'',' 0 '));
}
//crmv@21833e
$adb->query("ALTER TABLE vtiger_report 
			CHANGE reportname reportname VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER folderid, 
			CHANGE description description VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER reportname, 
			CHANGE reporttype reporttype VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER description, COMMENT=''");
$adb->query("ALTER TABLE vtiger_reportdatefilter 
			CHANGE datecolumnname datecolumnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER datefilterid, 
			CHANGE datefilter datefilter VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER datecolumnname, 
			DROP KEY reportdatefilter_datefilterid_idx, 
			ADD KEY reportdtflter_datefilterid_idx(datefilterid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_reportfolder 
			CHANGE folderid folderid INT(19)   NOT NULL FIRST, 
			CHANGE foldername foldername VARCHAR(100)  COLLATE utf8_general_ci NOT NULL AFTER folderid, 
			CHANGE description description VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER foldername, COMMENT=''");
$adb->query("ALTER TABLE vtiger_reportmodules 
			CHANGE primarymodule primarymodule VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER reportmodulesid, 
			CHANGE secondarymodules secondarymodules VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER primarymodule, COMMENT=''");
$adb->query("ALTER TABLE vtiger_reportsortcol 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER reportid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_reportsummary 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NOT NULL AFTER summarytype, 
			ADD KEY reportsummary_id_idx(reportsummaryid), 
			DROP KEY reportsummary_reportsummaryid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_revenuetype 
			CHANGE revenuetypeid revenuetypeid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_role2picklist 
			ADD KEY role2picklist_rlid_pckid_idx(roleid,picklistid,picklistvalueid), 
			DROP KEY role2picklist_roleid_picklistid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_role2profile 
			ADD KEY role2profile_rlid_prfid_idx(roleid,profileid), 
			DROP KEY role2profile_roleid_profileid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_rss 
			CHANGE rssurl rssurl VARCHAR(200)  COLLATE utf8_general_ci NOT NULL AFTER rssid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_salesmanactivityrel 
			ADD KEY salesmanactivityrel_actid_idx(activityid), 
			DROP KEY salesmanactivityrel_activityid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_salesmanattachmentsrel 
			DROP KEY salesmanattachmentsrel_attachmentsid_idx, 
			DROP KEY salesmanattachmentsrel_smid_idx, 
			ADD KEY salesmanattrel_attid_idx(attachmentsid), 
			ADD KEY salesmanattrel_smid_idx(smid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_seattachmentsrel 
			DROP KEY seattachmentsrel_attachmentsid_crmid_idx, 
			DROP KEY seattachmentsrel_attachmentsid_idx, 
			ADD KEY seattrel_attachmentsid_idx(attachmentsid), 
			ADD KEY seattrel_attid_crmid_idx(attachmentsid,crmid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_selectcolumn 
			CHANGE columnname columnname VARCHAR(250)  COLLATE utf8_general_ci NULL AFTER columnindex, COMMENT=''");
$adb->query("ALTER TABLE vtiger_service_usageunit 
			CHANGE service_usageunitid service_usageunitid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_servicecategory 
			CHANGE servicecategoryid servicecategoryid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_servicecontracts 
			CHANGE servicecontractsid servicecontractsid INT(11)   NOT NULL FIRST, 
			ADD PRIMARY KEY(servicecontractsid), 
			ADD KEY vtiger_servicecontracts_idx(servicecontractsid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_smsdetails 
			CHANGE from_number from_number VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER smsid, 
			CHANGE assigned_user_number assigned_user_number VARCHAR(50)  COLLATE utf8_general_ci NULL AFTER to_number, 
			CHANGE idlists idlists VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER assigned_user_number, 
			CHANGE sms_flag sms_flag VARCHAR(50)  COLLATE utf8_general_ci NOT NULL AFTER idlists, COMMENT=''");
$adb->query("ALTER TABLE vtiger_sostatushistory 
			CHANGE historyid historyid INT(19)   NOT NULL FIRST, 
			CHANGE lastmodified lastmodified TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER sostatus, 
			DROP KEY sostatushistory_salesorderid_idx, 
			ADD KEY sostatushistory_sid_idx(salesorderid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_systems 
			CHANGE inc_call inc_call LONGBLOB   NULL AFTER name, COMMENT=''");
$adb->query("ALTER TABLE vtiger_taskstatus 
			CHANGE history history INT(1)   NULL DEFAULT '0' AFTER picklist_valueid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_ticketcomments 
			CHANGE commentid commentid INT(19)   NOT NULL FIRST, 
			CHANGE createdtime createdtime TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER ownertype, COMMENT=''");
$adb->query("ALTER TABLE vtiger_ticketstracktime 
			DROP KEY ticketstracktime_ticket_id_idx, 
			ADD KEY ticketstracktm_ticket_id_idx(ticket_id), COMMENT=''");
$adb->query("ALTER TABLE vtiger_timecardtype 
			CHANGE timecardtypeid timecardtypeid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("DROP TABLE if exists vtiger_timecardtypes"); 
$adb->query("ALTER TABLE vtiger_tmp_read_g_per 
			ADD KEY tmp_read_g_per_uid_shuid_idx(userid,sharedgroupid), 
			DROP KEY tmp_read_group_sharing_per_userid_sharedgroupid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_read_g_rel_per 
			ADD KEY tmp_read_g_rel_per_uid_shgid(userid,sharedgroupid,tabid), 
			DROP KEY tmp_read_group_rel_sharing_per_userid_sharedgroupid_tabid, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_read_u_per 
			ADD KEY tmp_read_u_per_uid_shid_idx(userid,shareduserid), 
			DROP KEY tmp_read_user_sharing_per_userid_shareduserid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_read_u_rel_per 
			ADD KEY tmp_read_u_rel_uid_tid_idx(userid,shareduserid,relatedtabid), 
			DROP KEY tmp_read_user_rel_sharing_per_userid_shared_reltabid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_write_g_per 
			ADD KEY tmp_write_g_per_UK1(userid,sharedgroupid), 
			DROP KEY tmp_write_group_sharing_per_UK1, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_write_g_rel_per 
			ADD KEY tmp_write_g_rel_uid_shuid_idx(userid,sharedgroupid,tabid), 
			DROP KEY tmp_write_group_rel_sharing_per_userid_sharedgroupid_tabid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_write_u_per 
			ADD KEY tmp_write_u_uid_shuid_idx(userid,shareduserid), 
			DROP KEY tmp_write_user_sharing_per_userid_shareduserid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tmp_write_u_rel_per 
			ADD KEY tmp_write_u_uid_shuidr_idx(userid,shareduserid,tabid), 
			DROP KEY tmp_write_user_rel_sharing_per_userid_sharduserid_tabid_idx, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tracker 
			CHANGE id id INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_tracking_unit 
			CHANGE tracking_unitid tracking_unitid INT(19)   NOT NULL FIRST, 
			CHANGE picklist_valueid picklist_valueid INT(19)   NULL DEFAULT '0' AFTER presence, COMMENT=''");
$adb->query("ALTER TABLE vtiger_troubletickets 
			CHANGE internal_project_number internal_project_number TEXT  COLLATE utf8_general_ci NULL AFTER version_id, 
			CHANGE external_project_number external_project_number TEXT  COLLATE utf8_general_ci NULL AFTER internal_project_number, 
			ADD COLUMN hours VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER ticket_no, 
			ADD COLUMN days VARCHAR(200)  COLLATE utf8_general_ci NULL AFTER hours, COMMENT=''");
$adb->query("DROP TABLE if exists vtiger_tttimecards"); 
$adb->query("ALTER TABLE vtiger_user_module_preferences 
			ADD KEY fk_2_vtiger_user_module_pref(tabid), 
			DROP KEY fk_2_vtiger_user_module_preferences, COMMENT=''");
$adb->query("ALTER TABLE vtiger_users 
			CHANGE user_password user_password VARCHAR(128)  COLLATE utf8_general_ci NULL AFTER user_name, 
			CHANGE date_entered date_entered TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP AFTER description, 
			CHANGE date_modified date_modified TIMESTAMP   NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER date_entered, 
			CHANGE confirm_password confirm_password VARCHAR(128)  COLLATE utf8_general_ci NULL AFTER defhomeview, 
			CHANGE use_ldap use_ldap INT(1)   NULL AFTER is_mobileuser, 
			CHANGE reminder_interval reminder_interval VARCHAR(100)  COLLATE utf8_general_ci NOT NULL AFTER use_ldap, 
			CHANGE reminder_next_time reminder_next_time VARCHAR(100)  COLLATE utf8_general_ci NULL AFTER reminder_interval, 
			CHANGE accesskey accesskey VARCHAR(36)  COLLATE utf8_general_ci NULL AFTER reminder_next_time, 
			DROP COLUMN extension, COMMENT=''");
$adb->query("ALTER TABLE vtiger_users2group 
			DROP KEY users2group_groupname_uerid_idx, 
			ADD KEY users2group_grpname_uerid_idx(groupid,userid), COMMENT=''");
$adb->query("ALTER TABLE vtiger_usertype 
			CHANGE usertypeid usertypeid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_wordtemplates 
			CHANGE date_entered date_entered TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER module, 
			CHANGE filetype filetype VARCHAR(100)  COLLATE utf8_general_ci NOT NULL AFTER filesize, COMMENT=''");
$adb->query("DROP TABLE vtiger_workflow"); 
$adb->query("DROP TABLE vtiger_workflowcf"); 
$adb->query("ALTER TABLE vtiger_ws_entity_fieldtype 
			DROP KEY vtiger_idx_1_tablename_fieldname, 
			ADD UNIQUE KEY vtiger_idx_1_tblname_fldname(table_name,field_name), COMMENT=''");
$adb->query("ALTER TABLE vtiger_ws_fieldtype 
			CHANGE fieldtypeid fieldtypeid INT(19)   NOT NULL FIRST, COMMENT=''");
$adb->query("ALTER TABLE vtiger_ws_operation_parameters 
			CHANGE operationid operationid INT(11)   NOT NULL FIRST, COMMENT=''");
$adb->query("RENAME TABLE _script TO crmv_script");
$adb->query("RENAME TABLE _script_fields TO crmv_script_fields");
$adb->query("RENAME TABLE _script_seq TO crmv_script_seq");
$adb->query("update vtiger_field set uitype = 1 where columnname in ('hour_format','end_hour','start_hour')");
$adb->query("update vtiger_field_seq set id = id+2");
$adb->query("UPDATE vtiger_profile2field SET visible = 0 WHERE fieldid = 111");
$adb->query("delete from vtiger_field where tabid = 29 and columnname = 'extension'");
$adb->query("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=2 AND related_tabid = 20");
$adb->query("UPDATE vtiger_relatedlists SET actions='' WHERE tabid=6 AND related_tabid = 9 and name = 'get_history'");
$adb->query("UPDATE vtiger_relatedlists SET related_tabid = 22 WHERE tabid=20 AND related_tabid = 23");
$adb->query("delete from vtiger_relatedlists where tabid = 8 and related_tabid = 10");
$adb->query("update vtiger_relatedlists set actions = 'add' where relation_id = 83");
$adb->query("update vtiger_field set uitype = 16 where fieldname = 'hdnTaxType'");
$adb->query("UPDATE vtiger_field SET readonly = 100 WHERE columnname IN ('failtype') AND tabid IN (SELECT tabid FROM vtiger_tab WHERE NAME = 'Projects')");
$adb->query("UPDATE vtiger_field SET readonly = 99 WHERE columnname IN ('project_end') AND tabid IN (SELECT tabid FROM vtiger_tab WHERE NAME = 'Projects')");
$adb->query("UPDATE vtiger_failtype SET presence = 0");
$adb->query("UPDATE vtiger_projects_status SET projects_status = 'Open' WHERE projects_status = 'none'");
$adb->query("UPDATE vtiger_projects_status SET projects_status = 'In Progress' WHERE projects_status = 'Visible'");
$adb->query("UPDATE vtiger_projects_status SET projects_status = 'Closed',presence = 0 WHERE projects_status = 'Invoice'");
$adb->query("DELETE FROM vtiger_field WHERE tablename = 'vtiger_projectscf' AND columnname = 'failtype'");
$adb->query("INSERT INTO vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) VALUES ((SELECT tabid FROM vtiger_tab WHERE NAME = 'Projects'),(select max(fieldid)+1 from vtiger_field),'projects_status','vtiger_projectscf','1','15','projects_status','Status','0','0','0','100','20',(SELECT blockid FROM vtiger_blocks WHERE blocklabel = 'LBL_PROJECT_INFORMATION') ,'1','V~O','1','0','BAS','1',NULL)");
$adb->query("INSERT INTO vtiger_def_org_field VALUES ((SELECT tabid FROM vtiger_tab WHERE NAME = 'Projects'),(select max(fieldid)+1 from vtiger_field),0,1)");
$adb->query("INSERT INTO vtiger_profile2field SELECT profileid,(SELECT tabid FROM vtiger_tab WHERE NAME = 'Projects'),(select max(fieldid)+1 from vtiger_field),0,1 FROM  vtiger_profile");
$adb->query("update vtiger_field_seq set id = id+1");
?>