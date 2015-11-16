<?php

error_reporting(E_ALL ^ E_DEPRECATED);

$connect = mysql_connect('localhost', 'thomas', 'gevutema28')  or die ('erreur de connexion');

mysql_select_db('projet', $connect) or die ('erreur de connexion base');

?>
