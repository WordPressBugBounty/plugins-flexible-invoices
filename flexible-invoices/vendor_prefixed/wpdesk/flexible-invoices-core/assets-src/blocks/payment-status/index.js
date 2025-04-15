import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Payment status','flexible-invoices'),
	description: __('Payment status of the invoice.','flexible-invoices'),
	edit: Edit,
	save,
} );
