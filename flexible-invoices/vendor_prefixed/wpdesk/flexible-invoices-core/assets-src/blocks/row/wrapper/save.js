import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ( { attributes } ) => {

	const {
		columnAmount,
		backgroundColor,
		marginTop,
		marginBottom,
		paddingTop,
		paddingBottom
	} = attributes;

	let width = Math.floor( 100 / columnAmount );


	const blockProps = useBlockProps.save( {
		className: 'wp-block-flexible-invoices-wrapper fi-innerwrapper',
		style: {
			minHeight: '1px',
			float: 'left',
			width: `${ width }%`,
			backgroundColor: backgroundColor || '#ffffff00',
			marginTop,
			marginBottom,
			paddingTop,
			paddingBottom
		}
	} );

	return (
		<div { ...blockProps }>
			&nbsp; {/* Add empty space so mpdf doesnt ignore it */ }
			<InnerBlocks.Content/>
		</div>
	);
};

export default Save;
