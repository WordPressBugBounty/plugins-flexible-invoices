import {
	InnerBlocks,
	useBlockProps,
} from '@wordpress/block-editor';

export default function Edit(  ) {
	const TEMPLATE = [
				[ 'flexible-invoices/text', { content: '{Signature}', placeholder: '{Signature}' } ],
			];


	const blockProps = useBlockProps( {className:"fi-text-group fi-block-signature"});
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
