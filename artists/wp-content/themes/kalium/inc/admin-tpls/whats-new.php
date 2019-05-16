<?php
/**
 *	Whats New
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
$version = kalium()->getVersion();
?>
<div class="kalium-whats-new">
	
	<?php if ( kalium()->url->get( 'welcome', true ) ) : ?>
	<div class="kalium-activated">
		<h3>
			Thanks for choosing Kalium theme!
			<br>
			<small>Here are the first steps to setup the theme:</small>
		</h3>
		
		<ol>
			<li>Install and activate required plugins by <a href="<?php echo admin_url('themes.php?page=kalium-install-plugins' ); ?>" target="_blank">clicking here</a></li>
			<?php if ( ! kalium()->theme_license->isValid() ) : ?>
			<li>Activate the theme on <a href="<?php echo admin_url( 'admin.php?page=kalium-product-registration' ); ?>" target="_blank">Product Registration</a> tab</li>
			<?php endif; ?>
			<li>Install demo content via <a href="<?php echo admin_url( 'admin.php?page=laborator-demo-content-installer' ); ?>" target="_blank">One-Click Demo Content</a> installer (requires <a href="https://documentation.laborator.co/kb/kalium/activating-the-theme/" target="_blank">theme activation</a>)</li>
			<li>Configure <a href="<?php echo admin_url( 'admin.php?page=laborator_options' ); ?>" target="_blank">theme options</a> (optional)</li>
			<li>Refer to our <a href="<?php echo admin_url( 'admin.php?page=laborator-docs' ); ?>">theme documentation</a> and learn how to setup Kalium (recommended)</li>
		</ol>
	</div>
	<?php endif; ?>
	
	<div class="kalium-version">
		<div class="kalium-version-gradient">
			<span class="numbers-<?php echo strlen( str_replace( '.', '', $version ) ); ?>"><?php echo $version; ?></span>
		</div>
		
		<div class="kalium-version-info">
			<h2>Kalium: What’s New!</h2>
			<p>
				Kalium continuously expands with new features, bug fixes and other adjustments to provide smoother experience for everyone. <br>
				Scroll down to see main features implemented in current version. For complete list of changes <a href="https://documentation.laborator.co/kb/kalium/kalium-changelog/" target="_blank">view full changelog here</a>.
			</p>
		</div>
	</div>
	
	<div class="feature-section two-col">
        <div class="col">
            <a href="https://demo.kaliumtheme.com/wedding/" target="_blank"><img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/wedding-demo.jpg' ); ?>"></a>
            <h3>Wedding demo</h3>
            <p>
                Lovely demo that offers everything you could ever need for your wedding website. Gallery, RSVP, timeline, gift registry sections and so much more.

                <br>
                <a href="https://demo.kaliumtheme.com/wedding/" target="_blank">Click to preview &raquo;</a>
            </p>
        </div>
        <div class="col">
            <a href="https://demo.kaliumtheme.com/medical/" target="_blank"><img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/medical-demo.jpg' ); ?>"></a>
            <h3>Medical demo</h3>
            <p>
                Our newest professionally designed demo is an ideal solution for Hospitals, Medical Centers, Dentists, Health Clinics, Plastic Surgeons, Veterinary Clinics and more.

                <br>
                <a href="https://demo.kaliumtheme.com/medical/" target="_blank">Click to preview &raquo;</a>
            </p>
        </div>
	</div>
	
	
	<div class="whats-new-secondary feature-section three-col">
        <div class="col">
            <img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/custom-font-made-easy.jpg' ); ?>">
            <h3>Upload Custom Fonts</h3>
            <p>
                Ever wanted to store your web fonts on your site? Well this is the option you have been waiting for. Try it on
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=typolab' ) ); ?>">Typography &raquo;</a>
            </p>
        </div>
        <div class="col">
            <img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/premium-plugins.jpg' ); ?>">
            <h3>Premium plugins updates</h3>
            <p>Alongside with the theme update, latest versions of premium plugins are included as well.</p>
        </div>
        <div class="col">
            <img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/acf-pro.jpg' ); ?>">
            <h3>Advanced Custom Fields PRO</h3>
            <p>The PRO is here! Faster and better organized custom fields, packed in one single plugin.</p>
        </div>
	</div>
	
	
	<div class="whats-new-secondary feature-section three-col">
        <div class="col">
            <img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/woocommerce-35.jpg' ); ?>">
            <h3>WooCommerce compatibility</h3>
            <p>
                Added compatibility for latest stable WooCommerce version (3.5.x). Feel safe to update!
            </p>
        </div>
		<div class="col">
			<img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/php7-compatible.jpg' ); ?>">
			<h3>PHP 7 compatibility</h3>
			<p>
				Kalium is now friendly with PHP version 7-7.3.
				<?php if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '7.0', '<' ) ) : ?>
				We recommend you to switch to PHP 7 and see the magic.
				<?php else : ?>
				Its made to work with your hosting environment.
				<?php endif; ?>
			</p>
		</div>
        <div class="col">
            <img src="<?php echo kalium()->assetsUrl( 'images/admin/whats-new/remote-plugin-updates.jpg' ); ?>">
            <h3>Remote plugins updates</h3>
            <p>No need to wait for theme update to get latest version of bundled plugins, you'll get plugin updates right after their release.</p>
        </div>
	</div>
	
	<a href="https://documentation.laborator.co/kb/kalium/kalium-changelog/" target="_blank" class="view-changelog">See full changelog &#65515;</a>
	
</div>