<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CFYC_Frontend' ) ) {
	class CFYC_Frontend {

		private static CFYC_Frontend $instance;

		public static function get_instance(): CFYC_Frontend {
			if ( empty( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}


		/**
		 * Load captcha JS in a head
		 *
		 * @return void
		 */
		public function cfyc_head_code(): void {
			wp_enqueue_script(
				'yandex-captcha',
				'https://smartcaptcha.yandexcloud.net/captcha.js?render=onload&onload=cfycOnloadFunction',
				array(),
				'3.8.3',
				array(
					'strategy' => 'defer'
				)
			);
		}

		/**
		 * Add shortcode handler
		 *
		 * @return void
		 */
		public function cfyc_add_shortcode(): void {
			wpcf7_add_form_tag(
				array( CFYC_Common::TAG_NAME, CFYC_Common::TAG_NAME . '*' ),
				array( $this, 'cfyc_form_tag_handler' ),
				array(
					'name-attr'               => true,
					'do-not-store'            => true,
					'display-block'           => true,
					'zero-controls-container' => true,
					'not-for-mail'            => true,
					'singular'                => true,
				)
			);
		}

		/**
		 * Handle shortcode
		 *
		 * @param $tag
		 *
		 * @return string
		 */
		function cfyc_form_tag_handler( $tag ): string {
			$tag = new WPCF7_FormTag( $tag );

			$test           = $tag->has_option( 'test' ) ? 'true' : 'false';
			$invisible      = $tag->has_option( 'invisible' ) ? 'true' : 'false';
			$hideShield     = $tag->has_option( 'hideShield' ) ? 'true' : 'false';
			$shieldPosition = $tag->get_option( 'shieldPosition', '(top-left|center-left|bottom-left|top-right|center-right|bottom-right)', true );
			if ( ! $shieldPosition ) {
				$shieldPosition = 'center-right';
			}

			$service = CFYC_Service::get_instance();
			$key     = $service->get_sitekey();

			$execute = $invisible === 'true' ? 'window.smartCaptcha.execute();' : 'if (tokenField.value.length === 0) { container.classList.add("wpcf7-not-valid"); }';
			$content = <<<CONTENT
<div class="smart-captcha" id="{$tag->name}"></div>
<style>
.smart-captcha.wpcf7-not-valid {
    height: 102px;
    border: 1px solid;
    padding-right: 2px;
    border-radius: 11px;
}
</style>
<script>
    function cfycOnloadFunction() {
        if (window.smartCaptcha) {
            const container = document.getElementById('{$tag->name}');
            const widgetId = window.smartCaptcha.render(container, {
                sitekey: '{$key}',
                invisible: {$invisible},
                test: {$test},
                hideShield: {$hideShield},
                shieldPosition: '{$shieldPosition}',
                callback: (token) => container.classList.remove("wpcf7-not-valid"),
            });
            const wpcf7Elm = container.closest( '.wpcf7' );
            wpcf7Elm.addEventListener( 'wpcf7submit', function() {
			   {$execute}
			}, false );
        }
    }
</script>
CONTENT;

			return $content;
		}

		/**
		 * Check if captcha solved before submitting
		 *
		 * @param $result
		 * @param $tag
		 *
		 * @return mixed
		 */
		public function cfyc_validate_fills( $result, $tag ) {
			// inform: that part verify only captcha, nonce checks by contact form 7
			$token = stripslashes( sanitize_text_field( $_POST['smart-token'] ?? '' ) );
			if ( empty( $token ) ) {
				$error = __( 'Please check captcha', 'captcha-by-yandex-for-contact-form-7' );
				$result->invalidate( $tag, $error );
			}

			return $result;
		}

		/**
		 * Verify captcha
		 *
		 * @param $result
		 * @param $tag
		 *
		 * @return mixed
		 */
		public function cfyc_validate_captcha( $result, $tag ): bool {
			$service = CFYC_Service::get_instance();

			if ( ! $service->is_active() ) {
				return false;
			}
			// inform: that part verify only captcha, nonce checks by contact form 7
			$token = stripslashes( sanitize_text_field( $_POST['smart-token'] ?? '' ) );

			if ( $service->verify( $token ) ) { // Human
				$spam = false;
			} else { // Bot
				$spam = true;
			}

			return $spam;
		}
	}
}
