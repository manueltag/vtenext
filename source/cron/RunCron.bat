@echo off
set ROOTDIR=%~dp0
set VTECRM_ROOTDIR=%~dp0..
set PHP_EXE="php.exe"
cd /D %VTECRM_ROOTDIR%
%PHP_EXE% -f RunCron.php
cd /D %ROOTDIR%
