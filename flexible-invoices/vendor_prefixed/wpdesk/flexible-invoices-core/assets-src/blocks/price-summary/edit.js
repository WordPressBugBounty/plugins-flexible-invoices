import {
	InnerBlocks,
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {
	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			placeholder: __( 'Total: {TotalAmount}', 'flexible-invoices' ),
			content: __( 'Total: {TotalAmount}', 'flexible-invoices' )
		} ],
		[ 'flexible-invoices/text', {
			placeholder: __( 'Paid: {PaidAmount}', 'flexible-invoices' ),
			content: __( 'Paid: {PaidAmount}', 'flexible-invoices' )
		} ],
		[ 'flexible-invoices/text', {
			placeholder: __( 'Due: {DueAmount}', 'flexible-invoices' ),
			content: __( 'Due: {DueAmount}', 'flexible-invoices' )
		} ],
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-price-summary"});
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
