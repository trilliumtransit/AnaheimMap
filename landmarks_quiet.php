<?php

// $fp = fopen("https://docs.google.com/spreadsheet/pub?key=1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ&single=true&gid=238862006&output=csv","r");
// $string = fread($fp, filesize("https://docs.google.com/spreadsheet/pub?key=1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ&single=true&gid=238862006&output=csv"));
// echo $string;

$landmarks = file_get_contents('https://docs.google.com/spreadsheet/pub?key=1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ&single=true&gid=238862006&output=csv');


?>

