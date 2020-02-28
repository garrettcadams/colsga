<?php
namespace WilokeListingTools\Framework\Routing;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use WilokeListingTools\Framework\Helpers\DebugStatus;

abstract class Controller {
	/*
	 * As the default, it always die if the payment parameter is wrong, but some cases, we simply return false;
	 */
	public $isNotDieIfFalse;
	protected $aMiddleware = [];

	public function middleware($aMiddleware, array $aOptions = []){
		if ( (!DebugStatus::status('WILOKE_LISTING_TOOLS_CHECK_EVEN_ADMIN') && current_user_can('administrator')) || DebugStatus::status('WILOKE_LISTING_TOOLS_PASSED_MIDDLEWARE') ){
			return true;
		}

		/*
		 * All Controller must be passed this middleware first
		 */
		do_action('wiloke-listing-tools/top-middleware');

		$msg = esc_html__('You do not have permission to access this page', 'wiloke-listing-tools');
		$aOptions['userID'] = isset($aOptions['userID']) ? $aOptions['userID'] : get_current_user_id();

		foreach ($aMiddleware as $middleware ){
			$middlewareClass = $this->getMiddleware($middleware);
			if ( class_exists($middlewareClass) ){
				$instMiddleware = new $middlewareClass;
				$status = $instMiddleware->handle($aOptions);

				if ( !$status ){
					if ( isset($aOptions['isBoolen']) ){
						return false;
					}else if ( isset($aOptions['isAjax']) || wp_doing_ajax() ){
						wp_send_json_error(
							array(
								'msg' => property_exists($instMiddleware, 'msg') ? $instMiddleware->msg : $msg
							)
						);
					}else if(isset($aOptions['isRedirect'])){
						$url = property_exists($instMiddleware, 'redirectTo') ? $instMiddleware->redirectTo : null;
						Redirector::to($url);
					}else if (isset($aOptions['isApp']) && ( $aOptions['isApp'] == 'yes' || $aOptions['isApp']) ){
						return array(
							'status' => 'error',
							'msg'    => property_exists($instMiddleware, 'msg') ? $instMiddleware->msg : $msg
						);
					}else{
						throw new AccessDeniedHttpException($msg);
					}
				}
			}else{
				if ( wp_doing_ajax() ){
					wp_send_json_error(
						array(
							'msg' => sprintf(esc_html__("Class %s does not exists", 'wiloke-listing-tools'), $middleware)
						)
					);
				}else{
					if ( isset($aOptions['isApp']) && $aOptions['isApp'] == 'yes'){
						return array(
							'status' => 'error',
							'msg'    => sprintf(esc_html__("Class %s does not exists", 'wiloke-listing-tools'), $middleware)
						);
					}else{
						throw new NotFoundHttpException;
					}
				}
			}
		}

		return true;
	}

	public function validate($aInput, $aRules){
		foreach ( $aRules as $name => $rule ){
			switch ($rule){
				case 'required':
					if ( !isset($aInput[$name]) || empty($aInput[$name]) ){
						if ( wp_doing_ajax() ){
							wp_send_json_error(
								array(
									'msg' => sprintf(esc_html__("The %s is required", 'wiloke-listing-tools'), $name)
								)
							);
						}else{
							throw new AccessDeniedHttpException(esc_html__("The %s is required", 'wiloke-listing-tools'));
						}
					}
					break;
				case 'email':
					if ( !isset($aInput[$name]) || empty($aInput[$name]) || !is_email($aInput[$name]) ){
						if ( wp_doing_ajax() ){
							wp_send_json_error(
								array(
									'msg' => sprintf(esc_html__("You provided an invalid email address", 'wiloke-listing-tools'), $name)
								)
							);
						}else{
							throw new AccessDeniedHttpException(esc_html__("You provided an invalid email address", 'wiloke-listing-tools'));
						}
					}
					break;
				default:
					do_action('wiloke-listing-tools/app/Framework/Routing/Controller/validate', $aInput, $name, $rule);
					break;
			}
		}
	}

	public function getMiddleware($middleware){
		return wilokeListingToolsRepository()->get('middleware:'.$middleware);
	}

	/**
	 * Handle Calls to missing methods on the control
	 *
	 * @param array $aParameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $aParameters ) {
		throw new \BadMethodCallException( esc_html__("Method [{{$method}}] does not exist", 'wiloke') );
	}

}