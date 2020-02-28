<?php

namespace WILCITY_APP\Controllers;


trait ParsePost {
	function parse_raw_http_request($input)
	{
		// grab multipart boundary from content type header
		preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
		$boundary = $matches[1];

		// split content by boundary and get rid of last -- element
		$a_blocks = preg_split("/-+$boundary/", $input);
		array_pop($a_blocks);

		// loop data blocks
		foreach ($a_blocks as $id => $block)
		{
			if (empty($block))
				continue;

			// you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

			// parse uploaded files
			if (strpos($block, 'application/octet-stream') !== FALSE)
			{
				// match "name", then everything after "stream" (optional) except for prepending newlines
				preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
			}
			// parse all other fields
			else
			{
				// match "name" and optional value in between newline sequences
				preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
			}
			$a_data[$matches[1]] = $matches[2];
		}
		return $a_data;
	}

	private function parsePutData($rawData){
		if ( empty($rawData) ){
			return '';
		}

		if ( isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] !== 'multipart/form-data' ){
			parse_str($rawData, $aDataSimpleParse);
			return $aDataSimpleParse;
		}

		$boundary = substr($rawData, 0, strpos($rawData, "\r\n"));
		$aParts = array_slice(explode($boundary, $rawData), 1);
		$aData = array();
		foreach ($aParts as $part) {
			// If this is the last part, break
			if ($part == "--\r\n") break;

			// Separate content from headers
			$part = ltrim($part, "\r\n");
			list($rawHeaders, $body) = explode("\r\n\r\n", $part, 2);

			// Parse the headers list
			$aRawHeaders = explode("\r\n", $rawHeaders);
			$aHeaders = array();
			foreach ($aRawHeaders as $header) {
				list($name, $value) = explode(':', $header);
				$aHeaders[strtolower($name)] = ltrim($value, ' ');
			}
			// Parse the Content-Disposition to get the field name, etc.
			if (isset($aHeaders['content-disposition'])) {
				$filename = null;
				preg_match(
					'/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
					$aHeaders['content-disposition'],
					$aMatches
				);

				if ( isset($aMatches[2]) ){
					$name = $aMatches[2];
				}

				if ( isset($name) ){
					$aData[$name] = is_array($body) ? $body : substr($body, 0, strlen($body) - 2);
				}
			}
		}

		if ( isset($aData['content-type']) ){
			unset($aData['content-type']);
		}

		return $aData;
	}

	public function parsePost(){
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
		$rawData = file_get_contents('php://input');

		switch ($method){
			case 'DELETE':
				parse_str($rawData, $aData);
				break;
			case 'PUT':
				return $this->parsePutData($rawData);
				break;
			default:
				if ( !empty($rawData) ){
					$aData = json_decode($rawData, true);
				}else{
					$aData = $_POST;
				}
				break;
		}
		return $aData;
	}
}
