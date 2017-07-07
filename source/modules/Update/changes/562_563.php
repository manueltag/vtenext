<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

$fields = array();
$fields[] = array('module'=>'Users','block'=>'LBL_USERLOGIN_ROLE','name'=>'notify_summary','label'=>'Notification Summary','uitype'=>'15','helpinfo'=>'LBL_NOT_SUMMARY_INFO','picklist'=>array('Never','Every week','Every 2 days','Every day','Every 4 hours','Every 2 hours','Hourly')); //crmv@33465
include('modules/SDK/examples/fieldCreate.php');
SDK::setLanguageEntries('Users', 'Notification Summary', array('it_it'=>'Resoconto notifiche','en_us'=>'Notification Summary','pt_br'=>'Resumo notificaчуo'));
SDK::setLanguageEntries('Users', 'Never', array('it_it'=>'Mai','en_us'=>'Never','pt_br'=>'Nunca'));
SDK::setLanguageEntries('Users', 'Hourly', array('it_it'=>'Ogni ora','en_us'=>'Hourly','pt_br'=>'Cada uma hora'));
SDK::setLanguageEntries('Users', 'Every 2 hours', array('it_it'=>'Ogni 2 ore','en_us'=>'Every 2 hours','pt_br'=>'Cada 2 horas'));
SDK::setLanguageEntries('Users', 'Every 4 hours', array('it_it'=>'Ogni 4 ore','en_us'=>'Every 4 hours','pt_br'=>'Cada 4 horas'));
SDK::setLanguageEntries('Users', 'Every day', array('it_it'=>'Ogni giorno','en_us'=>'Every day','pt_br'=>'Cada dia'));
SDK::setLanguageEntries('Users', 'Every 2 days', array('it_it'=>'Ogni 2 giorni','en_us'=>'Every 2 days','pt_br'=>'Cada 2 dias'));
SDK::setLanguageEntries('Users', 'Every week', array('it_it'=>'Ogni settimana','en_us'=>'Every week','pt_br'=>'Todas as semanas'));
SDK::setLanguageEntries('Users', 'LBL_NOT_SUMMARY_INFO', array('it_it'=>'Se hai scelto di essere notificato via VTE puoi ricevere un resconto via email delle notifiche non lette. Va configurato lo script ModNotifications da crontab.','en_us'=>'If you choose to be notified via VTE, you can receive email notifications of unread notifications. It should be configured ModNotifications script in crontab.','pt_br'=>'Se vocъ optar por ser notificado por VTE, vocъ pode receber notificaчѕes de email de notificaчѕes nуo lidas. Ele deve ter configurado roteiro ModNotifications no crontab.'));
?>