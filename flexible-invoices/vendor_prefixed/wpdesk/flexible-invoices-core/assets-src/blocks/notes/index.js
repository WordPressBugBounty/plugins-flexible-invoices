import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Notes','flexible-invoices'),
	description: __('Element containing document notes','flexible-invoices'),
	edit: Edit,
	save,
} );
