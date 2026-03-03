<?php

namespace WPDeskFIVendor;

/**
 * @var string $description
 * @var string $rate_url
 */
if (!isset($rate_url)) {
    $rate_url = 'https://wpdesk.link/fi-footer-review-link';
}
if (!isset($description)) {
    $is_PL = \get_locale() === 'pl_PL' ? 'https://wpdesk.pl/sk/flexible-invoices-woocommerce-rate-pl' : 'https://wpdesk.net/sk/flexible-invoices-woocommerce-rate-en';
    $url = 'https://wpdesk.link/fi-footer-review-link';
    $description = \sprintf(
        // translators: %1$s icon,  %2$s open url tag, %3$s close url tag.
        \esc_html__('Created with %1$s by %2$sWP Desk%3$s - if you like Flexible Invoices rate us &rarr;', 'flexible-invoices-woocommerce'),
        '<span class="love"><span class="dashicons dashicons-heart"></span></span>',
        '<a target="_blank" href="' . \esc_url($is_PL) . '">',
        '</a>'
    );
}
?>
<div id="fiw-settings-footer">
	<section class="rate-plugin-wrapper wpdesk-rate-icons">
		<p class="description">
			<?php 
echo \wp_kses_post($description);
?>
			<a href="<?php 
echo \esc_url($rate_url);
?>" target="blank">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</a>

	</section>
	<style>
		.rate-plugin-wrapper p.description {
			display: inline-block;
			padding-left: 10px;
		}

		.rate-plugin-wrapper span.love {
			color: firebrick;
		}

		.wpdesk-rate-icons {
			padding: 10px;
			text-align: center;
		}

		.wpdesk-rate-icons a {
			text-decoration: none;
		}

		.wpdesk-rate-icons p {
			margin: 0 0;
			color: #AAA;
		}

		.wpdesk-rate-icons p.description {
			color: #333;
			margin-bottom: 5px;
		}

		.wpdesk-rate-icons p.description2 {
			margin-top: 5px;
		}

		.wpdesk-rate-icons [class*="dashicons-star-"] {
			color: #ffb900;
		}

		.wpdesk-rate-icons span.dashicons-star-filled {
			transition: all 3s;
		}

		.wpdesk-rate-icons a:hover span.dashicons-star-filled {
			transform: rotate(180deg);
		}

		.wpdesk-rate-icons .love .dashicons {
			font-size: 24px;
			width: 24px;
			height: 24px;
		}
	</style>
</div>
<?php 
