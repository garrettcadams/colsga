<?php

namespace WilokeListingTools\Frontend;


use ReallySimpleJWT\TokenValidator;
use WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

class User {
	public static $aCoverImgs = array();
	public static $aAvatars = array();
	public static $aUsersSocialNetworks = array();
	public static $aUsersData = array();
	public static $aUsersPhone = array();
	public static $aUsersWebsite = array();
	public static $aUsersAddress = array();
	public static $aUsersPosition = array();
	public static $aUsersPicture = array();
	public static $aInstantMessages = array();
	public static $aCountPosts = array();
	public static $aCanSubmitListingRoles = array('contributor', 'seller', 'vendor');
	protected static $oValidation;
	protected static $oPayLoad;
	protected static $userID;
	protected static $aRoles;
	public static $firebaseIDKey = 'firebase_id';

	public static function getMyPosts($postType, $aQuery=array()){
	    $userID = self::getCurrentUserID();
		$aArgs = wp_parse_args(
			$aQuery,
           array(
	           'post_type'     => $postType,
	           's'             => '',
	           'posts_per_page'=> 20,
	           'post_status'   => array('publish', 'pending'),
	           'author'        => $userID
           )
        );

		if ( empty($aQuery['s']) ){
			unset($aArgs['s']);
		}

		$aRoles = self::getField('roles', $userID);

		if ( is_array($aRoles) && in_array('administrator', $aRoles) ){
			unset($aArgs['author']);
		}

		$query = new \WP_Query($aArgs);

		$aOptions = array();
		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aOptions[] = General::buildSelect2OptionForm($query->post);
			}
		}

		return $aOptions;
    }

	public static function getLastSentMessage($to){
		return get_transient('last_message_at_'.$to.'_'.User::getCurrentUserID());
    }

	public static function setLastSentMessage($to){
		set_transient('last_message_at_'.User::getCurrentUserID().'_'.$to, 'yes', 300);
	}

	public static function getvalidation(){
	    return self::$oValidation;
    }

	public static function getFirebaseID($userID=null){
		$userID = empty($userID) ? User::getCurrentUserID() : $userID;
		return GetSettings::getUserMeta($userID, self::$firebaseIDKey);
	}

    public static function getFirebaseUserID(){
	    return GetSettings::getUserMeta(self::getCurrentUserID(), 'firebase_id');
    }

	protected static function getPayLoad(){
		if ( self::$oValidation ){
			$rawPayLoad = self::$oValidation->getPayload();
			if ( empty($rawPayLoad) ){
				self::$oPayLoad = false;
			}else{
				self::$oPayLoad = json_decode($rawPayLoad, true);
			}
		}else{
			self::$oPayLoad = false;
		}
	}

	private static function getAuthorizationHeader(){
		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		}
		else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} elseif (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			//print_r($requestHeaders);
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		return $headers;
	}

	private static function getBearerToken() {
		$headers = self::getAuthorizationHeader();
		// HEADER: Get the access token from the header
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}

	public static function getUserID(){
		self::$userID = abs(self::$oPayLoad['userID']);
		return self::$userID;
	}

	public static function getRoles(){
		self::$aRoles = self::getField('roles', self::$userID);
		return self::$aRoles;
	}

	public static function isSubmissionRole($userID){
		$aRoles = self::getField('roles', $userID);
		foreach ($aRoles as $role){
		    if ( in_array($role, apply_filters('wilcity/wiloke-listing-tools/filter/wiloke-submission-roles', array('contributor', 'vendor', 'seller'))) ){
		        return true;
            }
        }
        return false;
    }

    public static function currentUserCan($capability){
	    $userID = self::getCurrentUserID();
	    $oUser  = new \WP_User($userID);
	    $args = array_slice( func_get_args(), 1 );

	    $args = array_merge( array( $capability ), $args );
	    return call_user_func_array( array( $oUser, 'has_cap' ), $args );
    }

	public static function isUserLoggedIn($isApp=false){
	    if ( !$isApp ){
	        return is_user_logged_in();
        }

		self::$oValidation = new TokenValidator();
		$token = self::getBearerToken();

		if ( empty($token) ){
			return false;
		}

		try{
			self::$oValidation->splitToken($token)
			                 ->validateExpiration()
			                 ->validateSignature(General::getSecurityAuthKey());
			self::getPayLoad();
			return self::getUserID();
		}catch (\Exception $exception){
			self::$oValidation = false;
			return false;
		}
    }

	public static function getCurrentUserID($isApp=false){
	    $userID = get_current_user_id();
		if ( !empty($userID) ){
			return $userID;
		}

		return self::isUserLoggedIn(true);
	}

	public static function canAddProduct($userID=''){
	    if ( empty($userID) ){
	        $userID = get_current_user_id();
        }

        $oUser = new \WP_User($userID);
	    if ( in_array('seller', $oUser->roles) || in_array('administrator', $oUser->roles) ){
	        return true;
        }
        return false;
    }

	public static function isPostAuthor($post, $isAcceptAdmin=false){
		if ( !is_user_logged_in() ){
			return false;
		}

		if ( $isAcceptAdmin && current_user_can('administrator') ){
			return true;
		}

		return get_current_user_id() == $post->post_author;
	}

	public static function getSuperAdmins(){
		$query = new \WP_User_Query( array(
			'role'          => 'administrator',
			'orderby'       => 'ID',
			'total_users'   => 1
		) );
		$aResults = $query->get_results();
		return $aResults;
    }

	public static function getFirstSuperAdmin(){
        $oMaybeAdmin = get_user_by('email', get_option('admin_email'));
        if (!empty($oMaybeAdmin) && !is_wp_error($oMaybeAdmin)) {
            if (in_array('administrator', $oMaybeAdmin->roles)) {
                return apply_filters('wilcity/wiloke-listing-tools/getFirstSuperAdmin/', $oMaybeAdmin);
            }
        }

        $aArgs = apply_filters('wilcity/wiloke-listing-tools/get-first-super-admin', array(
            'role'          => 'administrator',
            'orderby'       => 'ID',
            'total_users'   => 1
        ));

		$query = new \WP_User_Query($aArgs);
		$aResults = $query->get_results();
        $oUser = end($aResults);
        return apply_filters('wilcity/wiloke-listing-tools/getFirstSuperAdmin/', $oUser);
    }

    public static function dokanGetUserIDByWithDrawID($withdrawID){
	    global $wpdb;
	    $dbName = $wpdb->prefix . 'dokan_withdraw';
	    return $wpdb->get_var($wpdb->prepare(
		    "SELECT user_id FROM {$dbName} WHERE id=%d",
		    $withdrawID
	    ));
    }

	public static function isAccountConfirmed($userID=null){
		if ( !\WilokeThemeOptions::isEnable('toggle_confirmation') ){
			return true;
		}
		$userID = empty($userID) ? self::getCurrentUserID() : $userID;
		return GetSettings::getUserMeta($userID, 'confirmed');
	}

	public static function canSubmitListing($userID='', $isFocusConfirm=true){
        if ( !self::isAccountConfirmed($userID) && $isFocusConfirm ){
            return false;
        }

	    $userID = empty($userID) ? self::getCurrentUserID() : $userID;
		if ( empty($userID) ){
			return false;
		}

	    $oUser = new \WP_User($userID);
		$aRoles = $oUser->roles;

		if ( in_array('administrator', $aRoles) ){
			return true;
		}

		$aAvailableRoles = apply_filters('wiloke/submission/roles', self::$aCanSubmitListingRoles);

		foreach ($aRoles as $role){
			if ( in_array($role, $aAvailableRoles) ){
				return true;
			}
		}

		return false;
	}

	public static function countPostsByPostStatus($postStatus, $postType){
		if ( !post_type_exists( $postType ) ){
			return 0;
		}

		global $wpdb;
        $authorID = get_current_user_id();
		$cache_key = $authorID . $postType . $postStatus;
		$counts = wp_cache_get( $cache_key, 'counts' );
		if ( false !==  $counts ){
            return $counts;
        }

		$counts = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT( $wpdb->posts.ID ) FROM {$wpdb->posts} WHERE post_type = %s AND post_author=%d AND post_status=%s",
				$postType, $authorID, $postStatus
            )
        );

		wp_cache_set( $cache_key, $counts, 'counts' );
		return $counts;
	}

	public static function countUserPosts($userID, $type, $perm=''){
		global $wpdb;

		$aPostStatus = wilokeListingToolsRepository()->get('posttypes:post_statuses');
		$aPostStatusKey = array_keys($aPostStatus);
		$aPostStatusKey[] = 'publish';
		$aPostStatusKey[] = 'pending';
//		$aPostStatusKey[] = 'draft';

		if ( !post_type_exists( $type ) ){
			$aResults = array();
			foreach ($aPostStatusKey as $postStatus){
				$aResults[$postStatus] = 0;
			}
			$aResults['total'] = 0;
			return $aResults;
		}

		$cache_key = _count_posts_cache_key( $type, $perm );
		$cache_key = $userID . $cache_key;

		$counts = wp_cache_get( $cache_key, 'counts' );
		if ( false !== $counts ) {
			/** This filter is documented in wp-includes/post.php */
			return apply_filters( 'wilcity_count_user_posts', $counts, $type, $perm );
		}

		$aPostStatusKey[] = 'pending';

		$query = "SELECT post_status, COUNT( ID ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author=%d AND post_status IN('".implode("','", $aPostStatusKey)."')";

		$query .= ' GROUP BY post_status';

		$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type, $userID ), ARRAY_A );
		$counts = array_fill_keys( $aPostStatusKey, 0 );

		$total = 0;
		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = $row['num_posts'];
			$total +=  abs($row['num_posts']);
		}

		$counts['total'] = $total;

		wp_cache_set( $cache_key, $counts, 'counts' );

		/**
		 * Modify returned post counts by status for the current post type.
		 *
		 * @since 3.7.0
		 *
		 * @param object $counts An object containing the current post_type's post
		 *                       counts by status.
		 * @param string $type   Post type.
		 * @param string $perm   The permission to determine if the posts are 'readable'
		 *                       by the current user.
		 */
		return apply_filters( 'wilcity_count_user_posts', $counts, $type, $userID, $perm );
	}

	public static function countUserPostsByPostTypes($userID, $isOtherPostTypeExceptEvent=false){
		$total = 0;
		if ( $isOtherPostTypeExceptEvent ){
			$aPostTypeKeys = General::getPostTypeKeys(false, $isOtherPostTypeExceptEvent);
			foreach ($aPostTypeKeys as $posType){
				$aTotals = self::countUserPosts($userID, $posType);
				if ( !empty($aTotals) ){
					$total += $aTotals['total'];
				}
			}

		}else{
			$aTotals = self::countUserPosts($userID, 'event');
			$total += $aTotals['total'];
		}

		return $total;
	}

	public static function userIDExists($userID){
		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $userID));
		return $count == 1;
	}

	public static function getAvatar($userID=null, $size = 'thumbnail') {
        remove_action( 'get_avatar_url', 'dokan_get_avatar_url', 99 );

		$userID = empty($userID) ? get_current_user_id() : $userID;

		if ( isset(self::$aAvatars[$userID]) ){
			return self::$aAvatars[$userID];
		}

		$avatar = '';
		$avatar_id = GetSettings::getUserMeta($userID, 'avatar_id');

		if ( empty($avatar_id) ){

			$avatar = GetSettings::getUserMeta($userID, 'avatar');

			if( empty( $avatar) ) {

				if ( wp_doing_ajax() ){
					$aOptions = \Wiloke::getThemeOptions();
					$aAvatar = $aOptions['user_avatar'];
				} else {
					global $wiloke;
					$aAvatar = $wiloke->aThemeOptions['user_avatar'];
				}

				if ( !empty($aAvatar) ){
					$avatar = $aAvatar['url'];
				}
			}
			
		} else {
			$size = apply_filters('wiloke-listing-tools/user/avatar-size', $size);
			$image_attributes = wp_get_attachment_image_src($avatar_id, $size);

			if($image_attributes) {
				$avatar = $image_attributes[0];
			}
		}

		if ( !empty($avatar) ){
			self::$aAvatars[$userID] = $avatar;
		}else{
			self::$aAvatars[$userID] = get_avatar_url($userID);
		}

		$avatar = apply_filters('wiloke-listing-tools/user/avatar-url', $avatar, $userID, $size);

		return  $avatar;
	}

	public static function getCoverImage($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aCoverImgs[$userID]) ){
			return self::$aCoverImgs[$userID];
		}

		$coverImg = GetSettings::getUserMeta($userID, 'cover_image');
		if ( empty($coverImg) ){
			if ( wp_doing_ajax() ){
				$aOptions = \Wiloke::getThemeOptions();
				$aCoverImg = $aOptions['cover_image'];
			}else{
				global $wiloke;
				$aCoverImg = $wiloke->aThemeOptions['cover_image'];
			}

			if ( !empty($aCoverImg) ){
				$coverImg = $aCoverImg['url'];
			}
		}
		self::$aCoverImgs[$userID] = $coverImg;
		return self::$aCoverImgs[$userID];
	}

	public static function getPosition($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersPosition[$userID]) ){
			return self::$aUsersPosition[$userID];
		}
		self::$aUsersPosition[$userID] = GetSettings::getUserMeta($userID, 'position');
		return self::$aUsersPosition[$userID];
	}

	public static function getPicture($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersPicture[$userID]) ){
			return self::$aUsersPicture[$userID];
		}
		self::$aUsersPicture[$userID] = GetSettings::getUserMeta($userID, 'picture');
		return self::$aUsersPicture[$userID];
	}

	public static function getInstantMessage($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aInstantMessages[$userID]) ){
			return self::$aInstantMessages[$userID];
		}
		self::$aInstantMessages[$userID] = GetSettings::getUserMeta($userID, 'instant_message');
		self::$aInstantMessages[$userID] = empty(self::$aInstantMessages[$userID]) ? esc_html__('Hi, thanks for your message. We are not here at the moment, but we\'ll get back to you soon!', 'wiloke-listing-tools') : self::$aInstantMessages[$userID];
		return self::$aInstantMessages[$userID];
	}

	public static function getAddress($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersAddress[$userID]) ){
			return self::$aUsersAddress[$userID];
		}
		self::$aUsersAddress[$userID] = GetSettings::getUserMeta($userID, 'address');
		return self::$aUsersAddress[$userID];
	}

	public static function getPhone($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersPhone[$userID]) ){
			return self::$aUsersPhone[$userID];
		}
		self::$aUsersPhone[$userID] = GetSettings::getUserMeta($userID, 'phone');
		return self::$aUsersPhone[$userID];
	}

	public static function getWebsite($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersWebsite[$userID]) ){
			return self::$aUsersWebsite[$userID];
		}

		$oUserData = get_userdata( $userID);
		self::$aUsersWebsite[$userID]  = isset($oUserData->user_url) ? $oUserData->user_url : '';
		return self::$aUsersWebsite[$userID];
	}

	public static function getSocialNetworks($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersSocialNetworks[$userID]) ){
			return self::$aUsersSocialNetworks[$userID];
		}

		$aSocialNetworks = GetSettings::getUserMeta($userID, 'social_networks');

		if ( empty($aSocialNetworks) ){
			self::$aUsersSocialNetworks[$userID] = array();
			return self::$aUsersSocialNetworks[$userID];
        }

		foreach ($aSocialNetworks as $key => $val){
		    if ( empty($val) ){
		        unset($aSocialNetworks[$key]);
            }
        }

        self::$aUsersSocialNetworks[$userID] = $aSocialNetworks;
		return self::$aUsersSocialNetworks[$userID];
	}

	public static function getUserData($userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		if ( isset(self::$aUsersData[$userID]) ){
			return self::$aUsersData[$userID];
		}

		self::$aUsersData[$userID] = get_userdata($userID);
		return self::$aUsersData[$userID];
	}

	public static function getUserActivateKey($userID){
	    global $wpdb;
	    return $wpdb->get_var(
            $wpdb->prepare(
                    "SELECT user_activation_key FROM $wpdb->users WHERE ID=%d",
                $userID
            )
        );
    }

	public static function getField($field, $userID=null){
	    if ( $field == 'user_activation_key' ){
	        return self::getUserActivateKey($userID);
        }

		$userID = empty($userID) ? get_current_user_id() : $userID;
		$oUserData = self::getUserData($userID);
		if ( empty($oUserData) ){
			return '';
		}

		return isset($oUserData->{$field}) ? $oUserData->{$field} : '';
	}

	public static function can($permission){
		if ( !is_user_logged_in() ){
			return false;
		}

		return current_user_can($permission);
	}

	public static function url($userID){
		return get_author_posts_url($userID);
	}

	public static function renderFollower($authorID){
		if ( !FollowController::toggleFollow() ){
			return '';
		}

		$followers  = FollowController::countFollowers($authorID);
		$nFollowers = empty($followers) || $followers == 1 ? 1 : $followers;
		?>
		<span id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-follower-number-'.$authorID)); ?>" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-follower')); ?> author-hero_rightText__1Yfm7"><span class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-print-number')); ?> color-primary"><?php echo esc_html(General::convertToNiceNumber($followers)); ?></span> <?php echo _n('Follower', 'Followers', $nFollowers, 'wiloke-listing-tools'); ?></span>
		<?php
	}

	public static function renderFollowing($authorID){
		if ( !FollowController::toggleFollow() ){
			return '';
		}
		$followings = FollowController::countFollowings($authorID);
		?>
		<span class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-following')); ?> author-hero_rightText__1Yfm7"><span class="color-primary"><?php echo esc_html(General::convertToNiceNumber($followings)); ?></span> <?php esc_html_e('Following', 'wiloke-listing-tools'); ?></span>
		<?php
	}

	public static function renderFollowStatus($authorID){
		if ( !FollowController::toggleFollow() ){
			return '';
		}

		?>
        <a class='color-primary fs-12 font-secondary font-bold' href='<?php echo esc_url(get_author_posts_url($authorID)); ?>'><i class='la la-refresh'></i> <?php echo FollowController::isIamFollowing($authorID) ?  esc_html__('Following', 'wiloke-listing-tools') : esc_html__('Follow', 'wiloke-listing-tools'); ?></a>
        <?php
    }
}
