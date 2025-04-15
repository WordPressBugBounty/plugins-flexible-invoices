import { registerBlockType } from '@wordpress/blocks';
import { __ } from "@wordpress/i18n";
import Edit from './edit';
import metadata from './block.json';

const customMetadata = {
	...metadata,
	attributes: {
		...metadata.attributes,
		headers: {
			...metadata.attributes.headers,
			default: [
				__( 'Tax rate', 'flexible-invoices' ),
				__( 'Tax amount', 'flexible-invoices' ),
				__( 'Net price', 'flexible-invoices' ),
				__( 'Gross price', 'flexible-invoices' )
			]
		},
		footers: {
			...metadata.attributes.footers,
			default: [
				'{ConversionShopCurrency} = {ConversionCurrency}',
				__( 'Exchange rate European Central Bank of the day: {ConversionDate}', 'flexible-invoices' )
			]
		},
	}
};

registerBlockType( metadata.name, {
	...customMetadata,
	title: __( "Table of conversion", 'flexible-invoices' ),
	description: __( "Table of conversion block", 'flexible-invoices' ),
	edit: Edit,
} );
