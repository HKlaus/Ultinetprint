<?php
/**
*
* @author   https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
* @version  1.0
* 
*/

/**
* Erstellt eine MYSQLi Verbindung mit den Verbindungsdaten aus der Datei "netprint_config.php"
* Diese MYSQLi Verbindung kann dann überall daraufhin mit einem include verwendet werden 
* MYSQLi ist eine verbesserte (das i steht für „Improved“) Erweiterung von PHP (vgl. https://de.wikipedia.org/wiki/MySQLi)
*
*/
include_once 'netprint_config.php';   
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
