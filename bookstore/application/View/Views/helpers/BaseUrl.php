<?php

class Zend_View_Helper_BaseUrl extends Zend_View_Helper_Abstract {
	/**
	 * Returns site's base url, or file with base url prepended
	 *
	 * $file is appended to the base url for simplicity
	 *
	 * @param  string|null $file
	 * @return string
	 */
	public function baseUrl($file = null, $secure = null) {
		// Get baseUrl
		// Get baseUrl
		
		$baseUrl = '/'.BASEURL;
		// Remove trailing slashes
		if (null !== $file) {
			$file = ltrim ( $file, '/\\' );
		}
		
		if ($secure) {
			return $baseUrl . '/' . $file;
		} else {
			return  $baseUrl . '/' . $file;
		}
	}
}