<?php

namespace WPDeskFIVendor;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\InvoiceQuery;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
$proforma_id = (int) \get_post_meta($document->get_id(), '_document_proforma_relation', \true);
$correction_ids = \get_post_meta($document->get_id(), '_document_correction_relation');
if (isset($order)) {
    ?>
	<div class="form-field form-required">
		<h4><?php 
    \esc_html_e('Order', 'flexible-invoices');
    ?></h4>
		<p>
			<a target="_blank" href="<?php 
    echo \esc_url(\admin_url('admin.php?page=wc-orders&action=edit&id=' . $order->get_id()));
    ?>">
				<strong><?php 
    echo \esc_html($order->get_title());
    ?> #<?php 
    echo \esc_html($order->get_order_number());
    ?></strong>
			</a>
		</p>
	</div>
<?php 
}
?>

<div class="form-field form-required">
	<?php 
$related_proforma_id = isset($_GET['related_proforma_id']) ? (int) \wp_unslash($_GET['related_proforma_id']) : $proforma_id;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$proforma = (new InvoiceQuery())->get_document_by_id($related_proforma_id);
?>
	<h4><?php 
\esc_html_e('Proforma', 'flexible-invoices');
?></h4>
	<input type="hidden" id="document_proforma_relation" name="document_proforma_relation" value="<?php 
echo \esc_attr((string) (int) $related_proforma_id);
?>"/>
	<?php 
if (isset($proforma->post_title)) {
    ?>
		<p>
			<a href="<?php 
    echo \esc_url(\admin_url('post.php?post=' . $proforma->ID . '&action=edit'));
    ?>">
				<?php 
    echo \esc_html($proforma->post_title);
    ?>
			</a>
		</p>
	<?php 
} else {
    ?>
		<p><?php 
    \esc_html_e('---', 'flexible-invoices');
    ?></p>
	<?php 
}
?>
</div>

<div class="form-field form-required">
	<h4><?php 
\esc_html_e('Correction', 'flexible-invoices');
?></h4>
	<?php 
$has_correction = \false;
if ($correction_ids && \is_array($correction_ids)) {
    foreach ($correction_ids as $correction_id) {
        $correction = (new InvoiceQuery())->get_document_by_id((int) $correction_id);
        if (!$correction instanceof \WP_Post || RegisterPostType::POST_TYPE_NAME !== $correction->post_type || !isset($correction->post_title)) {
            continue;
        }
        $has_correction = \true;
        ?>
				<p>
					<a href="<?php 
        echo \esc_url(\admin_url('post.php?post=' . $correction->ID . '&action=edit'));
        ?>">
						<?php 
        echo \esc_html($correction->post_title);
        ?>
					</a>
					<input type="hidden" id="document_correction_relation" name="document_correction_relation[]" value="<?php 
        echo \esc_attr((string) $correction->ID);
        ?>"/>
				</p>
				<?php 
    }
}
if (!$has_correction) {
    ?>
		<p><?php 
    echo \esc_html('Brak');
    ?></p>
		<?php 
}
?>
</div>
<?php 
