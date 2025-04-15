import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ( { attributes } ) => {

	const {
		columns,
		marginTop,
		marginBottom,
		backgroundColor,
		paddingTop,
		paddingBottom
	} = attributes;

	const blockStyle = {
		display: 'block',
		overflow: 'hidden',
		marginTop,
		marginBottom,
		backgroundColor,
		paddingTop,
		paddingBottom
	};

	const blockProps = useBlockProps.save( {
		'data-columns': columns,
		className: 'fi-row',
		style: blockStyle
	} );

	return (
		<div { ...blockProps } >
			<InnerBlocks.Content/>
		</div>
	);
};

export default Save;
