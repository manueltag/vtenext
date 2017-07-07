<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is crmvillage.biz.
* Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
* All Rights Reserved.
********************************************************************************/
//crmv@33465
if(!function_exists('easter_date')) {
	function easter_date ($Year){
		$G = $Year % 19;
		$C = (int)($Year / 100);
		$H = (int)($C - (int)($C / 4) - (int)((8*$C+13) / 25) + 19*$G +  15) % 30;
		$I = (int)$H - (int)($H / 28)*(1 - (int)($H / 28)*(int)(29 / ($H +1))*((int)(21 - $G) / 11));
		$J = ($Year + (int)($Year/4) + $I + 2 - $C + (int)($C/4)) % 7;
		$L = $I - $J;
		$m = 3 + (int)(($L + 40) / 44);
		$d = $L + 28 - 31 * ((int)($m / 4));
		$y = $Year;
		$E = mktime(0,0,0, $m, $d, $y);
		return $E;
	}
}
//crmv@33465e
$sla_config['HelpDesk']=Array( //modulo al quale applicare lo SLA
	'time_measure'=>'seconds',
	'status_field'=>'ticketstatus', //campo stato del modulo
	'status_idle_value'=>Array( //stati del modulo per i quali il conteggio dello SLA è in "pausa"
		'Wait For Response',
	),
	'status_close_value'=>Array( //stati del modulo per i quali considerare chiuso il ticket (si calcola il tempo effettivamente trascorso in base alla data e ora chiusura effettiva -> si può utilizzare il conditional per forzare la compilazione dei suddetti campi!)
		'Closed',
	),
	'auto_set_closing_datetime'=>true, // inserimento automatico data e ora chiusura una volta messo in stato chiuso
	'hours'=>Array( //orario giornaliero nel quale effettuare il conteggio
		0=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //domenica
		1=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //lunedi
		2=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //martedi
		3=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //mercoledi
		4=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //giovedi
		5=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //venerdi
		6=>Array(Array("8:00","12:00"),Array("15:00","19:00")), //sabato
	),
	'jump_days'=>Array( // giorni della settimana da saltare nel conteggio (0 = domenica 1= lunedi......6 = sabato)
		0,
	), 
	'holidays'=>Array( //giorni nell'anno da saltare (in formato dd-mm)
		'01-01', //capodanno
		'06-01', //epifania
		date("d-m",easter_date(date('Y'))), //pasqua ////crmv@33465
		date("d-m",strtotime("+ 1 day",easter_date(date('Y')))), //pasquetta ////crmv@33465
		'25-04', //liberazione
		'01-05', //festa del lavoro
		'02-06', //repubblica
		'15-08', //assunzione
		'01-11', //ognissanti
		'08-12', //immacolata concezione
		'25-12', //natale
		'26-12', //santo stefano
	),
	'force_days'=>Array( //giorni nell'anno da contare nonostante siano da saltare, oppure quelli con una finestra temporale diversa dal normale (in formato dd-mm => finestre temporali, come nell'array hours!)
		//esempio "12-12"=>Array(Array("8:00","12:00"),Array("15:00","19:00")) //crmv@46872
	), 
	'fields'=>Array( //campi calcolati
		'time_elapsed',
		'time_remaining',
		'start_sla',
		'end_sla',
		'time_refresh',
		'sla_time',
		'due_date',
		'due_time',
		'time_change_status',
		'time_elapsed_change_status',
		'reset_sla',
		'ended_sla',
		'time_elapsed_idle',
	),
);
?>