import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {Modal, RadioControl, SelectControl, TextareaControl, TextControl, ToggleControl,} from '@wordpress/components';
import {useSelect, useDispatch} from '@wordpress/data';
import {__} from "@wordpress/i18n";
import {registerPlugin} from '@wordpress/plugins';
import {useCallback, useEffect, useState} from "react";
import FIBlockTemplateWizard from "./FIBlockTemplateWizard";

const TemplateSettings = () => {
	const postType = useSelect((select) => select('core/editor').getCurrentPostType(), []);

	if (postType !== 'fi_template') {
		return null;
	}

	const meta = useSelect(
		(select) => select('core/editor').getEditedPostAttribute('meta') || {},
		[]
	);
	const {editPost} = useDispatch('core/editor');
	const [selectedFont, setSelectedFont] = useState('dejavusanscondensed');

	const handleMetaChange = useCallback((metaName, newValue) => {
		editPost({
			meta: {
				...meta,
				[metaName]: newValue
			},
		});
	}, [ meta, editPost ]);

	const getMetaValue = (key, defaultValue ) => meta?.[key] ?? defaultValue;

	const handleFontChange = useCallback((metaName, newValue) => {
		handleMetaChange(metaName, newValue);
		setSelectedFont(newValue);
	}, [meta,handleMetaChange]);

	const fontOptions = [
		{ label: 'DejaVu Sans', value: 'dejavusans' },
		{ label: 'DejaVu Serif', value: 'dejavuserif' },
		{ label: 'DejaVu Sans Condensed', value: 'dejavusanscondensed' },
		{ label: 'FreeSerif', value: 'freeserif' },
		{ label: 'Montserrat', value: 'montserrat' },
		{ label: 'OpenSans', value: 'opensans' },
		{ label: 'OpenSans Condensed', value: 'opensanscondensed' },
		{ label: 'Roboto', value: 'roboto' },
		{ label: 'Roboto Slab', value: 'robotoslab' },
		{ label: 'Rubik', value: 'rubik' },
		{ label: 'Titillium Web', value: 'titilliumweb' },
	];

	const [ isWizardOpen, setWizardOpen ] = useState( false );
	useEffect( () => {
		if ( ! getMetaValue('welcome_dismissed', false) ) {
			setWizardOpen( true );
		}
	}, [ meta ] );

	const closeWizard = useCallback( () => {
		setWizardOpen( false );
		handleMetaChange( 'welcome_dismissed', true );
	}, [ meta, editPost ] );


	return (
		<>
			<>
			<style key="dynamic-font-style">
				{selectedFont &&
					`.fi-row * { font-family: '${selectedFont}', sans-serif !important; }`}
			</style>
			</>

			<PluginDocumentSettingPanel
				name="fi-general-template-settings"
				title={__('General settings', 'flexible-invoices')}
				className="fi-general-template-settings"
				initialOpen={true}
			>
				<ToggleControl
					key="woocommerce_shipping_address"
					label={__('Recipient shipping address', 'flexible-invoices')}
					help={__('Enable if you want to remove recipient block if recipient has the same shipping address as customer', 'flexible-invoices')}
					checked={getMetaValue('woocommerce_shipping_address', false)}
					onChange={(newValue) => handleMetaChange('woocommerce_shipping_address', newValue)}
				/>
				<ToggleControl
					key="hide_tax_number"
					label={__('If tax is 0 hide seller\'s VAT Number on PDF invoices', 'flexible-invoices')}
					checked={getMetaValue('hide_tax_number', false)}
					onChange={(newValue) => handleMetaChange('hide_tax_number', newValue)}
				/>
				<ToggleControl
					key="hide_tax_cells"
					label={__('If tax is 0 hide all tax cells on PDF invoices', 'flexible-invoices')}
					checked={getMetaValue('hide_tax_cells', false)}
					onChange={(newValue) => handleMetaChange('hide_tax_cells', newValue)}
				/>
				<ToggleControl
					key="enable_page_numbering"
					label={__('Enable page numbering', 'flexible-invoices')}
					checked={getMetaValue('enable_page_numbering', false)}
					onChange={(newValue) => handleMetaChange('enable_page_numbering', newValue)}
				/>
				<SelectControl
					label={__('Document font', 'flexible-invoices')}
					options={fontOptions}
					value={getMetaValue('document_font', 'dejavusans')}
					onChange={(newValue) => handleFontChange('document_font', newValue)}
				/>
			</PluginDocumentSettingPanel>
			<PluginDocumentSettingPanel
				name="fi-invoice-template-settings"
				title={__('Invoice settings', 'flexible-invoices')}
				className="fi-invoice-template-settings"
			>
			<TextControl
					type='number'
					key='invoice_next_number'
					label={__('Next document number', 'flexible-invoices')}
					help={__('Enter the next document number. The default value is 1 and changes every time an invoice is issued. Existing documents won\'t be changed.', 'flexible-invoices')}
					value={getMetaValue('invoice_next_number', 1)}
					onChange={(newValue) => handleMetaChange('invoice_next_number', newValue)}
				/>

				<RadioControl
					key="invoice_number_reset_type"
					label={__('Number reset', 'flexible-invoices')}
					selected={getMetaValue('invoice_number_reset_type', 'month') }
					help={__('Select when to reset the invoice number to 1.', 'flexible-invoices')}
					options={[
						{label: __('Yearly', 'flexible-invoices'), value: 'year'},
						{label: __('Monthly', 'flexible-invoices'), value: 'month'}]
					}
					onChange={(newValue) => handleMetaChange('invoice_number_reset_type', newValue)}
				/>

				<TextControl
					key='invoice_number_prefix'
					label={__('Document Prefix', 'flexible-invoices')}
					value={getMetaValue('invoice_number_prefix', __('Invoice','flexible-invoices'))}
					help={__('For prefixes use the following short tags: {DD} for day, {MM} for month, {YYYY} for year.', 'flexible-invoices')}
					onChange={(newValue) => handleMetaChange('invoice_number_prefix', newValue)}

				/>
				<TextControl
					key='invoice_number_suffix'
					label={__('Document suffix', 'flexible-invoices')}
					value={getMetaValue('invoice_number_suffix', '/{MM}/{YYYY}')}
					help={__('For suffixes use the following short tags: {DD} for day, {MM} for month, {YYYY} for year.', 'flexible-invoices')}
					onChange={(newValue) => handleMetaChange('invoice_number_suffix', newValue)}
				/>
				<TextControl
					type='number'
					key='invoice_default_due_time'
					label={__('Default Due Time', 'flexible-invoices')}
					help={__('Enter the default due time (in days) for invoices.', 'flexible-invoices')}
					value={getMetaValue('invoice_default_due_time', 1)}
					onChange={(newValue) => handleMetaChange('invoice_default_due_time', newValue)}

				/>
				<TextareaControl
					key='invoice_notes'
					label={__('Notes', 'flexible-invoices')}
					help={__('Notes visible on generated document.', 'flexible-invoices')}
					value={getMetaValue('invoice_notes', '')}
					onChange={(newValue) => handleMetaChange('invoice_notes', newValue)}

				/>
			</PluginDocumentSettingPanel>
			<PluginDocumentSettingPanel
				name="fi-correction-template-settings"
				title={__('Correction settings', 'flexible-invoices')}
				className="fi-correction-template-settings"
			>
				<TextControl
					type='number'
					key='correction_next_number'
					label={__('Next document number', 'flexible-invoices')}
					help={__('Enter the next document number. The default value is 1 and changes every time an invoice is issued. Existing documents won\'t be changed.', 'flexible-invoices')}
					value={getMetaValue('correction_next_number', 1)}
					onChange={(newValue) => handleMetaChange('correction_next_number', newValue)}
				/>

				<RadioControl
					key="correction_number_reset_type"
					label={__('Number reset', 'flexible-invoices')}
					selected={getMetaValue('correction_number_reset_type', 'month')}
					help={__('Select when to reset the invoice number to 1.', 'flexible-invoices')}
					options={[
						{label: __('Yearly', 'flexible-invoices'), value: 'year'},
						{label: __('Monthly', 'flexible-invoices'), value: 'month'}]
					}
					onChange={(newValue) => handleMetaChange('correction_number_reset_type', newValue)}
				/>

				<TextControl
					key='correction_number_prefix'
					label={__('Document Prefix', 'flexible-invoices')}
					value={getMetaValue('correction_number_prefix', __('Correction', 'flexible-invoices'))}
					help={__('For prefixes use the following short tags: {DD} for day, {MM} for month, {YYYY} for year.', 'flexible-invoices')}
					onChange={(newValue) => handleMetaChange('correction_number_prefix', newValue)}

				/>
				<TextControl
					key='correction_number_suffix'
					label={__('Document suffix', 'flexible-invoices')}
					value={getMetaValue('correction_number_suffix', '/{MM}/{YYYY}')}
					help={__('For suffixes use the following short tags: {DD} for day, {MM} for month, {YYYY} for year.', 'flexible-invoices')}
					onChange={(newValue) => handleMetaChange('correction_number_suffix', newValue)}
				/>
				<TextControl
					type='number'
					key='correction_default_due_time'
					label={__('Default Due Time', 'flexible-invoices')}
					help={__('Enter the default due time (in days) for invoices.', 'flexible-invoices')}
					value={getMetaValue('correction_default_due_time', 7)}
					onChange={(newValue) => handleMetaChange('correction_default_due_time', newValue)}

				/>
				<TextareaControl
					key='correction_notes'
					label={__('Notes', 'flexible-invoices')}
					help={__('Notes visible on generated document.', 'flexible-invoices')}
					value={getMetaValue('correction_notes', '')}
					onChange={(newValue) => handleMetaChange('correction_notes', newValue)}

				/>
			</PluginDocumentSettingPanel>
			<PluginDocumentSettingPanel
				name="fi-proforma-template-settings"
				title={__('Proforma settings', 'flexible-invoices')}
				className="proforma"
			>
				<TextControl
					type='number'
					key='proforma_next_number'
					label={__('Next document number', 'flexible-invoices')}
					help={__('Enter the next document number. The default value is 1 and changes every time an invoice is issued. Existing documents won\'t be changed.', 'flexible-invoices')}
					value={getMetaValue('proforma_next_number', 1)}
					onChange={(newValue) => handleMetaChange('proforma_next_number', newValue)}
				/>

				<RadioControl
					label={__('Number reset', 'flexible-invoices')}
					key="proforma_number_reset_type"
					selected={getMetaValue('proforma_number_reset_type', 'month')}
					help={__('Select when to reset the invoice number to 1.', 'flexible-invoices')}
					options={[
						{label: __('Yearly', 'flexible-invoices'), value: 'year'},
						{label: __('Monthly', 'flexible-invoices'), value: 'month'}]
					}
					onChange={(newValue) => handleMetaChange('proforma_number_reset_type', newValue)}
				/>

				<TextControl
					key='proforma_number_prefix'
					label={__('Document Prefix', 'flexible-invoices')}
					value={getMetaValue('proforma_number_prefix', __('Proforma', 'flexible-invoices')) }
					help={__('For prefixes use the following short tags: {DD} for day, {MM} for month, {YYYY} for year.', 'flexible-invoices')}
					onChange={(newValue) => handleMetaChange('proforma_number_prefix', newValue)}

				/>
				<TextControl
					key='proforma_number_suffix'
					label={__('Document suffix', 'flexible-invoices')}
					value={getMetaValue('proforma_number_suffix', '/{MM}/{YYYY}')}
					help={__('For suffixes use the following short tags: {DD} for day, {MM} for month, {YYYY} for year.', 'flexible-invoices')}
					onChange={(newValue) => handleMetaChange('proforma_number_suffix', newValue)}
				/>
				<TextControl
					key='proforma_default_due_time'
					type='number'
					label={__('Default Due Time', 'flexible-invoices')}
					help={__('Enter the default due time (in days) for invoices.', 'flexible-invoices')}
					value={getMetaValue('proforma_default_due_time', 7)}
					onChange={(newValue) => handleMetaChange('proforma_default_due_time', newValue)}

				/>
				<TextareaControl
					key='proforma_notes'
					label={__('Notes', 'flexible-invoices')}
					help={__('Notes visible on generated document.', 'flexible-invoices')}
					value={getMetaValue('proforma_notes', '')}
					onChange={(newValue) => handleMetaChange('proforma_notes', newValue)}

				/>
			</PluginDocumentSettingPanel>
			{ isWizardOpen && (
				<Modal
					onRequestClose={ closeWizard }
					className="fi-template-wizard-modal"
				>
					<FIBlockTemplateWizard onWizardClose={ closeWizard } />
				</Modal>
			) }
		</>
	);
};

registerPlugin('fi-template-settings', {
	render: TemplateSettings,
	icon: 'admin-settings',
});
