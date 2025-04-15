import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { useEffect } from "react";
import { ColorSelector, DuoInput } from "@wpdesk/wordpress-ui-components";

const Edit = ( { attributes, setAttributes, context } ) => {

	const {
		backgroundColor,
		marginTop,
		marginBottom,
		paddingTop,
		paddingBottom,
	} = attributes;

	useEffect( () => {
		setAttributes( {
			columnAmount: context[ 'flexible-invoices/columnAmount' ],
		} );
	}, [ context ] );

	const columnStyle = {
		minHeight: '1px',
		float: 'left',
		width: `${ 100 / context[ 'flexible-invoices/columnAmount' ] }%`,
		backgroundColor: backgroundColor,
		marginTop,
		marginBottom,
		paddingTop,
		paddingBottom
	};

	const blockProps = useBlockProps( { className: 'fi-innerwrapper', style: columnStyle } );

	return ( <>
			<InspectorControls>
				<PanelBody title={ __( 'Wrapper settings', 'flexible-invoices' ) }>
					<ColorSelector
						label={ __( 'Background color', 'flexible-invoices' ) }
						value={ backgroundColor }
						returnFormat={ 'rgb' }
						onChange={ ( value ) => setAttributes( { backgroundColor: value } ) }
					/>
					<DuoInput
						label={ __( 'Margin', 'flexible-invoices' ) }
						values={ { marginTop, marginBottom } }
						onChange={ ( newValues ) => setAttributes( { ...attributes, ...newValues } ) }
					/>
					<DuoInput
						label={ __( 'Padding', 'flexible-invoices' ) }
						values={ { paddingTop, paddingBottom } }
						onChange={ ( newValues ) => setAttributes( { ...attributes, ...newValues } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div  { ...blockProps } >
				&nbsp; {/*Add empty space so mpdf doesnt ignore it*/ }
				<InnerBlocks
					templateLock={ false }
					allowedBlocks={ true }
					renderAppender={ () => <InnerBlocks.ButtonBlockAppender/> }
				/>
			</div>
		</>
	);
};

export default Edit;
