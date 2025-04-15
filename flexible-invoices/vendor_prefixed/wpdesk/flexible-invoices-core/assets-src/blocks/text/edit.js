import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import {
	PanelBody,
} from '@wordpress/components';
import {QuatroInput, ColorSelector, FontStyles, NumberWithUnit, AlignButtons} from "@wpdesk/wordpress-ui-components";
const Edit = ( { attributes, setAttributes } ) => {
	const {
		content,
		placeholder,
		level,
		textColor,
		backgroundColor,
		textAlign,
		fontWeight,
		fontStyle,
		fontSize,
		textDecoration,


		paddingLeft,
		paddingRight,
		paddingTop,
		paddingBottom,

		marginLeft,
		marginRight,
		marginTop,
		marginBottom,

		borderColor,
		borderWidthLeft,
		borderWidthRight,
		borderWidthTop,
		borderWidthBottom,
		borderRadiusTopLeft,
		borderRadiusTopRight,
		borderRadiusBottomLeft,
		borderRadiusBottomRight
	} = attributes;

	const handleMultipleAttributesChange = ( newValues ) => {
		setAttributes( { ...attributes, ...newValues } );
	}

	const blockProps = useBlockProps( {
		style: {
			color: textColor,
			backgroundColor: backgroundColor,
			textAlign: textAlign,
			fontWeight: fontWeight,
			fontStyle: fontStyle,
			fontSize: fontSize,
			textDecoration: textDecoration,

			paddingLeft: paddingLeft,
			paddingRight: paddingRight,
			paddingTop: paddingTop,
			paddingBottom: paddingBottom,
			marginLeft: marginLeft,
			marginRight: marginRight,
			marginTop: marginTop,
			marginBottom: marginBottom,

			borderColor: borderColor,
			borderLeftWidth: borderWidthLeft,
			borderRightWidth: borderWidthRight,
			borderTopWidth: borderWidthTop,
			borderBottomWidth: borderWidthBottom,
			borderTopLeftRadius: borderRadiusTopLeft,
			borderTopRightRadius: borderRadiusTopRight,
			borderBottomLeftRadius: borderRadiusBottomLeft,
			borderBottomRightRadius: borderRadiusBottomRight,
			borderStyle: 'solid'
		},
	} );

	return (
		<>
			<InspectorControls>
				<>
				<PanelBody title={ __( 'Text Settings', 'flexible-invoices' ) }>
					<ColorSelector
						label={ __( 'Text Color', 'flexible-invoices' ) }
						value={ textColor }
						enableAlpha={ false }
						onChange={ ( value ) => setAttributes( { textColor: value } ) }
					/>
					<ColorSelector
						label={ __( 'Background Color', 'flexible-invoices' ) }
						value={ backgroundColor }
						onChange={ ( value ) => setAttributes( { backgroundColor: value } ) }
					/>
					<NumberWithUnit
						label={ __( 'Font Size', 'flexible-invoices' ) }
						value={ fontSize }
						onChange={ ( value ) => setAttributes( { fontSize: value } ) }
					/>
					<AlignButtons
						label={ __( 'Text Align', 'flexible-invoices' ) }
						onChange={ ( value ) => setAttributes( { textAlign: value } ) }
					/>
					<FontStyles
						label={ __( 'Font Styles', 'flexible-invoices' ) }
						onChange={ ( value ) => handleMultipleAttributesChange( value ) }
						values={
							{
								fontStyle: fontStyle,
								fontWeight: fontWeight,
								textDecoration: textDecoration
							}
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Position', 'flexible-invoices' ) }>
					<QuatroInput
						label={ __( "Margins", 'flexible-invoices' ) }
						values={
							{
								marginTop: marginTop,
								marginLeft: marginLeft,
								marginRight: marginRight,
								marginBottom: marginBottom
							}
						}
						onChange={ handleMultipleAttributesChange }
					/>
					<QuatroInput
						label={ __( "Paddings", 'flexible-invoices' ) }
						values={
							{
								paddingTop: paddingTop,
								paddingLeft: paddingLeft,
								paddingRight: paddingRight,
								paddingBottom: paddingBottom
							}
						}
						onChange={ handleMultipleAttributesChange }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Borders', 'flexible-invoices' ) }>
					<ColorSelector
						label={ __( 'Border Color', 'flexible-invoices' ) }
						value={ borderColor }
						enableAlpha={ false }
						returnFormat={ 'hex' }
						onChange={ ( value ) => setAttributes( { borderColor: value } ) }
					/>
					<QuatroInput
						label={ __( "Border width", 'flexible-invoices' ) }
						values={
							{
								borderWidthTop: borderWidthTop,
								borderWidthLeft: borderWidthLeft,
								borderWidthRight: borderWidthRight,
								borderWidthBottom: borderWidthBottom,
							}
						}
						units={ [
							{ label: 'px', value: 'px' },
							{ label: 'em', value: 'em' },
							{ label: 'pt', value: 'pt' }
						] }
						onChange={ handleMultipleAttributesChange }
					/>
					<QuatroInput
						label={ __( "Border radius", 'flexible-invoices' ) }
						values={
							{
								borderRadiusTopLeft: borderRadiusTopLeft,
								borderRadiusTopRight: borderRadiusTopRight,
								borderRadiusBottomLeft: borderRadiusBottomLeft,
								borderRadiusBottomRight: borderRadiusBottomRight
							}
						}
						layout='box'
						onChange={ handleMultipleAttributesChange }
					/>
				</PanelBody>
				</>
			</InspectorControls>
			<RichText
				{ ...blockProps }
				tagName={ level }
				value={ content }
				allowedFormats={ [] }
				onChange={ ( value ) => setAttributes( { content: value } ) }
				className={ 'fi-text-block' }
				placeholder={ placeholder }
			/>
		</>
	);
};

export default Edit;
