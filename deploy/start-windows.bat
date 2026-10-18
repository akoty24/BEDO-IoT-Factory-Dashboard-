@echo off
rem BEDO IoT Factory dashboard - double-click to start (needs PHP 8.2+)
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0deploy\start-windows.ps1"
if errorlevel 1 pause
