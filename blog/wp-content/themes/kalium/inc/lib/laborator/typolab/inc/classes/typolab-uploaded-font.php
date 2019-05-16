<?php
/**
 *    Uploaded Font
 *
 *    Laborator.co
 *    www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class TypoLab_Uploaded_Font {

	/**
	 * Single Line Font Preview Link
	 */
	public static function singleLinePreview( $font ) {

		$family        = $font['family'];
		$font_variants = $font['options']['font_variants'];

		ob_start();


		if ( ! empty( $font_variants ) ) {
			$font_variant = reset( $font_variants );

			?>
            <div class="font-preview-box">
                <p style="font-family: '<?php echo esc_attr( $family ); ?>'; font-style: <?php echo esc_attr( $font_variant['style'] ); ?>; font-weight: <?php echo esc_attr( $font_variant['weight'] ); ?>"><?php echo esc_html( TypoLab::$font_preview_str ); ?></p>
                <style>
                    <?php echo self::getFontFace( $family, $font_variant ); ?>
                </style>
            </div>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * Get font face.
	 */
	public static function getFontFace( $family, $variant ) {
		$family_sanitized = str_replace( ' ', '-', $family );
		$files            = $variant['files'];
		$source           = array();
		$src_eot          = '';
		$nl               = PHP_EOL;

		if ( ! empty( $files['eot'] ) ) {
			$src_eot = "url('{$files['eot']}')";
		}


		$source[] = "local('{$family}')";
		$source[] = "local('{$family_sanitized}')";

		if ( ! empty( $files['eot'] ) ) {
			$source[] = "url('{$files['eot']}?#iefix') format('embedded-opentype')";
		}

		if ( ! empty( $files['woff'] ) ) {
			$source[] = "url('{$files['woff']}') format('woff')";
		}

		if ( ! empty( $files['woff2'] ) ) {
			$source[] = "url('{$files['woff2']}') format('woff2')";
		}

		if ( ! empty( $files['ttf'] ) ) {
			$source[] = "url('{$files['ttf']}') format('truetype')";
		}

		if ( ! empty( $files['svg'] ) ) {
			$source[] = "url('{$files['svg']}#svgFont') format('svg')";
		}

		$font_face = '@font-face {' . $nl;
		$font_face .= "\tfont-family: '{$family}';" . $nl;
		$font_face .= "\tfont-style: {$variant['style']};" . $nl;
		$font_face .= "\tfont-weight: {$variant['weight']};" . $nl;
		$font_face .= "\tsrc: {$src_eot};" . $nl;
		$font_face .= "\tsrc: " . implode( ', ', $source ) . ";" . $nl;
		$font_face .= '}';

		return $font_face;
	}

	/**
	 * Download Fonts
	 */
	public static function downloadAndReplaceFontFiles( & $font_face ) {
		if ( is_array( $font_face ) && isset( $font_face['source'] ) && 'uploaded-font' == $font_face['source'] ) {
			if ( isset( $font_face['options']['font_variants'] ) ) {
				$font_variants = &$font_face['options']['font_variants'];

				foreach ( $font_variants as $i => $font_variant ) {
					$files = array_filter( $font_variant['files'] );

					foreach ( $files as $file_type => $url ) {
						if ( false === strpos( $url, site_url() ) ) {
							$temp_file = @download_url( $url );

							if ( ! is_wp_error( $temp_file ) ) {
								$file = array(
									'name'     => basename( $url ), // ex: wp-header-logo.png
									'tmp_name' => $temp_file,
									'error'    => 0,
									'size'     => filesize( $temp_file ),
								);

								$result = wp_handle_sideload( $file, array(
									'test_size' => true,
									'test_form' => false,
								) );

								// Check if font is successfully added to uploads
								if ( is_array( $result ) && ! empty( $result['url'] ) ) {
								    $font_variants[ $i ]['files'][ $file_type ] = $result['url'];
                                }
							}
						}
					}
				}
			}
		}
	}
}