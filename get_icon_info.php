<?php
// this generates a csv of icon image info to be used on the map.
$iconFiles = scandir("landmark_icons");
$first = true;
echo "id,filename,width,height\n";
foreach($iconFiles as &$iconFile) {
	if(strlen($iconFile) > 2) {
		if(!$first) echo "\n";
		$first = false;
		$img_size = getimagesize ("landmark_icons/".$iconFile);
		echo explode('.', $iconFile)[0].','.$iconFile.','.$img_size[0].','.$img_size[1];	
	}
}

?>