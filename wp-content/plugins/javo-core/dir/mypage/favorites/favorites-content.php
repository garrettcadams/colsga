<!-- Starting Content -->
<?php
$output = null;
$arrFavorites = null;
if( function_exists('lv_directory_favorite') ){
    $arrFavorites = lv_directory_favorite()->core->getFavorites( bp_loggedin_user_id() );
}
if( !empty( $arrFavorites ) ) {
    $intFavoriteCount = 0;
    foreach( $arrFavorites as $arrFavoriteMeta ) {
        if( 5 < $intFavoriteCount ){
            break;
        }
        $intFavoriteCount++;
        $objPost = isset( $arrFavoriteMeta[ 'post_id' ] ) ? get_post( $arrFavoriteMeta[ 'post_id' ] ) : null;
        if( empty( $objPost ) ) {
            continue;
        }
        $strImage = jvbpd_tso()->get( 'no_image', JVBPD_IMG_DIR.'/no-image.png' );
        if( has_post_thumbnail( $objPost ) ) {
            $intFeaturedID = get_post_thumbnail_id( $objPost->ID );
            $strImage = wp_get_attachment_thumb_url( $intFeaturedID );
        }
        $arrFeaturedCategory = wp_get_object_terms( $objPost->ID, 'listing_category', array( 'fields' => 'names' ) );
        $strDate = date( get_option( 'date_format' ), strtotime( $arrFavoriteMeta[ 'save_day' ] ) );
        $objButton = new lvDirectoryFavorite_button(
            Array(
                'format' => '{text}',
                'post_id'	=> $objPost->ID,
                'unsave'	=> __( "Remove", 'jvfrmtd' ),
                'dashboard'	=> true
            )
        );
        $strButton = $objButton->output( false );
        $output .= sprintf(
        '<li class="list-group-item">
            <div class="listing-thumb">
                <a href="%1$s" target="_blank"><img src="%5$s" class="rounded-circle" alt="%2$s"></a>
            </div>
            <div class="listing-content">
                <h5 class="title">
                    <a href="%1$s" target="_blank">%2$s</a>
                </h5>
                <span class="author"><a href="%1$s"><i class="jvbpd-icon2-user2" aria-hidden="true"></i> javo	</a>	</span>
                <span class="meta-taxonomies"><i class="jvbpd-icon2-bookmark"></i>%6$s</span>
                <span class="time date"><i class="jvbpd-icon2-calendar" aria-hidden="true"></i> %3$s</span>
            </div><!-- listing-content -->
            <div class="listing-action btn-box">
                <div class="lava-action text-right">%4$s</div><!-- lava-action -->
            </div><!-- listing-action -->
        </li>',
            get_permalink( $objPost ), $objPost->post_title, $strDate, $strButton, $strImage,
            join( ', ', $arrFeaturedCategory )
        );
    }
}else{
    $output = sprintf( '<p>%s</p>', esc_html__( "Not found any data", 'jvfrmtd' ) );
}
echo $output;