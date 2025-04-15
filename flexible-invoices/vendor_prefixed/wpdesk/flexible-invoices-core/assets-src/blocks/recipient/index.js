import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __('Recipient','flexible-invoices'),
	description: __('Preset of elements containing recipient data','flexible-invoices'),
	edit: Edit,
	save,
} );
