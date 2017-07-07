<?php
$documentsInstance = Vtiger_Module::getInstance('Documents');
Vtiger_Link::addLink($documentsInstance->id, 'DETAILVIEWWIDGET', 'DOC_PREVIEW', "module=Documents&action=DocumentsAjax&file=PreviewFile&mode=button&record=$"."RECORD$");

$trans = array(
	'Messages' => array(
		'it_it' => array(
			'LBL_VIEW_AS_EMAIL'=>'Visualizza come Email',
			'LBL_DOWNLOAD_ALL'=>'Scarica tutti gli allegati',
			'LBL_VIEW_DOCUMENT'=>'Visualizza Documento',
			'LBL_VIEWERJS_PRESENTATION'=>'Presentazione',
			'LBL_VIEWERJS_FULLSCREEN'=>'Schermo intero',
			'LBL_VIEWERJS_DOWNLOAD'=>'Scarica',
			'LBL_VIEWERJS_PREV_PAGE'=>'Pagina Precedente',
			'LBL_VIEWERJS_NEXT_PAGE'=>'Pagina Successiva',
			'LBL_VIEWERJS_PAGE'=>'Pagina',
			'LBL_VIEWERJS_ZOOM_OUT'=>'Riduci Zoom',
			'LBL_VIEWERJS_ZOOM_IN'=>'Aumenta Zoom',
			'LBL_VIEWERJS_ZOOM'=>'Zoom',
			'LBL_VIEWERJS_ZOOM_AUTO'=>'Automatico',
			'LBL_VIEWERJS_ZOOM_AS'=>'Dimensioni Effettive',
			'LBL_VIEWERJS_ZOOM_AL'=>'Adatta Larghezza',
		),
		'en_us' => array(
			'LBL_VIEW_AS_EMAIL'=>'View as Email',
			'LBL_DOWNLOAD_ALL'=>'Download all attachments',
			'LBL_VIEW_DOCUMENT'=>'View Document',
			'LBL_VIEWERJS_PRESENTATION'=>'Presentation',
			'LBL_VIEWERJS_FULLSCREEN'=>'Fullscreen',
			'LBL_VIEWERJS_DOWNLOAD'=>'Download',
			'LBL_VIEWERJS_PREV_PAGE'=>'Previous Page',
			'LBL_VIEWERJS_NEXT_PAGE'=>'Next Page',
			'LBL_VIEWERJS_PAGE'=>'Page',
			'LBL_VIEWERJS_ZOOM_OUT'=>'Zoom Out',
			'LBL_VIEWERJS_ZOOM_IN'=>'Zoom In',
			'LBL_VIEWERJS_ZOOM'=>'Zoom',
			'LBL_VIEWERJS_ZOOM_AUTO'=>'Automatic',
			'LBL_VIEWERJS_ZOOM_AS'=>'Actual Size',
			'LBL_VIEWERJS_ZOOM_AL'=>'Full Width',
		),
		'de_de' => array(
			'LBL_VIEW_AS_EMAIL'=>'Als E-Mail',
			'LBL_DOWNLOAD_ALL'=>'Alle Anhänge herunterladen',
			'LBL_VIEW_DOCUMENT'=>'Dokument anzeigen',
			'LBL_VIEWERJS_PRESENTATION'=>'Präsentation',
			'LBL_VIEWERJS_FULLSCREEN'=>'Vollbild',
			'LBL_VIEWERJS_DOWNLOAD'=>'Download',
			'LBL_VIEWERJS_PREV_PAGE'=>'Vorherige Seite',
			'LBL_VIEWERJS_NEXT_PAGE'=>'Nächste Seite',
			'LBL_VIEWERJS_PAGE'=>'Seite',
			'LBL_VIEWERJS_ZOOM_OUT'=>'Verkleinern',
			'LBL_VIEWERJS_ZOOM_IN'=>'zoomen',
			'LBL_VIEWERJS_ZOOM'=>'Zoom',
			'LBL_VIEWERJS_ZOOM_AUTO'=>'Automatisch',
			'LBL_VIEWERJS_ZOOM_AS'=>'Originalgröße',
			'LBL_VIEWERJS_ZOOM_AL'=>'volle Breite',
		),
		'nl_nl' => array(
			'LBL_VIEW_AS_EMAIL'=>'Tonen als Email',
			'LBL_DOWNLOAD_ALL'=>'Download alle bijlagen',
			'LBL_VIEW_DOCUMENT'=>'Bekijk Document',
			'LBL_VIEWERJS_PRESENTATION'=>'Presentatie',
			'LBL_VIEWERJS_FULLSCREEN'=>'Volledig Scherm',
			'LBL_VIEWERJS_DOWNLOAD'=>'Download',
			'LBL_VIEWERJS_PREV_PAGE'=>'Vorige Pagina',
			'LBL_VIEWERJS_NEXT_PAGE'=>'Volgende Pagina',
			'LBL_VIEWERJS_PAGE'=>'Pagina',
			'LBL_VIEWERJS_ZOOM_OUT'=>'Uitzoomen',
			'LBL_VIEWERJS_ZOOM_IN'=>'Inzoomen',
			'LBL_VIEWERJS_ZOOM'=>'Zoom',
			'LBL_VIEWERJS_ZOOM_AUTO'=>'Automatisch',
			'LBL_VIEWERJS_ZOOM_AS'=>'Ware grootte',
			'LBL_VIEWERJS_ZOOM_AL'=>'volledige breedte',
		),
		'pt_br' => array(
			'LBL_VIEW_AS_EMAIL'=>'Visualizar como Email',
			'LBL_DOWNLOAD_ALL'=>'Baixar todos os anexos',
			'LBL_VIEW_DOCUMENT'=>'Visualizar documento',
			'LBL_VIEWERJS_PRESENTATION'=>'Apresentação',
			'LBL_VIEWERJS_FULLSCREEN'=>'Tela Cheia',
			'LBL_VIEWERJS_DOWNLOAD'=>'Baixar',
			'LBL_VIEWERJS_PREV_PAGE'=>'Página Anterior',
			'LBL_VIEWERJS_NEXT_PAGE'=>'Próxima Página',
			'LBL_VIEWERJS_PAGE'=>'Página',
			'LBL_VIEWERJS_ZOOM_OUT'=>'Zoom Out',
			'LBL_VIEWERJS_ZOOM_IN'=>'Zoom In',
			'LBL_VIEWERJS_ZOOM'=>'Zoom',
			'LBL_VIEWERJS_ZOOM_AUTO'=>'Automático',
			'LBL_VIEWERJS_ZOOM_AS'=>'Tamanho Real',
			'LBL_VIEWERJS_ZOOM_AL'=>'Largura Total',
		),
	),
	'Documents' => array(
		'it_it' => array(
			'DOC_PREVIEW'=>'Anteprima file',
			'DOC_PREVIEW_BUTTON'=>'Anteprima',
			'DOC_NOT_SUPP'=>'File non supportato',
			'DOC_NOT_ACTIVE'=>'File non attivo',
		),
		'en_us' => array(
			'DOC_PREVIEW'=>'File preview',
			'DOC_PREVIEW_BUTTON'=>'Preview',
			'DOC_NOT_SUPP'=>'File not supported',
			'DOC_NOT_ACTIVE'=>'File not active',
		),
		'de_de' => array(
			'DOC_PREVIEW'=>'Dateivorschau',
			'DOC_PREVIEW_BUTTON'=>'Vorschau',
			'DOC_NOT_SUPP'=>'Datei nicht unterstützt',
			'DOC_NOT_ACTIVE'=>'Datei nicht aktiv',
		),
		'nl_nl' => array(
			'DOC_PREVIEW'=>'File voorvertoning',
			'DOC_PREVIEW_BUTTON'=>'Voorbeeld',
			'DOC_NOT_SUPP'=>'Bestand niet ondersteund',
			'DOC_NOT_ACTIVE'=>'Bestand niet actief',
		),
		'pt_br' => array(
			'DOC_PREVIEW'=>'Visualização de ficheiros',
			'DOC_PREVIEW_BUTTON'=>'Visualização',
			'DOC_NOT_SUPP'=>'Arquivo não suportado',
			'DOC_NOT_ACTIVE'=>'Arquivo não ativa',
		),
	),
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
?>