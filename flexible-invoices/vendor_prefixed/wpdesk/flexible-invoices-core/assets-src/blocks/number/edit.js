import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function Edit() {

	const TEMPLATE = [ [ 'flexible-invoices/text', {
		textAlign: 'center',
		fontWeight: 'bold',
		fontSize: '1.5em',
		placeholder: '{DocumentNumber}',
		content: '{DocumentNumber}'
	} ], ];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-number"});
	return (
		<>
			<div { ...blockProps } >
				<InnerBlocks
					template={ TEMPLATE }
					templateLock={ false }
					renderAppender={ () => <InnerBlocks.ButtonBlockAppender/> }
				/>
			</div>
		</>
	);
}
