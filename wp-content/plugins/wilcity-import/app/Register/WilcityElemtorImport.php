<?php

namespace WILCITY_IMPORT\Register;


use Elementor\Core\Settings\Page\Model;
use Elementor\TemplateLibrary\Source_Local;

class WilcityElemtorImport extends Source_Local {

	public function run($file_name){
		return $this->import($file_name);
	}

	private function createPage($file_name){
		$aParse = explode('/', $file_name);
		$fileName = end($aParse);
		$fileName = str_replace(array('.json', '-'), array('', ' '), $fileName);
		$fileName = ucfirst($fileName);

		$pageID = wp_insert_post(
			array(
				'post_title' => $fileName,
				'post_type'  => 'page',
				'post_status'=> 'publish'
			)
		);

		update_post_meta($pageID,'_wp_page_template', 'templates/page-builder.php');

		return $pageID;
	}

	private function isTemplateExist($data){
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title=%s AND post_type=%s",
				$data['title'],
				parent::CPT
			)
		);
	}

	private function import($file_name){
		$data = json_decode( file_get_contents( $file_name ), true );

		if ( empty( $data ) ) {
			return new \WP_Error( 'file_error', 'Invalid File.' );
		}

		$content = $data['content'];

		if ( ! is_array( $content ) ) {
			return new \WP_Error( 'file_error', 'Invalid File.' );
		}

		if ( !$template_id = $this->isTemplateExist($data) ){
			$content = $this->process_export_import_content( $content, 'on_import' );

			$page_settings = [];

			if ( ! empty( $data['page_settings'] ) ) {
				$page = new Model( [
					'id' => 0,
					'settings' => $data['page_settings'],
				] );

				$page_settings_data = $this->process_element_export_import_content( $page, 'on_import' );

				if ( ! empty( $page_settings_data['settings'] ) ) {
					$page_settings = $page_settings_data['settings'];
				}
			}

			$template_id = $this->save_item( [
				'content'       => $content,
				'title'         => $data['title'],
				'type'          => $data['type'],
				'page_settings' => $page_settings,
			] );

			if ( is_wp_error( $template_id ) ) {
				return false;
			}
		}

		$pageID = $this->createPage($file_name);

		if ( isset($data['content']) ){
			update_post_meta($pageID, '_elementor_data',$data['content']);
		}
		update_post_meta($pageID, '_elementor_edit_mode', 'builder');
		update_post_meta($pageID, '_elementor_version', $data['version']);

		return true;
	}
}