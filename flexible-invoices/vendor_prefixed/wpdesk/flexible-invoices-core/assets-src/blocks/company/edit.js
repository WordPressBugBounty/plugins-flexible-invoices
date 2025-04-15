import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { __ } from "@wordpress/i18n";

export default function Edit() {

	const TEMPLATE = [
		[ 'flexible-invoices/text', {
			fontSize: '1.3em',
			fontWeight: 'bold',
			textColor: '#333',
			placeholder: __( 'Seller', 'flexible-invoices' ),
			content: __( 'Seller', 'flexible-invoices' )
		} ],
		[ 'flexible-invoices/text', { placeholder: '{CompanyName}', content: '{CompanyName}' } ],
		[ 'flexible-invoices/text', { placeholder: '{CompanyAddress}', content: '{CompanyAddress}' } ],
		[ 'flexible-invoices/text', { placeholder: '{CompanyVAT}', content: '{CompanyVAT}' } ],
		[ 'flexible-invoices/text', { placeholder: '{CompanyBankAccount}', content: '{CompanyBankAccount}' } ],
		[ 'flexible-invoices/text', { placeholder: '{CompanyBankName}', content: '{CompanyBankName}' } ],
	];


	const blockProps = useBlockProps( {className:"fi-text-group fi-block-company"});
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
