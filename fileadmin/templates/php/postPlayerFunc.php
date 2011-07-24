<?php

class user_postPlayerFunc {
	function processPlayer($content,$conf) {
		$content = str_replace( '9999', '100%', $content );
		print_r($content); die();
		return $content;
	}
}

?>