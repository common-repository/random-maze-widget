<?php
header('Content-Type: image/gif');

function idie() {
		$im = imagecreatetruecolor(1, 1);
		imagegif($im);
		imagedestroy($im);
}

$size = $_GET['size'];
if(strlen($size)<3) 
	idie();

$pattern = '/([0-9]{1,2})x([0-9]{1,2})x([0-9]{1,2})/i';
if(preg_match($pattern, $size, $matches)) {
	$x = intval($matches[1]);
	$y = intval($matches[2]);
	$tileSize = intval($matches[3]);
	
	if($x>8 && $y>8 && $x<51 && $y<51 && $tileSize>1 && $tileSize<16) {
		$width = intval($x);
		$height = intval($y);
		$size = $width * $height;
		$maze = array_fill(0, $size, 0);
		$counter = 0;
		$current = 0;
		$visited = array_fill(0, $size, false);
		$visited[0] = true;
		$stack = array();
		while($counter<$size) {
			$choices = array();
			if($current%$width>0 and !$visited[$current-1])
				$choices[] = -1;
			if($current%$width<$width-1 and !$visited[$current+1])
				$choices[] = 1;
			if($current>=$width and !$visited[$current-$width])
				$choices[] = -$width;
			if($current<$size-$width and !$visited[$current+$width])
				$choices[] = $width;
			if(!empty($choices)) {
				$choice = $choices[array_rand($choices)];
				$stack[] = $current;
				switch($choice) {
					case -1:
						$maze[$current] |= 1;
						$maze[$current+$choice] |= 4;
					break;
					case 1:
						$maze[$current] |= 4;
						$maze[$current+$choice] |= 1;
					break;
					case -$width:
						$maze[$current] |= 2;
						$maze[$current+$choice] |= 8;
					break;
					case $width:
						$maze[$current] |= 8;
						$maze[$current+$choice] |= 2;
					break;
				}
				$current += $choice;
				$visited[$current] = true;
				$counter++;
			} else if(!empty($stack)) {
				$current = array_pop($stack);
			} else {
				$current = rand(0, $size-1);
				$visited[$current] = true;
				$counter++;
			}
		}
		$viewWidth = $width * $tileSize;

		$viewHeight = $height * $tileSize;

		$im = imagecreatetruecolor(1+$viewWidth, 1+$viewHeight);
		$random = imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255));
		$black = imagecolorallocate($im, 0, 0, 0);
		$red = imagecolorallocate($im, 102, 0, 0);
		$green = imagecolorallocate($im, 0, 102, 0);

		imagefill($im, 0, 0, $random);

		imagerectangle($im, 0, 0, $viewWidth, $viewHeight, $black);

		for($i=0;$i<$size;$i++) {
			$current = $maze[$i];
			$x = ($i % $width)*$tileSize;
			$y = floor($i/$width)*$tileSize;
			if(($current & 1) === 0)
				imageline($im, $x, $y, $x, $y+$tileSize, $black); 
			if(($current & 2) === 0)
				imageline($im, $x, $y, $x+$tileSize, $y, $black);
		}
 
		imagefilledrectangle($im, 1, $viewHeight-$tileSize, $tileSize, $viewHeight-1, $green);
		imagefilledrectangle($im, $viewWidth-$tileSize, 1, $viewWidth-1, $tileSize, $red);


		imagegif($im);
		imagedestroy($im);
	} else {
		idie();
	}
}