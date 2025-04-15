import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType( metadata.name, {
	...metadata,
	title: __( 'Company', 'flexible-invoices' ),
	description: __( 'Preset of elements containing seller info', 'flexible-invoices' ),
	edit: Edit,
	save,
} );
