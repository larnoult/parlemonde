<?php

class QuizMaster_Helper_Extension {

	public static function ajaxRegisterExtensionScriptCallbacks() {
		$callbacks = array();
		$callbacks = apply_filters( 'quizmaster_register_extension_script_callbacks', $callbacks );
		return json_encode( $callbacks );
	}

	public function extensionType() {
		return 'pro';
	}

	public static function autoload( $class, $dir ) {

		$registeredExtensions = $this->register();
		foreach( $registeredExtensions as $ext => $extSettings ) {
			if( $extSettings['type'] == 'pro' ) {

				$classPath = QUIZMASTER_PATH . '/pro/extensions/' . $ext . '/lib/' . $dir . '/' . $class . '.php';
				if (file_exists($classPath)) {
		      include_once $classPath;
		    }

			}
		}

	}

	// include main extension files
	public static function loadAll() {

		// get extension registry
		$extObj = new QuizMaster_Helper_Extension;
		$registeredExtensions = $extObj->register();

		if( empty( $registeredExtensions )) {
			return; // no registered extensions
		}

		foreach( $registeredExtensions as $ext => $extSettings ) {
			if( $extSettings['type'] == 'pro' ) {

				$classPath = QUIZMASTER_PATH . '/pro/extensions/' . $ext . '/' . $ext . '.php';
				if (file_exists($classPath)) {
		      include_once $classPath;
		    }

				// init
				$extObj = self::load( $ext );
				$extObj->init();

			}
		}

	}

	public static function load( $ext ) {

		$classPath = QUIZMASTER_PATH . '/pro/extensions/' . $ext . '/' . $ext . '.php';

		if ( file_exists( $classPath )) {

			require_once( $classPath );
			$ext = str_replace( '-', ' ', $ext );
			$ext = ucwords( $ext );
			$ext = str_replace( ' ', '', $ext );
			$extObjName = 'QuizMaster_Extension_' . $ext;
			$extObj = new $extObjName;

			if( $extObj ) {
				return $extObj;
			}

		}

		return false;

	}

	// provide onActivation hook

	public function register() {

		$registeredExtensions = array();
		return apply_filters( 'quizmaster_extension_registry', $registeredExtensions );

	}

	public static function doActivation() {

		$extObj = new QuizMaster_Helper_Extension;
		$extObj->loadAll();
		$extensions = $extObj->register();
		foreach( $extensions as $extKey => $extSettings ) {
			$ext = $extObj->load( $extKey );
			$ext->activation();
		}

	}

	public static function doDeactivation() {

		$extObj = new QuizMaster_Helper_Extension;
		$extObj->loadAll();
		$extensions = $extObj->register();
		foreach( $extensions as $extKey => $extSettings ) {
			$ext = $extObj->load( $extKey );
			$ext->deactivation();
		}

	}

	public function url() {
		return QUIZMASTER_URL . '/pro/extensions/' . $this->getKey() . '/';
	}

	public function path() {
		return QUIZMASTER_PATH . '/pro/extensions/' . $this->getKey() . '/';
	}

	// override functions
	public function activation() {}
	public function deactivation() {}
	public function init() {}


}
