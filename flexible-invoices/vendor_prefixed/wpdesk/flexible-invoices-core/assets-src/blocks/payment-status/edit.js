import { useBlockProps, InnerBlocks, } from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {
	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			placeholder: __( 'Payment status: {PaymentStatus}', 'flexible-invoices' ),
			content: __( 'Payment status: {PaymentStatus}', 'flexible-invoices' )
		} ],
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-payment-status"});
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
