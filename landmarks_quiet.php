<?php

// $fp = fopen("https://docs.google.com/spreadsheet/pub?key=1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ&single=true&gid=238862006&output=csv","r");
// $string = fread($fp, filesize("https://docs.google.com/spreadsheet/pub?key=1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ&single=true&gid=238862006&output=csv"));
// echo $string;
$landmarks_spreadsheet;
if (isset($_GET['staging'])) {
	$landmarks_spreadsheet = 'https://docs.google.com/spreadsheets/d/1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ/pub?gid=1548145855&single=true&output=csv';
	}
else {
	$landmarks_spreadsheet = 'https://docs.google.com/spreadsheets/d/1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ/pub?gid=238862006&single=true&output=csv';
}

$landmarks = file_get_contents($landmarks_spreadsheet);


?>