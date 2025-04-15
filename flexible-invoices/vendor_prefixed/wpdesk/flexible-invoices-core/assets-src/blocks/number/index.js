import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Document number','flexible-invoices'),
	description: __('Document formatted number. Contains prefix and suffix.','flexible-invoices'),
	edit: Edit,
	save,
} );
