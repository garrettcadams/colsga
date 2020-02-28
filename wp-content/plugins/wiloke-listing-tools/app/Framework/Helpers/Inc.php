<?php
namespace WilokeListingTools\Framework\Helpers;


class Inc{
	private static $file;
	private static $aFileParsed;
	private static $fullDir;

	private static function parseFile(){
		self::$aFileParsed = explode(':', self::$file);
		self::$fullDir = WILOKE_LISTING_TOOL_DIR . 'views/'.self::$aFileParsed[0].'/'.self::$aFileParsed[1] . '.php';
	}

	public static function file($file){
		self::$file = trim($file);
		self::parseFile();

		try{
			if ( file_exists(self::$fullDir) ){
				include self::$fullDir;
			}
		}catch (\Exception $e){
			echo sprintf(esc_html__('The file %s does not exists', 'wiloke-listing-tools'), $file);
		}

	}
}