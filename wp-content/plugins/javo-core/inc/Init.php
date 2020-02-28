<?php
/**
 * @package JavoCore
 */
namespace Jvbpd;

final class Init
{
	/**
	 * Store all the classes inside an array
	 * @return array Full list of classes
	 */
	public static function getServices()
	{
        //echo "ccc";
		return [
			//Pages\Dashboard::class,
			Base\Enqueue::class,
			//Base\SettingsLinks::class,
			// Base\CustomPostTypeController::class,
			// Base\CustomTaxonomyController::class,
			// Base\WidgetController::class,
			// Base\GalleryController::class,
			// Base\TestimonialController::class,
			// Base\TemplateController::class,
			// Base\AuthController::class,
			// Base\MembershipController::class,
			// Base\ChatController::class,
			// Base\TicketController::class,
			// Base\TicketVueController::class,
			// Base\CommentController::class, // Vue
			// Base\TicketComment::class, // PHP
			// Base\Uploader::class, //
			//Base\RestAPI::class,
			//Base\CommentAttachment::class,
		];
	}

	/**
	 * Loop through the classes, initialize them,
	 * and call the register() method if it exists
	 * @return
	 */
	public static function registerServices()
	{

        
		foreach (self::getServices() as $class) {
			$service = self::instantiate($class);
			if (method_exists($service, 'register')) {
                $service->register();
			}
		}
	}

	/**
	 * Initialize the class
	 * @param  class $class    class from the services array
	 * @return class instance  new instance of the class
	 */
	private static function instantiate($class)
	{
		$service = new $class();

		return $service;
	}
}
