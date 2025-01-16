<?php

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Invoice as InvoiceHelper;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\InvoiceQuery;

$proforma_id    = (int) get_post_meta( $document->get_id(), '_document_proforma_relation', true );
$correction_ids = get_post_meta( $document->get_id(), '_document_correction_relation' );
?>
<?php if ( isset( $order ) ): ?>
	<div class="form-field form-required">
		<h4><?php esc_html_e( 'Order', 'flexible-invoices-woocommerce' ); ?></h4>
		<p>
			<a target="_blank" href="<?php echo admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $order->get_id() ); ?>">
				<strong><?php echo $order->get_title(); ?> #<?php echo $order->get_order_number(); ?></strong>
			</a>
		</p>
	</div>
<?php endif; ?>

<div class="form-field form-required">
	<?php
	$related_proforma_id = sanitize_key($_GET['related_proforma_id'] ?? $proforma_id);
	$proforma            = ( new InvoiceQuery() )->get_document_by_id( $related_proforma_id );
	?>
	<h4><?php esc_html_e( 'Proforma', 'flexible-invoices-woocommerce' ); ?></h4>
	<input type="hidden" id="document_proforma_relation" name="document_proforma_relation" value="<?php echo (int) $related_proforma_id; ?>"/>
	<?php if ( isset( $proforma->post_title ) ): ?>
		<p>
			<a href="<?php echo admin_url( 'post.php?post=' . $proforma->ID . '&action=edit' ); ?>">
				<?php echo $proforma->post_title; ?>
			</a>
		</p>
	<?php else: ?>
		<p><?php esc_html_e( '---', 'flexible-invoices-woocommerce' ); ?></p>
	<?php endif; ?>
</div>

<div class="form-field form-required">
	<h4><?php esc_html_e( 'Correction', 'flexible-invoices-woocommerce' ); ?></h4>
	<?php
	$has_correction     = false;
	if ( $correction_ids && is_array( $correction_ids ) ):
		foreach ( $correction_ids as $correction_id ):
			$has_correction = true;
			$correction = ( new InvoiceQuery() )->get_document_by_id( (int) $correction_id );
			?>
			<?php if ( isset( $correction->post_title ) ): ?>
			<p>
				<a href="<?php echo esc_url(admin_url( 'post.php?post=' . $correction->ID . '&action=edit' )); ?>">
					<?php echo esc_html($correction->post_title); ?>
				</a>
				<input type="hidden" id="document_correction_relation" name="document_correction_relation[]" value="<?php echo esc_attr($correction->ID); ?>"/>
			</p>
		<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if ( ! $has_correction ): ?>
		<p>Brak</p>
	<?php endif; ?>
</div>
