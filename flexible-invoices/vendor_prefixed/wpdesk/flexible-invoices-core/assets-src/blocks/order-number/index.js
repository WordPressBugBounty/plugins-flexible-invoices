import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Order number','flexible-invoices'),
	description: __('Elements containing order number connected to invoice','flexible-invoices'),
	edit: Edit,
	save,
} );
