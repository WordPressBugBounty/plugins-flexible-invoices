import {useSelect, useDispatch} from '@wordpress/data';
import {__} from "@wordpress/i18n";
import {useCallback} from "react";
import Wizard from "./Wizard/Wizard";
import image_roadmap from './images/roadmap.jpg';
import image_general_settings from './images/general-settings.gif';
import image_block_settings from './images/block-settings.gif';
import image_block_searching from './images/block-searching.gif';

export default function FIBlockTemplateWizard({onWizardClose}) {

	const meta = useSelect(
		(select) => select('core/editor').getEditedPostAttribute('meta') || {},
		[]
	);
	const {editPost} = useDispatch('core/editor');


	const handleMetaChange = useCallback((metaName, newValue) => {
		editPost({
			meta: {
				...meta,
				[metaName]: newValue
			},
		});
	}, [meta, editPost]);

	//todo: change images...
	const wizardSteps = [
		{
			title: __('Settings', 'flexible-invoices'),
			description: __('Edit invoice numbering and basic template settings', 'flexible-invoices'),
			gifUrl:image_general_settings,
			content:
				__( '<p><strong>Attention!</strong> Each template has its own numbering settings, meaning that changing the default invoice template will affect numbering. In the main settings tab, you can also adjust font settings, page numbering, and the display of cells with zero values.</p>', 'flexible-invoices')
		},
		{
			title: __('Search', 'flexible-invoices'),
			description: __('Find the necessary elements to add to the invoice.', 'flexible-invoices'),
			gifUrl: image_block_searching,
			content:
				__('<p>To find a block to add to the editor, click "+" in the top-left corner, then type "invoices" in the search bar. This will quickly show all blocks supported by the plugin.</p>', 'flexible-invoices')
		},
		{
			title: __('Block editing','flexible-invoices'),
			description: __('Customize the appearance and content of invoice elements', 'flexible-invoices'),
			gifUrl:image_block_settings,
			content:
				__('<p>Blocks include various settings depending on the content they support. For example, the product table block allows you to customize the appearance of table headers and body, as well as the position and size of fonts. You can also toggle the visibility of individual columns.</p>', 'flexible-invoices')
		},
		{
			title: __('Roadmap','flexible-invoices'),
			description: __('Customize the appearance and content of invoice elements', 'flexible-invoices'),
			gifUrl:image_roadmap,
			content:
				__('<p>The template editor is just the beginning of changes for Flexible Invoices. This year, we plan to release:</p><ul><li>A paid plugin add-on that lets you set conditions for issuing invoices – for example, based on selected products or payment methods. This will allow invoice numbering and design to be customized.</li></ul><p>We also aim to integrate with multi-vendor plugins and add support for receipts and duplicates. Have an idea? Share it <a href="https://flexibleinvoices.com/ideas/" target="_blank">here</a>.</p>', 'flexible-invoices')
		},
	];


	return (
		<>
			<div className='fi-editor-wizard'>
				{!meta.welcome_dismissed &&
					<>
						<Wizard key='fi-editor-wizard' steps={wizardSteps} onWizardClose={onWizardClose}/>
						{/* Need to overwrite modal styles for wizard to work properly */}
						<style key={'editor-wizard-style-helper'}>{`
							.components-modal__frame {
								width: 100%;
								height: 100%;
								max-width: 100%;
								max-height: 100%;
								border-radius: 0;
								background: transparent;
							}
							.components-modal__frame .components-modal__header{
								display:none;
							}.components-modal__frame .components-modal__content{
								background: transparent
								padding:0;
							}
							`}</style>
					</>
				}
			</div>

		</>
	);
};


