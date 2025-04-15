import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Signature','flexible-invoices'),
	description: __('Signature','flexible-invoices'),
	edit: Edit,
	save,
} );
