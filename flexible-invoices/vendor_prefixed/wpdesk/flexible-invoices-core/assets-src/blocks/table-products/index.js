import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

const customMetadata = {
	...metadata,
	attributes: {
		...metadata.attributes,
		headers: {
			...metadata.attributes.headers,
			default: [
				__('#', 'flexible-invoices'),
				__('Name', 'flexible-invoices'),
				__('Unit', 'flexible-invoices'),
				__('SKU', 'flexible-invoices'),
				__('Discount', 'flexible-invoices'),
				__('Quantity', 'flexible-invoices'),
				__('Net price', 'flexible-invoices'),
				__('Net amount', 'flexible-invoices'),
				__('Tax rate', 'flexible-invoices'),
				__('Tax amount', 'flexible-invoices'),
				__('Gross amount', 'flexible-invoices'),
			]
		},
	}
};

registerBlockType( metadata.name, {
	...customMetadata,
	title: __('Products Table','flexible-invoices'),
	description: __('Table containing document items.','flexible-invoices'),
	edit: Edit,
} );
