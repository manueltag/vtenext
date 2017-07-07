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

class ThemeConfig extends OptionableClass {
	
	protected $options = array(
		'primary_menu_position' => 'left',
		'secondary_menu_position' => 'right',
		
		'tpl_overrides' => array(
			'Login.tpl' => 'themes/next/Login.tpl',
			'DetailView.tpl' => 'themes/next/DetailView.tpl',
			'DetailViewBlocks.tpl' => 'themes/next/DetailViewBlocks.tpl',
			'VtlibWidgets.tpl' => 'themes/next/VtlibWidgets.tpl',
			'DetailViewWidgetHeader.tpl' => 'themes/next/DetailViewWidgetHeader.tpl',
			'modules/MyNotes/MyNotesDetailView.tpl' => 'themes/next/modules/MyNotes/MyNotesDetailView.tpl',
			'modules/MyNotes/widgets/Create.tpl' => 'themes/next/modules/MyNotes/widgets/Create.tpl',
			'modules/MyNotes/widgets/DetailViewMyNotesWidget.tpl' => 'themes/next/modules/MyNotes/widgets/DetailViewMyNotesWidget.tpl',
			'RelatedListContents.tpl' => 'themes/next/RelatedListContents.tpl',
			'RelatedListDataContents.tpl' => 'themes/next/RelatedListDataContents.tpl',
			'Home/MainHomeBlock.tpl' => 'themes/next/Home/MainHomeBlock.tpl',
			'modules/ModNotifications/widgets/DetailViewBlockComment.tpl' => 'themes/next/modules/ModNotifications/widgets/DetailViewBlockComment.tpl',
			'modules/ModNotifications/widgets/DetailViewBlockCommentItem.tpl' => 'themes/next/modules/ModNotifications/widgets/DetailViewBlockCommentItem.tpl',
			'header/MenuTopSettings.tpl' => 'header/MenuTopSettings.tpl',
			'modules/Area/Menu.tpl' => 'themes/next/modules/Area/Menu.tpl',
			'ListViewEntries.tpl' => 'themes/next/ListViewEntries.tpl',
			'salesEditView.tpl' => 'themes/next/salesEditView.tpl',
			'AddressCopy.tpl' => 'themes/next/AddressCopy.tpl',
			'Inventory/InventoryEditView.tpl' => 'themes/next/Inventory/InventoryEditView.tpl',
			'Inventory/ProductRowEdit.tpl' => 'themes/next/Inventory/ProductRowEdit.tpl',
			'Inventory/ProductsHeaderEdit.tpl' => 'themes/next/Inventory/ProductsHeaderEdit.tpl',
			'Inventory/ProductsFooterEdit.tpl' => 'themes/next/Inventory/ProductsFooterEdit.tpl',
			'Inventory/ProductDetailsEditView.tpl' => 'themes/next/Inventory/ProductDetailsEditView.tpl',
			'Home/HomeButtons.tpl' => 'themes/next/Home/HomeButtons.tpl',
			'UnifiedSearchAjax.tpl' => 'themes/next/UnifiedSearchAjax.tpl',
			'ModuleHome/Block.tpl' => 'themes/next/ModuleHome/Block.tpl',
			'modules/ModComments/widgets/DetailViewBlockComment.tpl' => 'themes/next/modules/ModComments/widgets/DetailViewBlockComment.tpl',
			'OrgSharingDetailView.tpl' => 'themes/next/OrgSharingDetailView.tpl',
			'OrgSharingEditView.tpl' => 'themes/next/OrgSharingEditView.tpl',
			'ListGroup.tpl' => 'themes/next/ListGroup.tpl',
			'KanbanView.tpl' => 'themes/next/KanbanView.tpl',
			'KanbanGrid.tpl' => 'themes/next/KanbanGrid.tpl',
			'KanbanColumn.tpl' => 'themes/next/KanbanColumn.tpl',
			'GroupDetailview.tpl' => 'themes/next/GroupDetailview.tpl',
			'ModuleHomeView.tpl' => 'themes/next/ModuleHomeView.tpl',
			'CustomView.tpl' => 'themes/next/CustomView.tpl',
			'modules/Campaigns/Statistics.tpl' => 'themes/next/modules/Campaigns/Statistics.tpl',
			'UserListView.tpl' => 'themes/next/UserListView.tpl',
			'UserListViewContents.tpl' => 'themes/next/UserListViewContents.tpl',
			'RoleDetailView.tpl' => 'themes/next/RoleDetailView.tpl',
			'ListRoles.tpl' => 'themes/next/ListRoles.tpl',
			'RoleEditView.tpl' => 'themes/next/RoleEditView.tpl',
			'Recover.tpl' => 'themes/next/Recover.tpl',
			'ListView.tpl' => 'themes/next/ListView.tpl',
			'TurboliftRelations.tpl' => 'themes/next/TurboliftRelations.tpl',
			'LoadingIndicator.tpl' => 'themes/next/LoadingIndicator.tpl',
			'modules/Area/Area.tpl' => 'themes/next/modules/Area/Area.tpl',
			'modules/Messages/ListView.tpl' => 'themes/next/modules/Messages/ListView.tpl',
		),
	);
	
}
