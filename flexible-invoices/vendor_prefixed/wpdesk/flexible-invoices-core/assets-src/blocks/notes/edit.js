import {
	InnerBlocks, useBlockProps,
} from '@wordpress/block-editor';

export default function Edit() {

	const TEMPLATE = [ [ 'flexible-invoices/text', { placeholder: '{Notes}', content: '{Notes}' } ], ];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-notes"});
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
