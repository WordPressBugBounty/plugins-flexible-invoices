import { useBlockProps, InnerBlocks, } from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {
	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			placeholder: __( 'Payment Method: {PaymentMethod}', 'flexible-invoices' ),
			content: __( 'Payment Method: {PaymentMethod}', 'flexible-invoices' )
		} ],
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-payment"});
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
