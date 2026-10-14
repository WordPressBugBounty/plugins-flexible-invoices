<?php

namespace WPDeskFIVendor;

/**
 * Hidden WPML language field printed on classic checkout.
 *
 * @var string $language Current WPML language code.
 */
\defined('ABSPATH') || exit;
?>
<input type="hidden" class="input-hidden" name="wpml_user_lang" id="wpml_user_lang" value="<?php 
echo \esc_attr($language);
?>">
<?php 
