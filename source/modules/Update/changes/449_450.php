<?php
if (chmod('smartoptimizer/cache', 0777)) {
	rename('htaccess.txt','.htaccess');
}
?>