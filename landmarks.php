<?php

if (isset($_GET['staging']) ) {
	$landmarks_spreadsheet = 'https://docs.google.com/spreadsheets/d/1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ/pub?gid=1548145855&single=true&output=csv';
	$time = 30;
	}
else {
	$landmarks_spreadsheet = 'https://docs.google.com/spreadsheets/d/1SYqxY4IqpW159kQQXIn63ruJgzE2t1Z3OPidKz41cIQ/pub?gid=238862006&single=true&output=csv';
	$time = 3600; // =  1 hour
}

$quiet = false;
if (isset($_GET['quiet']) ) {
	$quiet = true;
}

// borrowed from http://phillippuleo.com/articles/lightweight-caching-proxy-php/


function fetchURL() {

	global $landmarks_spreadsheet;
	global $time;
	
	$out = '';

    $url = $landmarks_spreadsheet; // Be careful with posting variables.
    $cache_file = "cache/".hash('md5', $url).".csv"; // Create a unique name for the cache file using a quick md5 hash.

    // If the file exists and was cached in the last 24 hours...
    if (file_exists($cache_file) && (filemtime($cache_file) > (time() - $time ))) {

      $file = file_get_contents($cache_file); // Get the file from the cache.
     // $out =  $file; // echo the file out to the browser.
    }

    else {

      $file = file_get_contents($url); // Fetch the file.
      
      if($file === FALSE) {
            $file = file_get_contents($cache_file); // Get the file from the cache.
			//$out = $file; // echo the file out to the browser.
      }
      
      else{
      file_put_contents($cache_file, $file, LOCK_EX); // Save it for the next requestor.
     // $out = $file; // echo the file out to the browser.
      }
    }
    
	return $file;
}
if($quiet) {
	$landmarks = fetchURL(); // Execute the function
} else {
	echo fetchURL(); // Execute the function
}
?>