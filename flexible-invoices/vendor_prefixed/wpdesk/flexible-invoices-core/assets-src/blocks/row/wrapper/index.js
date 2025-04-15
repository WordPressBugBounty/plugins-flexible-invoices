import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Wrapper','flexible-invoices'),
	description: __('Column used as parent for all elements. Insert other blocks into column.','flexible-invoices'),
	edit: Edit,
	save,
} );
