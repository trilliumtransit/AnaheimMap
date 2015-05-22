<?php
// save map edits from Anaheim map editor
echo 'hello';
$json = file_get_contents('php://input');

file_put_contents("savedMapEdits.json", $json, LOCK_EX);


?>