import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Dates','flexible-invoices'),
	description: __('Preset of elements containing invoice dates','flexible-invoices'),
	edit: Edit,
	save,
} );
