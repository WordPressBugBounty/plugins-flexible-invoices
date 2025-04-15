import {registerBlockType} from '@wordpress/blocks';
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
				__('Total', 'flexible-invoices'),
				'{SummaryTotalNet}',
				__('Tax name', 'flexible-invoices'),
				'{SummaryTotalTax}',
				'{SummaryTotalGross}',
			]
		},
		rows: {
			...metadata.attributes.rows,
			default: [
				__('Including', 'flexible-invoices'),
				'{SummaryTaxNet}',
				'{SummaryTaxName}',
				'{SummaryTax}',
				'{SummaryTaxGross}',
			]
		}
	}
};

registerBlockType(metadata.name, {
	...customMetadata,
	title: __('Summary table', 'flexible-invoices'),
	description: __('Table containing summary and tax information.', 'flexible-invoices'),
	edit: Edit,
});
