<?php

namespace WPDeskFIVendor;

/**
 * File: footer.php
 */
?>
<div class="fix"></div>
<?php 
if (\is_plugin_active('ksef-for-flexible-invoices/faktury-ksef.php')) {
    $shortcode = \sprintf('[fiksef_document_qrcode document_id="%d"]', $invoice->get_id());
    echo \do_shortcode($shortcode);
}
?>
<input type="hidden" name="document_id" value="<?php 
echo \esc_attr($invoice->get_id());
?>"/>
<input type="hidden" name="order_id" value="<?php 
echo \esc_attr($invoice->get_order_id());
?>"/>
</div>
</body>
</html>
<?php 
