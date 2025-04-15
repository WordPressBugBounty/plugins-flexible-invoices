import {
	InnerBlocks,
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {

	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			content: __( 'Recipient', 'flexible-invoices' ),
			placeholder: __( 'Recipient', 'flexible-invoices' ),
			fontSize: '1.3em',
			fontWeight: 'bold',
			textColor: '#333'
		} ],
		[ 'flexible-invoices/text', { content: '{RecipientName}', placeholder: '{RecipientName}', textColor: '#222' } ],
		[ 'flexible-invoices/text', {
			content: '{RecipientStreet}',
			placeholder: '{RecipientStreet}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{RecipientPostalCode}, {RecipientCity}',
			placeholder: '{RecipientPostalCode}, {RecipientCity}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{RecipientVAT}',
			placeholder: '{RecipientVAT}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{RecipientCountry}',
			placeholder: '{RecipientCountry}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{RecipientPhone}',
			placeholder: '{RecipientPhone}',
			textColor: '#222'
		} ],
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-recipient"});
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
