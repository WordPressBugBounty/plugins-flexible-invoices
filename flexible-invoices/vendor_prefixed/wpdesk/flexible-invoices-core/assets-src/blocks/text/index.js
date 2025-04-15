import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Text','flexible-invoices'),
	description: __('Text block used to display custom text.','flexible-invoices'),
	edit: Edit,
	save,
} );
