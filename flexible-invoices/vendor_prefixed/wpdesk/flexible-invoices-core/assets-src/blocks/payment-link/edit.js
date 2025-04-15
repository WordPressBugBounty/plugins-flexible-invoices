import { useBlockProps, InnerBlocks, } from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {
	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			placeholder: __( 'Pay for this order', 'flexible-invoices' ),
			content: __( 'Pay for this order', 'flexible-invoices' )
		} ],
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-payment-link"});
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
