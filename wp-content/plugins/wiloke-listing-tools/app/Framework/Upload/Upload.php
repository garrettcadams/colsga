<?php
namespace WilokeListingTools\Framework\Upload;

use WilokeListingTools\Frontend\User;

class Upload {
	public $aData;
	public $userID;
	protected $aImageTypes = array(
		'image/gif',
		'image/jpeg',
		'image/jpg',
		'image/png'
	);
	private static $maximumFileSize;

	private static function fileSize(){
		self::$maximumFileSize = ini_get('max_file_uploads');
		self::$maximumFileSize = str_replace('M', '', self::$maximumFileSize);
		self::$maximumFileSize = absint(self::$maximumFileSize)*1024*1024;
		return self::$maximumFileSize;
	}

	/**
	 * Creating Folder
	 */
	public static function getFolderDir($folderName){
		$aUploadDir = wp_upload_dir();

		$folderDir = $aUploadDir['basedir'].'/'.$folderName;
		if ( !file_exists( $folderDir ) ) {
			wp_mkdir_p( $folderDir );
		}

		return trailingslashit($folderDir);
	}

	/**
	 * Creating User Folder
	 */
	public static function getUserUploadFolder($userID=null){
		if ( empty($userID) ){
			$userID = User::getCurrentUserID();
		}

		$oUserInfo  = get_userdata($userID);
		$aUploadDir = wp_upload_dir();

		$userDirname = $aUploadDir['basedir'].'/'.$oUserInfo->user_login;
		if ( !file_exists( $userDirname ) ) {
			wp_mkdir_p( $userDirname );
		}

		return $userDirname;
	}

	private function insertAttachment($file, $fileType, $uploadTo){
		$fileName = basename($file);

		$aAttachment = array(
			'post_mime_type' => $fileType,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $fileName ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => User::getCurrentUserID(),
			'guid'           => $uploadTo
		);
		$attachID  = wp_insert_attachment($aAttachment, $file);
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attachID, $file );
		wp_update_attachment_metadata( $attachID, $attach_data );

		return $attachID;
	}

	public function uploadFakeFile(){
		if ( $this->aData['aFile']['size'] > self::fileSize() ){
			return sprintf(esc_html__('You can not upload a file bigger than %s', 'wiloke-listing-tools' ), ini_get('max_file_uploads'));
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$uploadOverride = array('test_form' => false);
		$aMoveFile = wp_handle_upload( $this->aData['aFile'], $uploadOverride );

		if ( $aMoveFile && !isset( $aMoveFile['error'] ) ) {
			return $this->insertAttachment($aMoveFile['file'], $aMoveFile['type'], $aMoveFile['url']);
		}else{
			return $aMoveFile['error'];
		}
	}

	public static function deleteImg($id, $userID=null){
		if ( empty($userID) ){
			$userID = User::getCurrentUserID();
		}

		if ( $userID != get_post_field('post_author', $id) ){
			return false;
		}

		wp_delete_post($id, true);
	}

	public function image() {
		if ( !function_exists('WP_Filesystem') ){
			require_once  ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();
		global $wp_filesystem;
		$aUploadDir = wp_upload_dir();
		$fileName = $this->aData['fileName'];

		//HANDLE UPLOADED FILE
		if ( ! function_exists( 'wp_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$img = $this->aData['imageData'];
		$img = str_replace('data:'.$this->aData['fileType'].';base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$decoded   = base64_decode($img) ;
		$filename  = $this->aData['fileName'];

		if ( !isset($this->aData['uploadTo']) ){
			$uploadTo = trailingslashit($aUploadDir['basedir']);
		}else{
			$uploadTo = trailingslashit($this->aData['uploadTo']);
		}
		$uploadTo  = trailingslashit(str_replace( '/', DIRECTORY_SEPARATOR, $uploadTo ));

		// @new
		if( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$wp_filesystem->put_contents(
			$uploadTo . $filename,
			$decoded,
			FS_CHMOD_FILE // predefined mode settings for WP files
		);

		// Without that I'm getting a debug error!?
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}

		// @new
		$aFile             = array();
		$aFile['error']    = '';
		$aFile['tmp_name'] = $uploadTo . $fileName;
		$aFile['name']     = $fileName;
		$aFile['type']     = $this->aData['fileType'];
		$aFile['size']     = filesize( $uploadTo . $fileName );

		if ( $aFile['size'] > self::fileSize() ){
			return false;
		}

		$aFileReturn = wp_handle_sideload(
			$aFile,
			array(
				'test_form' => false
			)
		);

		if ( !in_array($aFileReturn['type'], $this->aImageTypes) ){
			wp_delete_file($uploadTo . $fileName);
			return false;
		}

		return $this->insertAttachment($aFileReturn['file'], $aFileReturn['type'], $uploadTo . $fileName);
	}
}