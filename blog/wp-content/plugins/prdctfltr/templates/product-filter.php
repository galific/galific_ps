<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php do_action( 'prdctfltr_filter_hooks' ); ?>

<?php if ( WC_Prdctfltr::get_filter_appearance() === false ) return false; ?>

<?php do_action( 'prdctfltr_filter_before' ); ?>

<div <?php WC_Prdctfltr::get_filter_tag_parameters(); ?>>

	<?php do_action( 'prdctfltr_filter_wrapper_before' ); ?>

	<form <?php echo WC_Prdctfltr::get_action_tag(); ?> class="prdctfltr_woocommerce_ordering" method="get">

		<?php do_action( 'prdctfltr_filter_form_before' ); ?>

		<div <?php WC_Prdctfltr::get_wrapper_tag_parameters(); ?>>

			<div class="prdctfltr_filter_inner">

			<?php

				foreach ( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_active_filters'] as $filterElement ) :

					do_action( 'prdctfltr_before_filter' );

					WC_Prdctfltr::get_filter( $filterElement );

					do_action( 'prdctfltr_after_filter' );

				endforeach;

			?>

			</div>

		</div>

		<?php do_action( 'prdctfltr_filter_form_after' ); ?>

	</form>

	<?php do_action( 'prdctfltr_output_css' ); ?>

</div>

<?php do_action( 'prdctfltr_filter_after' ); ?>