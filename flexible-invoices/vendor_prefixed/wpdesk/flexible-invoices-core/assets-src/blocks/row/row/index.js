import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Row','flexible-invoices'),
	description: __('Row block used to contain all document elements. Use it to group elements together in columns. Change amount of columns using slider.','flexible-invoices'),
	edit: Edit,
	save,
} );
