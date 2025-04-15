import { useBlockProps, RichText } from '@wordpress/block-editor';

const Save = ( { attributes } ) => {
	const {
		content,
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

	const blockProps = useBlockProps.save( {
		style: {
			color: textColor,
			backgroundColor:backgroundColor,
			textAlign: textAlign,
			fontWeight: fontWeight,
			fontStyle: fontStyle,
			fontSize: fontSize,
			textDecoration:textDecoration,

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
			borderTopLeftRadius:borderRadiusTopLeft,
			borderTopRightRadius:borderRadiusTopRight,
			borderBottomLeftRadius:borderRadiusBottomLeft,
			borderBottomRightRadius:borderRadiusBottomRight,
			borderStyle:'solid'
		},
	} );

	return (
		<RichText.Content
			{ ...blockProps }
			value={ content }
			tagName={ "p" }
			className={ 'fi-text-block' }
		/>
	);
};

export default Save;
