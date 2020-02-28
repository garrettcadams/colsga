<?php

class Jvbpd_BP_Replace_Content {

	public $id = null;
	public $type = null;
	public $object = null;
	public $template = null;

	public $defaults = Array();

	public function __construct( $id=0, string $template='', string $type='members', string $taxonomy='' ) {
        $this->id = $id;
		$this->type = $type;
		$this->object = $this->type == 'groups' ? groups_get_group(Array('group_id'=>$this->id)):false;
		$this->template = $template;
	}

	public function avatar() { return $this->type == 'members' ? bp_get_member_avatar() : bp_get_group_avatar(); }
	public function name() { return $this->type == 'members' ? bp_get_member_name() : bp_get_group_name(); }
	public function action_btn() { return do_action( 'bp_directory_members_actions' ); }
	public function member_registered() { return bp_get_member_registered(); }
	public function last_update_summary() {
		$update_contents = bp_get_member_latest_update();
		return $update_contents ? $update_contents : esc_html__("No latest updates", 'jvfrmtd');
	}
	public function friend_count() { return bp_get_member_total_friend_count(); }
	public function group_item() { return do_action( 'bp_directory_groups_item' ); }
	public function group_actions() { return do_action( 'DD' ); }
	public function bp_group_member_count() { return bp_get_group_member_count(); }
	public function bp_group_type() { return bp_get_group_type(); }
	public function group_description() { return bp_get_group_description_excerpt(); }
	public function last_active() {
		$active_contents = $this->type == 'members' ? bp_get_member_last_active() : bp_get_group_last_active();
		return $active_contents ? $active_contents : esc_html__("No latest actives", 'jvfrmtd');
	 }
	public function group_join() { return bp_get_group_join_button(); }
	public function add_friend() { return bp_get_add_friend_button($this->id); }

    public function bp_coverimage_url($attr) {
		$attrs = explode('|', $attr);
        $cover_image_url = bp_attachments_get_attachment('url', array(
            'object_dir' => $this->type,
            'item_id' => $this->id,
		));
		if( !$cover_image_url ) {
			$cover_image_url = $attrs[1];
		}
        return $cover_image_url;
    }

    public function render() {
		preg_match_all( '/{(.*?)}/', $this->template, $findReplace );
		if( !empty( $findReplace[1] ) ) {
			foreach( $findReplace[1] as $replaceCallback ) {
				$replaceParam = false;
				$selector = sprintf( '{%1$s}', $replaceCallback );
				if( -1 < strpos( $replaceCallback, ':' ) ) {
					$splitReplace = explode( ':', $replaceCallback, 2 );
					$replaceCallback = $splitReplace[0];
					$replaceParam = $splitReplace[1];
				}
				if( method_exists( $this, $replaceCallback ) ) {
					$output = call_user_func( Array( $this, $replaceCallback ), $replaceParam );
					$this->template = str_replace( $selector, $output, $this->template );
				}
			}
		}
		return $this->template;
	}

}