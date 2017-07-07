<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
@unlink('PEAR_Morphsuit.php');
@unlink('Smarty/templates/HomePage.tpl');
?>