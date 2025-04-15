import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from "@wordpress/i18n";
import { useEffect } from "react";
import { ColorSelector, DuoInput } from "@wpdesk/wordpress-ui-components";

const Edit = ( { attributes, setAttributes } ) => {

	const {
		columns,
		marginTop,
		marginBottom,
		paddingTop,
		paddingBottom,
		backgroundColor,
	} = attributes;

	useEffect( () => {
		setAttributes( { columnAmount: columns } );
	}, [columns, attributes.columnAmount, setAttributes] );

	const TEMPLATE = Array(columns).fill(["flexible-invoices/wrapper"]);


	const blockStyle = {
		display: 'block',
		overflow: 'hidden',
		backgroundColor,
		marginTop,
		marginBottom,
		paddingTop,
		paddingBottom
	};


	const blockProps = useBlockProps( { className: 'fi-row', style: blockStyle } );


	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( "Row settings", 'flexible-invoices' ) }>
					<RangeControl
						label={ __( "Columns", 'flexible-invoices' ) }
						value={ columns }
						onChange={ ( value ) => setAttributes( { columns: value, columnAmount: value } ) }
						min={ 1 }
						max={ 6 }
					/>
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
			<div { ...blockProps }>
				<InnerBlocks
					columns={ columns }
					template={ TEMPLATE }
					templateLock="all"
					allowedBlocks={ [ "flexible-invoices/wrapper" ] }
				/>

			</div>

		</>
	);
};

export default Edit;
