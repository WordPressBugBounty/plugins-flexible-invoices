import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Payment method','flexible-invoices'),
	description: __('Document payment method. Displays selected payment method','flexible-invoices'),
	edit: Edit,
	save,
} );
