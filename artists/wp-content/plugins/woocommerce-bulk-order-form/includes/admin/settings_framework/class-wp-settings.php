<?php
/**
 * Class for registering settings and sections and for display of the settings form(s).
 * For detailed instructions see: https://github.com/keesiemeijer/WP-Settings
 *
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/WordPress/Settings
 * @since 3.0
 * @version 2.0
 * @author keesiemeijer
 */
if ( ! class_exists( 'WooCommerce_Bulk_Order_Form_WP_Settings' ) ) {
	class WooCommerce_Bulk_Order_Form_WP_Settings {

		/**
		 * Version of WP_Settings_Settings class
		 *
		 * @since 2.0
		 * @var
		 */
		public $version = 2.0;

		/**
		 * Current settings page.
		 *
		 * @since 2.0
		 * @var array
		 */
		public $current_page = array();

		/**
		 * Debug errors and notices.
		 *
		 * @since 2.0
		 * @var string
		 */
		public $debug = '';

		/**
		 * Admin pages.
		 *
		 * @since 2.0
		 * @var array
		 */
		private $pages = array();

		/**
		 * Admin pages.
		 *
		 * @since 2.0
		 * @var array
		 */
		private $fields;

		/**
		 * Unique plugin admin page hook suffix.
		 *
		 * @since 2.0
		 * @var array
		 */
		private $page_hook;

		/**
		 * Fields that need Javascript. (e.g. colorpicker)
		 *
		 * @since 2.0
		 * @var array
		 */
		private $script_types;

		/**
		 * Fields that need the label_argument in add_settings_field()
		 *
		 * @since 2.0
		 * @var array
		 */
		private $label_for = array( 'text', 'select', 'textarea' );

		/**
		 * array of Javascrips needed for the current settings page
		 *
		 * @since 2.0
		 * @var array
		 */
		private $load_scripts = array();

		/**
		 * Multiple forms on one settings page.
		 *
		 * @since 2.0
		 * @var bool
		 */
		private $multiple_forms = false;

		/**
		 * valid admin pages and fields arrays.
		 *
		 * @since 2.0
		 * @var bool
		 */
		private $valid_pages = false;


		/**
		 * Registers settings using the WorPres settings Api.
		 *
		 * @uses WP_Settings_Settings_Fields class
		 * @since 2.0   *
		 *
		 * @param array  $pages Array with admin pages.
		 * @param string $page_hook Unique plugin admin page hook suffix.
		 */
		public function init( $pages, $page_hook = '' ) {

			$this->pages     = (array) $pages;
			$this->page_hook = trim( sanitize_title( (string) $page_hook ) );

			// Debug strings don't use Gettext functions for translation.

			if ( ! class_exists( 'WooCommerce_Bulk_Order_Form_Settings_WP_Fields' ) ) {
				$this->debug .= "Error: class WooCommerce_Bulk_Order_Form_Settings_WP_Fields doesn't exist<br/>";
			}

			if ( '' === $this->page_hook ) {
				$this->debug .= "Error: parameter 'page_hook' not provided in settings_admin_init()<br/>";
			}

			// Let external scripts do debugging .
			$this->debug .= apply_filters( "{$this->page_hook}_debug", $this->debug, $this->pages );

			if ( $this->debug ) {
				return $this->valid_pages = false; // Don't display the form and navigation.
			}

			// Passed validation (required to show form and navigation).
			$this->valid_pages = true;

			$this->current_page = $this->get_current_admin_page();

			if ( isset( $this->current_page['multiform'] ) && $this->current_page['multiform'] ) {
				$this->multiple_forms = ( count( $this->current_page['sections'] ) > 1 ) ? true : false;
			}

			// Instanciate the form fields.
			$this->fields = new WooCommerce_Bulk_Order_Form_Settings_WP_Fields( get_settings_errors() );

			// Array of fields that needs the 'label_for' parameter (add_settings_field()).
			$this->label_for = apply_filters( "{$this->page_hook}_label_for", $this->label_for );

			// Array of fields that needs javascript (e.g. 'color_picker').
			$this->script_types = apply_filters( "{$this->page_hook}_script_field_types", array() );

			$this->add_settings_sections();

			// Register all the settings.
			$this->register_settings();

			// only load javascript if it's needed for the current admin page
			if ( ! empty( $this->load_scripts ) ) {
				$this->load_scripts = array_unique( $this->load_scripts );
				add_action( 'admin_print_scripts-' . $this->page_hook, array( $this, 'enqueue_scripts' ) );
			}

		} // admin_init()


		/**
		 * Adds setting sections
		 *
		 * @since 2.0
		 * @return void
		 */
		private function add_settings_sections() {

			foreach ( $this->current_page['sections'] as $section ) {

				$section_description = '__return_false';

				if ( isset( $section['desc'] ) && $section['desc'] ) {
					$section_description = array( $this, 'render_section_description' );
				}

				$title = ( isset( $section['title'] ) ) ? $section['title'] : ''; // optional

				// Add page hook to sections and page ids.
				$page_id    = ( $this->multiple_forms ) ? $section['id'] : $this->current_page['id'];
				$page_id    = $this->page_hook . '' . $page_id;
				$section_id = $this->page_hook . '' . $section['id'];

				// Add database option(s) to debug messages.
				$this->debug .= ( '' === $this->debug ) ? 'Database option(s) created for this page:<br/>' : '';
				$this->debug .= "database option: " . $section_id . '<br/>'; // database option name

				add_settings_section( $section_id, $title, $section_description, $page_id );

				if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {

					// Add fields to sections.
					$this->add_settings_fields( $section_id, $section['fields'], $page_id );
				}
			}
		}


		/**
		 * Adds all fields to a settings section.
		 *
		 * @since 2.0
		 *
		 * @param string $sections_id ID of section to add fields to.
		 * @param array  $fields Array with section fields
		 * @param string $page_id Page id.
		 * @param bool   $use_defaults Use default values for the settings fields.
		 */
		private function add_settings_fields( $sections_id, $fields, $page_id ) {

			$opt_defaults = array();
			$defaults     = array(
				'section' => $sections_id,
				'id'      => '',
				'type'    => '',
				'label'   => '',
				'desc'    => '',
				'size'    => false,
				'options' => '',
				'default' => '',
				'content' => '',
				'attr'    => false,
				'before'  => '',
				'after'   => '',
				'_type'   => '',
			);

			// Check if database option exist (use defaults if it doesn't).
			$use_defaults = ( false === get_option( $sections_id ) ) ? true : false;

			foreach ( $fields as $field ) {

				// Field (rows) can be added by external scripts.
				$multiple = ( isset( $field['fields'] ) && $field['fields'] ) ? true : false;
				$options  = ( $multiple ) ? (array) $field['fields'] : array( $field );

				foreach ( $options as $key => $opt ) {

					$args = wp_parse_args( $opt, $defaults );

					$args['default']            = ( $use_defaults ) ? $args['default'] : '';
					$opt_defaults[ $opt['id'] ] = $args['default'];

					if ( in_array( $args['type'], $this->script_types ) ) {
						$this->load_scripts[] = $args['type']; // field needs javascript
					}

					if ( in_array( $args['type'], $this->label_for ) ) {
						$args['label_for'] = $sections_id . '' . $args['id'];
					}

					if ( $multiple ) {
						$field['fields'][ $key ] = $args;
					}
				}

				if ( $multiple ) {
					$args = $field;
				}

				// ability to add fields with an action hook
				if ( ! method_exists( $this->fields, 'callback_' . $field['type'] ) ) {
					$args['callback']  = $field['type'];
					$args['page_hook'] = $this->page_hook;
					$field['type']     = 'extra_field';
				}

				if ( method_exists( $this->fields, 'callback_' . $field['type'] ) ) {
					add_settings_field( $sections_id . '[' . $field['id'] . ']', isset( $args['label'] ) ? $args['label'] : '', array(
							$this->fields,
							'callback_' . $field['type'],
						), $page_id, $sections_id, $args );
				}
			}

			// add the option or validation errors show twice on the first submit (todo: Why?).
			if ( $use_defaults ) {
				add_option( $sections_id, $opt_defaults );
			}
		}


		/**
		 * Registers settings
		 *
		 * @since 2.0
		 * @return void
		 */
		private function register_settings() {
			foreach ( $this->pages as $page ) {
				foreach ( $page['sections'] as $section ) {

					// Use section ids for multiple forms.
					if ( isset( $page['multiform'] ) && $page['multiform'] ) {
						$page['id'] = ( count( $page['sections'] ) > 1 ) ? $section['id'] : $page['id'];
					}

					$page_id     = $this->page_hook . '' . $page['id'];
					$sections_id = $this->page_hook . '' . $section['id'];

					if ( isset( $section['validate_callback'] ) && $section['validate_callback'] ) {
						register_setting( $page_id, $sections_id, $section['validate_callback'] );
					} else {
						register_setting( $page_id, $sections_id );
					}
				}
			}
		}


		/**
		 * Gets all settings from all sections
		 *
		 * @since 2.0
		 * @return array Array with settings.
		 */
		public function get_settings( $section = '' ) {
			$settings = array();

			if ( ! empty( $section ) ) {
				return get_option( $this->page_hook . '' . $section );
			}

			foreach ( (array) $this->pages as $page ) {
				if ( ! isset( $page['sections'] ) ) {
					continue;
				}

				foreach ( $page['sections'] as $section ) {
					if ( ! isset( $section['id'] ) ) {
						continue;
					}

					$option = get_option( $this->page_hook . '' . $section['id'] );
					if ( $option ) {
						unset( $option['section_id'] );
						$settings[ $section['id'] ] = $option;
					}
				}
			}

			return $settings;
		}


		/**
		 * Returns the current settings page.
		 *
		 * @since 2.0
		 *
		 * @param array $admin_pages . Array of settings pages.
		 *
		 * @return array   Current settings page.
		 */
		public function get_current_admin_page() {

			foreach ( (array) $this->pages as $page ) {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					if ( ( $_GET['tab'] === $page['id'] ) || ( $_GET['tab'] === $page['slug'] ) )
						$current_page = $page;
				}
			}

			// Set the first settings page as current if it's not a tab.
			if ( empty( $current_page ) ) {
				$current_page = $this->pages[0];
			}

			return $current_page;
		}


		/**
		 * Adds a admin page.
		 *
		 * @since 2.0
		 *
		 * @param array $page Page array.
		 *
		 * @return array Admin pages array with the page added.
		 */
		public function add_page( $page ) {
			return $this->pages[] = $page;
		}


		/**
		 * Adds multiple admin pages.
		 *
		 * @since 2.0
		 *
		 * @param array $pages Array with pages.
		 *
		 * @return array Admin pages array with the pages added.
		 */
		public function add_pages( $pages ) {
			foreach ( $pages as $page ) {
				$this->add_page( $page );
			}
			return $this->pages;
		}


		/**
		 * Adds a section to an admin page.
		 *
		 * @since 2.0
		 *
		 * @param string $page Page id.
		 * @param array  $section Section array.
		 *
		 * @return array Admin pages array with the section added.
		 */
		public function add_section( $page, $section ) {

			foreach ( $this->pages as $key => $_page ) {

				if ( $page !== $_page['id'] ) {
					continue;
				}

				if ( isset( $this->pages[ $key ][ $page ]['sections'] ) ) {
					$this->pages[ $key ]['sections'] = array();
				}

				$this->pages[ $key ]['sections'][] = $section;
			}

			return $this->pages;
		}


		/**
		 * Adds multiple sections to an admin page.
		 *
		 * @since 2.0
		 *
		 * @param array $string Page id
		 * @param array $sections Array with sections.
		 *
		 * @return array Admin pages array with the sections added.
		 */
		public function add_sections( $page, $sections ) {
			foreach ( $sections as $section ) {
				$this->pages = $this->add_section( $page, $section );
			}
			return $this->pages;
		}


		/**
		 * Adds a form field to a section.
		 *
		 * @since 2.0
		 *
		 * @param string $page Page id.
		 * @param string $section Section id.
		 * @param array  $field Field array.
		 *
		 * @return array Admin pages array with the field added.
		 */
		public function add_field( $page, $section, $field ) {

			foreach ( $this->pages as $key => $_page ) {

				if ( $page !== $_page['id'] ) {
					continue;
				}

				if ( ! isset( $this->pages[ $key ]['sections'] ) ) {
					continue;
				}

				$_sections = $this->pages[ $key ]['sections'];

				foreach ( $_sections as $_key => $_section ) {

					if ( $section !== $_section['id'] ) {
						continue;
					}

					if ( ! isset( $this->pages[ $key ]['sections'][ $_key ]['fields'] ) ) {
						$this->pages[ $key ]['sections'][ $_key ]['fields'] = array();
					}

					$this->pages[ $key ]['sections'][ $_key ]['fields'][] = $field;
				}
			}

			return $this->pages;
		}


		/**
		 * Adds multiple form fields to a section.
		 *
		 * @since 2.0
		 *
		 * @param string $page Page id.
		 * @param string $section Section id.
		 * @param array  $fields Array with fields.
		 *
		 * @return array Admin pages array with the fields added.
		 */
		public function add_fields( $page, $section, $fields ) {
			foreach ( $fields as $field ) {
				$this->pages = $this->add_field( $page, $section, $field );
			}
			return $this->pages;
		}


		/**
		 * Enqueue javascript for fields that need them.
		 *
		 * @since 2.0
		 * @return void
		 */
		public function enqueue_scripts() {
			$screen = get_current_screen();

			// Only load scripts on plugin pages.
			if ( $screen->id === $this->page_hook ) {
				do_action( "{$this->page_hook}_scripts", $this->load_scripts );
			}
		}


		/**
		 * Display the description of a section.
		 *
		 * @since 2.0
		 *
		 * @param array $section Description of section.
		 *
		 * @return void
		 */
		public function render_section_description( $section ) {
			foreach ( $this->current_page['sections'] as $setting ) {
				if ( $this->page_hook . '' . $setting['id'] === $section['id'] )
					echo $setting['desc'];
			}
		}


		/**
		 * Display Plugin Title and if needed tabbed navigation.
		 *
		 * @since 2.0
		 *
		 * @param string $plugin_title Plugin title.
		 * @param string $tab_id Page id. Manually set the active tab.
		 *
		 * @return void
		 */
		public function render_header( $plugin_title = '', $tab_id = false ) {

			if ( ! empty( $plugin_title ) )
				echo get_screen_icon() . '<h2>' . (string) $plugin_title . '</h2>';

			// if ( !$this->valid_pages )
			//  return;

			$page_title_count = 0;

			foreach ( $this->pages as $page ) {
				if ( isset( $page['title'] ) && $page['title'] )
					++$page_title_count;
			}

			$html       = '';
			$current    = $this->current_page;
			$page_ids   = wp_list_pluck( $this->pages, 'id' );
			$cur_tab_id = ( $tab_id ) ? (string) $tab_id : $current['id'];
			$cur_tab_id = ( in_array( $cur_tab_id, $page_ids ) ) ? $cur_tab_id : $current['id'];
			$i          = 0;

			foreach ( $this->pages as $page ) {

				if ( ( isset( $page['title'] ) && $page['title'] ) ) {
					if ( $page_title_count > 1 ) {
						$html .= ( 0 === $i ) ? '<h2 class="nav-tab-wrapper">' : '';

						$active = '';
						if ( $cur_tab_id === $page['id'] )
							$active = ' nav-tab-active';

						// Get the url of the current settings page.
						$tab_url = remove_query_arg( array( 'tab', 'settings-updated' ) );

						// Add query arg 'tab' if it's not the first settings page.
						if ( $this->pages[0]['id'] !== $page['id'] )
							$tab_url = add_query_arg( 'tab', $page['slug'], $tab_url );

						$html .= sprintf( '<a href="%1$s" class="nav-tab%2$s" id="%3$s-tab">%4$s</a>', esc_url( $tab_url ), $active, esc_attr( $page['id'] ), $page['title'] );

						$html .= ( ++$i === $page_title_count ) ? '</h3>' : '';
					}

					if ( $page_title_count === 1 ) {
						if ( isset( $current['title'] ) && $current['title'] === $page['title'] ) {
							if ( ! empty( $page['title'] ) )
								$html .= '<h3>' . $page['title'] . '</h3>';
							break;
						}
					}

				}
			}

			echo $html;
		} // render_header()


		/**
		 * Displays the form(s) and sections.
		 *
		 * @since 2.0
		 * @return void
		 */
		public function render_form() {

			if ( ! $this->valid_pages ) {
				return;
			}

			$page = $this->current_page;

			if ( ! empty( $page ) ) {

				$ids   = wp_list_pluck( $page['sections'], 'id' );
				$forms = ( $this->multiple_forms ) ? $page['sections'] : array( $page );

				// Section ids hidden inputs.
				$section_ids = '';
				foreach ( $ids as $id ) {
					$section_ids .= "<input id='{$this->page_hook}_{$id}_section_id' type='hidden' value='{$id}' name='{$this->page_hook}_{$id}[section_id]'>";
				}

				foreach ( $forms as $form ) {

					echo '<form method="post" action="options.php">';
					// print hidden section_id fields
					echo $section_ids;

					// lets you add additional fields
					echo apply_filters( "{$this->page_hook}_form_fields", '', $form['id'], $form );

					settings_fields( $this->page_hook . '' . $form['id'] );
					wc_bof_do_settings_sections( $this->page_hook . '' . $form['id'] );

					$submit = ( isset( $form['submit'] ) && $form['submit'] ) ? $form['submit'] : '';

					if ( ( '' === $submit ) && isset( $page['submit'] ) && $page['submit'] ) {
						$submit = $page['submit'];
					}

					$text             = isset( $submit['text'] ) ? $submit['text'] : null;
					$type             = isset( $submit['$type'] ) ? $submit['text'] : 'primary';
					$name             = isset( $submit['$name'] ) ? $submit['name'] : 'submit';
					$other_attributes = array( 'id' => $form['id'] );

					submit_button( $text, $type, $name, true, $other_attributes );
					echo '</form>';
				}

			}
		} // render_form()

		/**
		 * Display Advert in Footer
		 *
		 * @since 2.0
		 *
		 * @param string $plugin_title Plugin title.
		 * @param string $tab_id Page id. Manually set the active tab.
		 *
		 * @return void
		 */
		public function render_footer( $plugin_title = '', $tab_id = false ) {
			if ( ! class_exists( 'WooCommerce_Bulk_Order_Form_Functions' ) || ! class_exists( 'WC_Bulk_Order_Form_Prepopulated_Functions' ) ) {
				?>
				<script type="text/javascript">
					jQuery( function ( $ ) {
						$( "input.wcbulkorder-disabled" ).attr( 'disabled', true );

						$( '.extensions .more' ).hide();
						$( '.extensions > li' ).click( function () {
							$( this ).toggleClass( 'expanded' );
							$( this ).find( '.more' ).slideToggle();
						} );
						$( '.wrap.wc_bof_settings .select2-container--disabled' ).click( function () {
							show_bof_pro_feature( $( this ).closest( 'td' ) );
						} );
						$( '.wrap.wc_bof_settings td' ).click( function () {
							if ( $( this ).find( 'input' ).prop( 'disabled' ) ) {
								show_bof_pro_feature( $( this ).closest( 'td' ) );
							}
						} );
						$( '.wrap.wc_bof_settings label' ).click( function () {
							if ( $( '#' + $( this ).attr( 'for' ) ).prop( 'disabled' ) ) {
								show_bof_pro_feature( $( this ).closest( 'td' ) );
							}
						} );

						function show_bof_pro_feature ( $row ) {
							if ( $row.find( '.pro-feature' ).length < 1 ) {
								$row.append( '<div class="pro-feature" style="display:none;"><?php printf( __( 'This feature is only available in %sWooCommerce Bulk Order Form Pro%s', 'woocommerce-bulk-order-form' ), '<a href="https://wpovernight.com/downloads/woocommerce-bulk-order-form">', '</a>' ); ?></div>' );
							}
							$row_ad = $row.find( '.pro-feature' );
							$( '.pro-feature' ).not( $row_ad ).hide( 'slow' );
							$row_ad.show( 'slow' );
						}
					} );
				</script>
				<div class="wcpdf-extensions-ad">
					<img src="<?php echo plugins_url( 'includes/img/wpo-helper.png', WC_BOF_FILE ); ?>"
						 class="wpo-helper">
					<h3><?php _e( 'Check out these premium extensions!', 'woocommerce-bulk-order-form' ); ?></h3>
					<i>(<?php _e( 'Click items to read more', 'woocommerce-bulk-order-form' ); ?>)</i>
					<ul class="extensions">
						<?php
						if ( ! class_exists( 'WooCommerce_Bulk_Order_Form_Pro' ) ) {
							?>
							<li>
								<?php _e( 'Go Pro: Get WooCommerce Bulk Order Form Pro!', 'woocommerce-bulk-order-form' ) ?>
								<div class="more" style="display:none;">
									<?php _e( 'Supercharge WooCommerce Bulk Order Form with the following features:', 'woocommerce-bulk-order-form' ); ?>
									<ul>
										<li><?php _e( 'Let user search by product id, title, sku, or all', 'woocommerce-bulk-order-form' ) ?></li>
										<li><?php _e( 'Choose from 5 different label outputs for the product field on your bulk order form', 'woocommerce-bulk-order-form' ) ?></li>
										<li><?php _e( 'Automatically add extra product rows with the "add row" button (can be turned on or off)', 'woocommerce-bulk-order-form' ) ?>
											*
										</li>
										<li><?php _e( 'Create and customize as many shortcodes as you want!', 'woocommerce-bulk-order-form' ) ?></li>
										<li><?php _e( 'Display images in product search', 'woocommerce-bulk-order-form' ) ?></li>
										<li><?php _e( 'Set custom add to cart success/failure messages', 'woocommerce-bulk-order-form' ) ?></li>
										<li><?php _e( 'Pick to send user to cart or checkout', 'woocommerce-bulk-order-form' ) ?></li>
										<li><?php _e( 'Option to include add to cart button next to each product field', 'woocommerce-bulk-order-form' ) ?></li>
									</ul>
									<a href="https://wpovernight.com/downloads/woocommerce-bulk-order-form/?source=settings"
									   target="_blank"><?php _e( "Get WooCommerce Bulk Order Form Pro!", 'woocommerce-bulk-order-form' ); ?></a>
							</li>
						<?php } ?>
						<?php
						if ( ! class_exists( 'WC_Bulk_Order_Form_Prepopulated_Functions' ) && ! defined( 'BOF_PP_PATH' ) ) {
							?>
							<li>
								<?php _e( 'Bulk Order Form Prepopulated Template', 'woocommerce-bulk-order-form' ) ?>
								<div class="more" style="display:none;">
									<?php _e( 'Remove the autocomplete search and pre-populate the form with your products and variations. (Very Popular)!', 'woocommerce-bulk-order-form' ); ?>
									<br/>
									<a href="https://wpovernight.com/downloads/wc-bulk-order-form-prepopulated/?source=settings"
									   target="_blank"><?php _e( "Get Bulk Order Form Prepopulated Template!", 'woocommerce-bulk-order-form' ); ?></a>
								</div>
							</li>
						<?php } ?>
					</ul>
				</div>
				<style>
					.wcpdf-extensions-ad {
						position:         relative;
						min-height:       90px;
						border:           1px solid #3D5C99;
						background-color: #EBF5FF;
						border-radius:    5px;
						padding:          15px;
						padding-left:     100px;
						margin-top:       15px;
						margin-bottom:    15px;
					}

					img.wpo-helper {
						position: absolute;
						top:      -20px;
						left:     3px;
					}

					.wcpdf-extensions-ad h3 {
						margin: 0;
					}

					.wcpdf-extensions-ad ul {
						margin:      0;
						margin-left: 1.5em;
					}

					.extensions li {
						margin: 0;
					}

					.extensions li ul {
						list-style-type: square;
						margin-top:      0.5em;
						margin-bottom:   0.5em;
					}

					.extensions > li:before {
						content:      "";
						border-color: transparent transparent transparent #111;
						border-style: solid;
						border-width: 0.35em 0.35em 0.35em 0.45em;
						display:      block;
						height:       0;
						width:        0;
						left:         -1em;
						top:          0.9em;
						position:     relative;
					}

					.extensions .expanded:before {
						border-color: #111 transparent transparent transparent;
						left:         -1.17em;
						border-width: 0.45em 0.45em 0.35em 0.35em !important;
					}

					.extensions .more {
						padding:          10px;
						background-color: white;
						border:           1px solid #ccc;
						border-radius:    5px;
					}

					.extensions table td {
						vertical-align: top;
					}

					.select2-container {
						min-width: 50% !important;
					}
				</style>
				<?php
			}
		} // render_footer()

	} // class
} // class exists