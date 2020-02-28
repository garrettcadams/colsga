<?php
abstract class Jvbpd_Module {

	const ACTION_HOOK = 'jvbpd_';

	public $hasVideo = false;

	public $strip_scodes = Array(
		'vc_row'
	);

	protected $lghTitle = 100;
	protected $lghContent = 200;
	protected $shortcode_args = Array();

	abstract protected function output();

	public function __construct( $post, $param=Array() ) {

		if( !is_object( $post ) )
			return;

		$this->setValues( $param );
		$this->setDefaultArgs();

		if( 0 === $this->lghContent )
			$this->hide_content		= true;

		$this->setPost( $post );

		$this->hasVideo = $this->check_video();

		// Post
		$this->permalink = get_permalink( $this->post_id );

		if( $this->post->post_status == 'pending' )
			$this->post->post_title = sprintf( '(%1$s) %2$s', __( 'Pending', 'jvfrmtd'), $this->post->post_title );

		$this->title = $this->get_title();
		$this->title_text = wp_trim_words( $this->post->post_title, $this->lghTitle );

		$this->excerpt = $this->getContents();

		// User
		$this->avatar = $this->hide_avatar ? false : get_avatar( $this->authorID );
		$this->author_name = $this->author->display_name;
	}

	public function setValues( $params=Array() ){

		$options = shortcode_atts(
			Array(
				'hide_title' => false,
				'hide_content' => false,
				'hide_meta' => false,
				'hide_thumbnail' => false,
				'hide_avatar' => false,
				'thumbnail_size' => false,
				'length_title' => false,
				'length_content' => false,
				'in_map_template' => false,
				'no_lazy' => false,
			),
			$params
		);

		$this->shortcode_args		= apply_filters( 'jvbpd_module_shortcode_args', Array() );
		foreach( $options as $key => $value )
			$this->$key				= $value;
	}

	public function setDefaultArgs(){

		if( $this->length_title )
			$this->lghTitle			= intVal( $this->length_title );

		if( $this->length_content || $this->length_content === 0 )
			$this->lghContent		= intVal( $this->length_content );

		if( $this->getArgs( 'hide_thumbnail' ) == 'hide' )
			$this->hide_thumbnail	= true;

		if( $this->getArgs( 'hide_avatar' ) )
			$this->hide_avatar		= true;

		if( $this->getArgs( 'thumbnail_size' ) )
			$this->thumbnail_size	= $this->getArgs( 'thumbnail_size' );

		$this->lghContent			= apply_filters( self::ACTION_HOOK . 'core_module_excerpt_length', $this->lghContent );

	}

	public function setPost( $post=null ){

		if( is_null( $post ) ){
			$post = new stdClass();
			$post->ID = 0;
			$post->post_author = 0;
		}
		$this->post = $post;
		$this->post_id = $post->ID;
		$this->authorID = $post->post_author;
		$this->author = new WP_User( $this->authorID );
		$this->date = get_the_date( get_option( 'date_format' ), $post->ID  );

	}

	public function getArgs( $key='', $default=false ){

		if( is_array( $this->shortcode_args ) ){
			if( array_key_exists( $key, $this->shortcode_args ))
				$default = $this->shortcode_args[ $key ];
		}

		return $default;
	}

	public function is_featured() {
		return get_post_meta( $this->post_id, '_featured_item', true ) == '1';
	}

	public function check_video() {
		$isValue	= false;
		if( !function_exists( 'lava_directory_video' ) )
			return $isValue;
		$isValue	= get_post_meta( $this->post_id, '_video_id', true );
		return !empty( $isValue );
	}

	public function get_featured_html() {
		if( !in_array( get_class( $this ), Array( 'module15' , 'module4'  ) ) ) {
			return;
		}
		$strFeatured		= apply_filters(
			'jvbpd_module_featured_item_string',
			'<i class="fa fa-star-o"></i>',
			get_class( $this )
		);
		return apply_filters(
			'jvbpd_module_featured_item_html',
			'<div class="label-ribbon-row">
				<div class="label-info-ribbon-row-wrapper">
					<div class="label-info-ribbon-row">
						<div class="ribbons" id="ribbon-15">
							<div class="ribbon-wrap">
								<div class="content">
									<div class="ribbon">
										<span class="ribbon-span">' . $strFeatured . '</span>
									</div>
								</div><!-- /.content -->
							</div><!-- /.ribbon-wrap -->
						</div><!-- /.ribbons -->
					</div><!-- /.label-info-ribbon -->
				</div><!-- /.ribbon-wrapper -->
			</div>',
			get_class( $this )
		);
	}

	public function get_title()
	{
		if( $this->hide_title )
			return false;

		$strOutput	= join(
			false, Array(
				apply_filters( 'jvbpd_module_title_before', '', get_class( $this ), $this ),
				$this->title_before(),
				wp_trim_words( $this->post->post_title, $this->lghTitle ),
				apply_filters( 'jvbpd_module_title_after', '', get_class( $this ), $this ),
			)
		);
		return sprintf( "<a href=\"%s\">%s</a>", $this->permalink, $strOutput );
	}

	public function title_before() {
		$strBeforeTitle			= Array();
		if( $this->hasVideo ) {
			$strBeforeTitle[]	= '<i class="fa fa-video-camera"></i>&nbsp;';
		}
		return join( false, $strBeforeTitle );
	}

	public function thumbnail( $sizeName='thumbnail', $div_holder=false, $responseive=true, $classes='' )
	{
		$strOutput				= $strAttribute = $strImgTagAppend = '';
		$arrClasses				= Array();

		if( $this->hide_thumbnail )
			return $strOutput;

		if( $this->thumbnail_size )
			$sizeName			= $this->thumbnail_size;

		if( jvbpd_tso()->get( 'lazyload' ) != 'disable' && !$this->no_lazy ) {
			// $arrClasses[]		= 'jv-lazyload';
		}


		$arrThumbnailID		= get_post_thumbnail_id( $this->post_id );
		$arrThumbnailMeta	= wp_get_attachment_image_src( $arrThumbnailID, $sizeName );
		$strNoImage			= apply_filters( 'jvbpd_no_image', jvbpd_tso()->get('no_image')!='' ?  jvbpd_tso()->get('no_image') : JVBPD_IMG_DIR.'/blank-image.png' );
		$is_not_found_image	= empty( $arrThumbnailMeta[0] );
		$returnImage		= ! $is_not_found_image ? $arrThumbnailMeta[0] : $strNoImage;
		$returnImage = apply_filters( 'jvbpd_module_thumbnail_src', $returnImage, $this );
		$thumbnailAlt		= $this->title_text;

		if( is_array( $sizeName ) && !empty( $sizeName[0] ) && is_numeric( $sizeName[0] ) )
			$strImgTagAppend		= " width=\"{$sizeName[0]}\" ";

		/*
		if( !empty( $arrThumbnailMeta[1] ) )
			$strImgTagAppend	.= " width=\"{$arrThumbnailMeta[1]}\" ";

		if( !empty( $arrThumbnailMeta[2] ) )
			$strImgTagAppend	.= " height=\"{$arrThumbnailMeta[2]}\" ";
		*/

		if( $this->no_lazy )
			$strAttribute			= ' data-no-lazy="true"';

		if( $is_not_found_image )
			$arrClasses[]			= 'no-image';

		if( $div_holder ) {
			$arrClasses[]			= 'javo-thb';
			$strOutput				= "<div class=\"%1\$s\" style=\"background-image:url(%2\$s);\" data-src=\"%2\$s\"{$strAttribute}></div>";
			// $strOutput				= "<div class=\"%1\$s\" data-src=\"%2\$s\"{$strAttribute} data-loader=\"ajax\" data-no-lazy=\"true\"></div>";
		}else{
			if( $responseive )
				$arrClasses[]		= 'img-responsive';
				$strOutput			= "<img class=\"%1\$s\" src=\"%2\$s\"  {$strImgTagAppend} alt=\"{$thumbnailAlt}\"{$strAttribute}>";
		}
		$arrClasses[]				= trim( $classes );

		return sprintf(
			apply_filters( 'jvbpd_' . get_class( $this ) . '_core_module_thumbnail_after', $strOutput, $this ),
			implode( ' ', $arrClasses ),
			$returnImage
		);
	}

	public function classes( $classes='' ) {
		$arrClasses			= Array( 'module' );
		$arrClasses[]		= $classes;
		$arrClasses[]		= 'javo-' . get_class( $this  );
		$arrClasses[]		= 'post-' . $this->post_id;
		$arrClasses[]		= 'status-' . $this->post->post_status;
		$arrClasses[]		= 'type-' . $this->post->post_type;

		if( $this->hide_avatar )
			$arrClasses[]	= 'hide-avatar';

		if( $this->is_featured() )
			$arrClasses[]	= 'featured';

		$strClasses	= @implode( ' ', (array) apply_filters( 'jvbpd_module_css', $arrClasses, get_class( $this ) ) );
		echo " class=\"{$strClasses}\" data-post-id=\"{$this->post_id}\" ";
	}

	public function getContents( $post=null  ){
		if( is_null( $post ) )
			$post = $this->post;

		$strExcerpt = false;
		$removeShortcodes = Array();

		if( ! $this->hide_content ) {
			$is_mb_substr = function_exists( 'mb_substr' ) && function_exists( 'jvbpd_tso' ) && jvbpd_tso()->get( 'core_module_excerpt' ) == 'mb_substr';

			// strip shortcodes on excerp
			/*
			$arrStripShortcodes = $this->addStripShortcode();
			$strExcerpt = $this->prepareStripShortcde( $post->post_content );
			$strExcerpt = strip_shortcodes( $strExcerpt );
			$this->removeStripShortcode( $arrStripShortcodes ); */
			//$strExcerpt = strip_shortcodes( $post->post_content );
			$strExcerpt = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $post->post_content);

			// for unicode languages
			if( $is_mb_substr ){
				$strExcerpt = mb_substr( $strExcerpt, 0, $this->lghContent ) . '...' ;
			}else{
				$strExcerpt = str_replace( '%', '%%', wp_trim_words( $strExcerpt, $this->lghContent, '...' ) );
				$strExcerpt = sprintf( "<span class='meta-excerpt'>%s</span>", $strExcerpt );
			}
		}
		return sprintf(
			apply_filters(
				self::ACTION_HOOK . get_class( $this ) . '_core_module_excerpt_after',
				$strExcerpt, $this
			)
		);
	}

	public function prepareStripShortcde( $content='' ){
		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches );
		if( !empty( $matches[5] ) )
			$content = join( '', $matches[5] );
		return  $content;
	}

	public function addStripShortcode(){
		global $shortcode_tags;
		$arrShortcodes = Array();
		foreach( $this->strip_scodes as $scodeTag ){
			if( !array_key_exists( $scodeTag, $shortcode_tags ) ){
				$shortcode_tags[ $scodeTag ] = false;
				$arrShortcodes[] = $scodeTag;
			}
		}
		return $arrShortcodes;
	}

	public function removeStripShortcode( $shortcodes=Array() ){
		global $shortcode_tags;
		if( !empty( $shortcodes ) ) : foreach( $shortcodes as $scodeTag ) {
			unset( $shortcodes[ $scodeTag ] );
		} endif;
	}

	public function get_meta( $key, $default_value=false ) {
		$strOutput	= get_post_meta( $this->post_id, $key, true );
		return empty( $strOutput ) ? $default_value : $strOutput;
	}

	public function m( $key, $value=false ){
		return $this->get_meta( $key, $value );
	}

	public function get_term( $taxonomy=false, $sep=', ' )
	{
		$output_terms = Array();
		if( $terms = wp_get_object_terms( $this->post_id, $taxonomy, Array( 'fields' => 'names' ) ) )
		{
			$output_terms = is_array( $terms ) ? join( $sep, $terms ) : null;
			$output_terms = trim( $output_terms );
			// $output_terms = substr( $output_terms, 0, -1 );
		}else{
			$output_terms = '';
		}
		return $output_terms;
	}

	public function c( $taxonomy=false, $default='',  $single=true, $sep=', ' )
	{
		$strTerms	= $this->get_term( $taxonomy, $sep );

		if( $single && !empty( $strTerms  ) ) {
			$strTerms	= @explode( $sep, $strTerms );
			$strTerms	= isset( $strTerms[0] ) ? $strTerms[0] : '';
		}
		return empty( $strTerms ) ? $default : $strTerms;
	}

	public function category() {
		return $this->c(
			apply_filters( 'jvbpd_' . get_class( $this ) . '_featured_tax', 'category', $this->post_id ),
			apply_filters( 'jvbpd_' . get_class( $this ) . '_featured_no_tax', __( "No Category", 'jvfrmtd' ), $this->post_id )
		);
	}

	public function addtionalInfo( $classes=null )
	{
		if( $this->hide_meta )
			return;

		$arrAdditional_args	= Array(
			'meta-date'			=> Array(
				'icon'					=> ''
				, 'value'				=> $this->date
			)
			/*, 'meta-author'		=> Array(
				'icon'					=> 'jv-icon2-user'
				, 'value'				=> $this->author_name
			)*/
		);
		$arrAdditional			= apply_filters( 'jvbpd_' . get_class( $this ) . '_additional_meta', $arrAdditional_args, $this );

		// Output
		$arrOutput			=$arrClass = Array();
		$arrClass[]			= 'module-meta';
		$arrClass[]			= 'list-inline';

		if( !empty( $classes ) )
			$arrClass[]		= $classes;

		$arrOutput[]		= sprintf( "<ul class=\"%s\">", implode( ' ', $arrClass ) );
		if( !empty( $arrAdditional ) ) foreach( $arrAdditional as $key => $meta )
			$arrOutput[]	= "<li class=\"{$key}\"><i class=\"{$meta['icon']}\"></i> {$meta['value']}</li>";
		$arrOutput[]		= "</ul>";

		if( function_exists( 'lava_favorite_button' ) ) :
			$arrOutput[] = lava_favorite_button(
				Array(
					'post_id' => $this->post_id,
					'save' => "<i class='fa fa-heart'></i>",
					'unsave' => "<i class='fa fa-heart'></i>",
					'format' => "{text}",
				)
			);
		endif;
		return implode( "\n", $arrOutput );
	}

	public function moreInfo(){
		do_action( 'jvbpd_' . get_class( $this ) . '_shortcode_more_meta', $this );
	}

	public function hover_layer(){
		?>
		<div class="jv-module-thumb-inner-detail">
			<a href="<?php echo $this->permalink; ?>" class="jv-module-detail-button">
				<i class="fa fa-search"></i>
			</a>
		</div>

		<?php
		do_action( 'jvbpd_module_hover_content', get_class( $this ), $this );
	}

	public function before(){
		if( $this->is_featured() )
			echo $this->get_featured_html();
		do_action( 'jvbpd_module_html_before', get_class( $this ), $this );
	}

	public function after() {
		do_action( 'jvbpd_module_html_after', get_class( $this ), $this );
	}
}