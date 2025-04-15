/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

const attributes = ( {
	defaultTitle = __( 'Step', 'flexible-invoices-woocommerce' ),
	defaultDescription = __(
		'Step description text.',
		'flexible-invoices-woocommerce'
	),
	defaultShowStepNumber = true,
} ) => ( {
	title: {
		type: 'string',
		default: defaultTitle,
	},
	description: {
		type: 'string',
		default: defaultDescription,
	},
	showStepNumber: {
		type: 'boolean',
		default: defaultShowStepNumber,
	},
} );

export default attributes;
