import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Payment link','flexible-invoices'),
	description: __('Order payment link. Displays clickable payment link for order.','flexible-invoices'),
	edit: Edit,
	save,
} );
