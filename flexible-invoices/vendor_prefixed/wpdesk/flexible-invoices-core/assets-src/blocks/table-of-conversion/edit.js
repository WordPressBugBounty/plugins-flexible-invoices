import { RichText } from "@wordpress/block-editor";
import { PanelBody } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { AlignButtons, ColorSelector, FlexibleTable, FontStyles, NumberWithUnit, QuatroInput } from "@wpdesk/wordpress-ui-components";

export default function Edit( { attributes, setAttributes } ) {

	const {
		footers,
		footerBackground,
		footerTextColor,
		footerTextAlign,
		footerTextDecoration,
		footerFontWeight,
		footerFontStyle,
		footerFontSize,
		footerBorderWidthTop,
		footerBorderWidthLeft,
		footerBorderWidthRight,
		footerBorderWidthBottom,
		footerBorderColor
	} = attributes;

	const placeholders = [
		{ label: '{ConversionTaxRate}', description: __( 'Tax rate name', 'flexible-invoices' ) },
		{ label: '{ConversionTaxAmount}', description: __( 'Tax amount', 'flexible-invoices' ) },
		{ label: '{ConversionNetPrice}', description: __( 'Net amount', 'flexible-invoices' ) },
		{ label: '{ConversionGrossPrice}', description: __( 'Gross amount', 'flexible-invoices' ) },
		{ label: '{ConversionShopCurrency}', description: __( 'Currency of the invoice', 'flexible-invoices' ) },
		{ label: '{ConversionCurrency}', description: __( 'Currency of the conversion', 'flexible-invoices' ) },
		{ label: '{ConversionDate}', description: __( 'Date of conversion', 'flexible-invoices' ) },
	];

	const updateFooter = ( index, value ) => {
		const newFooters = [ ...footers ];
		newFooters[ index ] = value;
		setAttributes( { footers: newFooters } );
	};

	const options = <>
		<PanelBody title={ __( 'Footer appearance', 'flexible-invoices' ) }>
			<ColorSelector
				label={ __( 'Text Color', 'flexible-invoices' ) }
				value={ footerTextColor }
				enableAlpha={ false }
				onChange={ ( value ) => setAttributes( { footerTextColor: value } ) }
			/>
			<ColorSelector
				label={ __( 'Background Color', 'flexible-invoices' ) }
				value={ footerBackground }
				onChange={ ( value ) => setAttributes( { footerBackground: value } ) }
			/>

			<NumberWithUnit
				label={ __( 'Font Size', 'flexible-invoices' ) }
				value={ footerFontSize }
				onChange={ ( value ) => setAttributes( { footerFontSize: value } ) }
			/>
			<AlignButtons
				label={ __( 'Text Align', 'flexible-invoices' ) }
				onChange={ ( value ) => setAttributes( { footerTextAlign: value } ) }
			/>
			<FontStyles
				label={ __( 'Font style', 'flexible-invoices' ) }
				onChange={ ( newFontStyles ) => setAttributes( {
					...attributes,
					footerFontStyle: newFontStyles.fontStyle,
					footerFontWeight: newFontStyles.fontWeight,
					footerTextDecoration: newFontStyles.textDecoration
				} ) }
				values={
					{
						fontStyle: footerFontStyle,
						fontWeight: footerFontWeight,
						textDecoration: footerTextDecoration
					}
				}
			/>
			<ColorSelector
				label={ __( 'Border color', 'flexible-invoices' ) }
				value={ footerBorderColor }
				enableAlpha={ false }
				returnFormat={ 'hex' }
				onChange={ ( value ) => setAttributes( { footerBorderColor: value } ) }
			/>
			<QuatroInput
				label={ __( "Border width", 'flexible-invoices' ) }
				values={
					{
						footerBorderWidthTop: footerBorderWidthTop,
						footerBorderWidthLeft: footerBorderWidthLeft,
						footerBorderWidthRight: footerBorderWidthRight,
						footerBorderWidthBottom: footerBorderWidthBottom,
					}
				}
				units={ [
					{ label: 'px', value: 'px' },
					{ label: 'em', value: 'em' },
					{ label: 'pt', value: 'pt' }
				] }
				onChange={ ( newValues ) => {
					setAttributes( { ...attributes, ...newValues } );
				} }
			/>
		</PanelBody></>


	const footer = <>
		<tr className={ "fitb-item-table-footer-row" } style={ {
			background: footerBackground,
		} }>
			<td className={ "fitb-item-table-footer-cell" } colSpan={ 4 }
				style={ {
					borderStyle: "solid",
					borderColor: footerBorderColor,
					borderTopWidth: footerBorderWidthTop,
					borderLeftWidth: footerBorderWidthLeft,
					borderRightWidth: footerBorderWidthRight,
					borderBottomWidth: footerBorderWidthBottom,
				} }>
				{ footers.map( ( footer, index ) => (
					<RichText
						tagName="div"
						key={ index }
						value={ footer }
						style={ {
							color: footerTextColor,
							textAlign: footerTextAlign,
							fontStyle: footerFontStyle,
							textDecoration: footerTextDecoration,
							fontSize: footerFontSize,
							fontWeight: footerFontWeight,
						} }
						onChange={ ( value ) => updateFooter( index, value ) }
					/>
				) ) }
			</td>
		</tr>
	</>

	return (
		<FlexibleTable
			attributes={ attributes }
			setAttributes={ setAttributes }
			placeholders={ placeholders }
			tableFooter={ footer }
			additionalOptions={ options }
		/>
	);
}
