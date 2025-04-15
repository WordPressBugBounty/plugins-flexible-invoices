import {registerBlockType} from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import {__} from "@wordpress/i18n";

registerBlockType(metadata.name, {
	...metadata,
	title: __('Logo', 'flexible-invoices'),
	description: __('Image element. Select image from media library or upload a new one.', 'flexible-invoices'),
    edit: Edit,
    save,
});
