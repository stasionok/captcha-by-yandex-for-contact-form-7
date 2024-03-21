<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CFYC_Form_Tag' ) ) {
	class CFYC_Form_Tag {

		const PANEL_BUTTON_NAME = 'Yandex Captcha';

		private static CFYC_Form_Tag $instance;

		public static function get_instance(): CFYC_Form_Tag {
			if ( empty( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Show this in popup when user does not configure plugin and tries to use it
		 *
		 * @return void
		 */
		public function cfyc_tag_generator_misconfigured(): void {
			$url = menu_page_url( 'wpcf7-integration', false );
			$url = add_query_arg( array(
				'service' => 'cfyc',
				'action'  => 'setup'
			), $url );
			/* translators: %s: url */
			$confText = sprintf( __( 'Please configure the plugin first. Go to <a href="%s">configuration</a>', 'captcha-by-yandex-for-contact-form-7' ), $url );

			?>
            <div class="alert-box">
				<?php echo wp_kses( $confText, array( 'a' => array( 'href' => true ) ) ) ?>
            </div>
			<?php
		}

		/**
		 * Backend: Add insert Yandex Captcha button (WP Admin > Contact Forms 7 > select one > see buttons at the top)
		 */
		public function cfyc_add_form_template_tag(): void {
			$service = CFYC_Service::get_instance();

			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add(
				CFYC_Common::TAG_NAME,
				self::PANEL_BUTTON_NAME,
				$service->is_active() ? array( $this, 'cfyc_tag_generator_configured' ) : array( $this, 'cfyc_tag_generator_misconfigured' )
			);
		}

		function cfyc_tag_generator_configured( $contact_form, $args = '' ): void {
			$args = wp_parse_args( $args, array() );
			?>
            <div class="control-box">
                <fieldset>
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>"/></td>
                        </tr>

                        <tr>
                            <th scope="row">
								<?php echo esc_html( __( 'Test mode', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
										<?php echo esc_html( __( 'Test mode', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                    </legend>
                                    <label for="<?php echo esc_attr( $args['content'] . '-test' ); ?>">
                                        <input type="checkbox" name="test" class="option" id="<?php echo esc_attr( $args['content'] . '-test' ); ?>"/>
										<?php echo esc_html( __( 'Enable test mode', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
								<?php echo esc_html( __( 'Invisible captcha', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
										<?php echo esc_html( __( 'Invisible captcha', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                    </legend>
                                    <label for="<?php echo esc_attr( $args['content'] . '-invisible' ); ?>">
                                        <input type="checkbox" name="invisible" class="option" id="<?php echo esc_attr( $args['content'] . '-invisible' ); ?>"/>
										<?php echo esc_html( __( 'Use invisible captcha', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr( $args['content'] . '-shieldPosition' ); ?>">
									<?php echo esc_html( __( 'Data processing block', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                </label>
                            </th>
                            <td>
                                <label for="<?php echo esc_attr( $args['content'] . '-hideShield' ); ?>">
                                    <input type="checkbox" name="hideShield" class="option" id="<?php echo esc_attr( $args['content'] . '-hideShield' ); ?>"/>
									<?php echo esc_html( __( 'Hide data processing block', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                </label>
                                <br/>
                                <i><?php echo esc_html( __( 'It is your responsibility to notify users that their data is being processed by SmartCaptcha. If you hide the notification block, let users know in some other way that SmartCaptcha is processing their data.', 'captcha-by-yandex-for-contact-form-7' ) ); ?></i>
                                <br/>
                                <br/>

                                <fieldset style="border: 1px solid;padding: 1em;">
                                    <legend>
										<?php echo esc_html( __( 'Position of Data processing block:', 'captcha-by-yandex-for-contact-form-7' ) ); ?>
                                    </legend>
                                    <div><label><input type="radio" class="option" name="shieldPosition" value="top-left"/>top-left</label></div>
                                    <div><label><input type="radio" class="option" name="shieldPosition" value="center-left"/>center-left</label></div>
                                    <div><label><input type="radio" class="option" name="shieldPosition" value="bottom-left"/>bottom-left</label></div>
                                    <div><label><input type="radio" class="option" name="shieldPosition" value="top-right"/>top-right</label></div>
                                    <div><label><input type="radio" class="option" name="shieldPosition" value="center-right" checked/>center-right</label></div>
                                    <div><label><input type="radio" class="option" name="shieldPosition" value="bottom-right"/>bottom-right</label></div>
                                </fieldset>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>

            <div class="insert-box">
                <input type="text" name="<?php echo esc_attr(CFYC_Common::TAG_NAME) ?>" class="tag code" readonly="readonly" onfocus="this.select()"/>

                <div class="submitbox">
                    <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'captcha-by-yandex-for-contact-form-7' ) ); ?>"/>
                </div>

                <br class="clear"/>
            </div>
			<?php
		}

		/**
		 * Automatically make insert captcha tag without an extra dialog in popup
		 *
		 * @return void
		 */
		public function cfyc_tag_generator_configured_autoclick(): void {
			?>
            <script>jQuery(function ($) {
                    $("a:contains('<?php echo esc_attr(self::PANEL_BUTTON_NAME) ?>')").click(function () {
                        wpcf7.taggen.insert("[<?php echo esc_attr(CFYC_Common::TAG_NAME) ?>]");
                    });
                    $(document).on("DOMNodeInserted", function () {
                        if ($("#TB_window .insert-box-cfyc").length) {
                            tb_remove()
                        }
                    })
                })</script>
            <div class="insert-box insert-box-cfyc">
                <input type="text" name="<?php echo esc_attr(CFYC_Common::TAG_NAME) ?>" class="tag code" readonly="readonly" value="<?php echo esc_attr( '[' . CFYC_Common::TAG_NAME . ']' ) ?>"/>
                <div class="submitbox">
                    <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ) ?>"/>
                </div>
            </div>
			<?php
		}
	}
}
