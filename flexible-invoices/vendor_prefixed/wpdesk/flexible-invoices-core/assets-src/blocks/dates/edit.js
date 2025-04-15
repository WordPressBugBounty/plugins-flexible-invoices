import {
	InnerBlocks,
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {

	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			placeholder: __( 'Issue Date: {IssueDate}', 'flexible-invoices' ),
			content: __( 'Issue Date: {IssueDate}', 'flexible-invoices' )
		} ],
		[ 'flexible-invoices/text', {
			placeholder: __( 'Payment Date: {PayDate}', 'flexible-invoices' ),
			content: __( 'Payment Date: {PayDate}', 'flexible-invoices' )
		} ],
		[ 'flexible-invoices/text', {
			placeholder: __( 'Sale Date: {SaleDate}', 'flexible-invoices' ),
			content: __( 'Sale Date: {SaleDate}', 'flexible-invoices' )
		} ]
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-dates"});
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
