<?php
/*
Plugin Name: QuizMaster Progress Bar
Plugin URI: http://wordpress.org/extend/plugins/quizmaster-progress-bar
Description: Provides a customizable progress bar for QuizMaster quizzes
Version: 0.0.1
Author: GoldHat Group
Author URI: https://goldhat.ca
Copyright: GoldHat Group
Text Domain: quizmaster-progress-bar
*/

define( 'QUIZMASTER_PROGRESS_BAR_VERSION', '0.0.1' );
define( 'QUIZMASTER_PROGRESS_BAR_TEMPLATES_PATH', plugin_dir_path( __FILE__ ) . "templates/" );
define( 'QUIZMASTER_PROGRESS_BAR_ASSETS_PATH', plugin_dir_path( __FILE__ ) . "assets/" );
define( 'QUIZMASTER_PROGRESS_BAR_ASSETS_URL', plugin_dir_url( __FILE__ ) . "assets/" );

class QuizMaster_Progress_Bar_Extension {

	public function __construct() {
		$this->init();
	}

	public function init() {
		add_filter('quizmaster_extension_registry', array( $this, 'registerExtension' ));
		add_action( 'wp_footer', array($this, 'progressBar' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'addScripts' ));
	}

	public function addScripts() {
		wp_register_script( 'jquery-progress-bar', plugins_url('js/progress_bar.js', __FILE__), array('jquery'), QUIZMASTER_PROGRESS_BAR_VERSION, true );
		wp_enqueue_script( 'jquery-progress-bar' );

		wp_register_style( 'jquery-progress-bar-style', plugins_url('css/progress-bar.css', __FILE__), array(), QUIZMASTER_PROGRESS_BAR_VERSION );
		wp_enqueue_style( 'jquery-progress-bar-style' );
	}

	public function progressBar() {
		?>
		<script>

			// init progress bar
			window.onload = function onLoad() {
				window.quizmasterProgressBarLine = new ProgressBar.Line('.qm-progress-bar', {
					color: '#1CAFF6',
					duration: 3000,
					easing: 'easeInOut'
				});
				window.quizmasterProgressBarLine.animate(0.0);
			};

			// bind to trigger questionSolved
			(function($) {

				// attach progress bar animate
				$(document).on('quizmasterQuestionSolved', progress);

				// move the progress bar
				function progress( e ) {

					var $value = parseFloat( e.values.results.comp.answered / e.values.questionCount )
					window.quizmasterProgressBarLine.animate( $value, {
					  duration: 500
					});
				}

			})( jQuery );

		</script>
		<?php
	}

	public function registerExtension( $registeredExtensions ) {

		$registeredExtensions['progress-bar'] = array(
			'type' => 'ext',
			'name' => 'Progress Bar',
		);

	}

}

new QuizMaster_Progress_Bar_Extension();

/*

RESEARCH NOTES

https://kimmobrunfeldt.github.io/progressbar.js/#examples
http://tcavalin.github.io/stepProgress/
http://www.gerardolarios.com/plugins-and-tools/jqmeter/
https://usablica.github.io/progress.js/
https://lightningtgc.github.io/MProgress.js/
https://jqueryui.com/progressbar/#default

Progress modes
1. Question completion
2. Points earned
3. Grade progress
4. Grade pace

 */
