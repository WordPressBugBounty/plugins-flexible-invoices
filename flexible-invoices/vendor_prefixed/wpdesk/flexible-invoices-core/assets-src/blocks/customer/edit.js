import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {

	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			content: __( 'Customer', 'flexible-invoices' ),
			placeholder: __( 'Customer', 'flexible-invoices' ),
			fontSize: '1.3em',
			fontWeight: 'bold',
			textColor: '#333'
		} ],
		[ 'flexible-invoices/text', { content: '{CustomerName}', placeholder: '{CustomerName}', textColor: '#222' } ],
		[ 'flexible-invoices/text', {
			content: '{CustomerStreet}',
			placeholder: '{CustomerStreet}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{CustomerPostalCode}, {CustomerCity}',
			placeholder: '{CustomerPostalCode}, {CustomerCity}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{CustomerCountry}',
			placeholder: '{CustomerPostalCode}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{CustomerVAT}',
			placeholder: '{CustomerVAT}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', {
			content: '{CustomerPhone}',
			placeholder: '{CustomerCountry}',
			textColor: '#222'
		} ],
		[ 'flexible-invoices/text', { content: '{CustomerEmail}', placeholder: '{CustomerPhone}', textColor: '#222' } ],
	];

	const blockProps = useBlockProps( {className:"fi-text-group fi-block-customer"});
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
