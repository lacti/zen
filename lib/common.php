<?php
function isWindows() {
	if (strpos ($_SERVER['HTTP_USER_AGENT'], "Windows") !== false) {
		return true;
	}
	return false;
}

function errorBack ($message) {

}

function error ($message, $backURL = null) {
	if (!$backURL) {
		$backURL = isset ($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']? $_SERVER['HTTP_REFERER']: HOME_URL;
	}
	require "page/index.error.php";
}

function _404 () {
	echo "없다.";
}

function redirectBack () {
	if ($_SERVER['HTTP_REFERER']) {
		header ("Location: " . $_SERVER['HTTP_REFERER']);
	} else {
		goHome ();
	}
	exit (0);
}

function goHome () {
	header ("Location: " . HOME_URL);
	exit (0);
}

function byteToString ($bytes) {
	if ($bytes == 0)
		return "0B";
	$s = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	$e = floor (log ($bytes) / log (1024));

	return sprintf('%.2f '.$s[$e], ($bytes / pow (1024, floor ($e))));
}

// FROM ZEROBOARD XE
/**
 * @brief 주어진 문자를 주어진 크기로 자르고 잘라졌을 경우 주어진 꼬리를 담
 * @param string 자를 원 문자열
 * @param cut_size 주어진 원 문자열을 자를 크기
 * @param tail 잘라졌을 경우 문자열의 제일 뒤에 붙을 꼬리
 * @return string
 **/
function cut_str($string,$cut_size=0,$tail = '...') {
	if($cut_size<1 || !$string) return $string;

	$chars = Array(12, 4, 3, 5, 7, 7, 11, 8, 4, 5, 5, 6, 6, 4, 6, 4, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 4, 4, 8, 6, 8, 6, 10, 8, 8, 9, 8, 8, 7, 9, 8, 3, 6, 7, 7, 11, 8, 9, 8, 9, 8, 8, 7, 8, 8, 10, 8, 8, 8, 6, 11, 6, 6, 6, 4, 7, 7, 7, 7, 7, 3, 7, 7, 3, 3, 6, 3, 9, 7, 7, 7, 7, 4, 7, 3, 7, 6, 10, 6, 6, 7, 6, 6, 6, 9);
	$max_width = $cut_size*$chars[0]/2;
	$char_width = 0;

	$string_length = strlen($string);
	$char_count = 0;

	$idx = 0;
	while($idx < $string_length && $char_count < $cut_size && $char_width <= $max_width) {
		$c = ord(substr($string, $idx,1));
		$char_count++;
		if($c<128) {
			$char_width += $c<32? 8: (int)$chars[$c-32];
			$idx++;
		}
		else if (191<$c && $c < 224) {
				  $char_width += $chars[4];
				  $idx += 2;
			}
		else {
			$char_width += $chars[0];
			$idx += 3;
		}
	}
	$output = substr($string,0,$idx);
	if(strlen($output)<$string_length) $output .= $tail;
	return $output;
}

function startsWith($haystack, $needle) {
	return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}

?>
