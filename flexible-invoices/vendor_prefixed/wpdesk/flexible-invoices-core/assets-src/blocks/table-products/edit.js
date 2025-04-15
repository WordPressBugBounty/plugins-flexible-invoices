import { __ } from "@wordpress/i18n";
import { FlexibleTable } from "@wpdesk/wordpress-ui-components";

export default function Edit( { attributes, setAttributes } ) {

	const placeholders = [
		{ label: '{Number}', description: __( 'Order number on the invoice', 'flexible-invoices' ) },
		{ label: '{ProductName}', description: __( 'Product name', 'flexible-invoices' ) },
		{ label: '{ProductUnit}', description: __( 'Product unit', 'flexible-invoices' ) },
		{ label: '{ProductSKU}', description: __( 'Product SKU', 'flexible-invoices' ) },
		{ label: '{ProductDiscount}', description: __( 'Product discount', 'flexible-invoices' ) },
		{ label: '{ProductQty}', description: __( 'Quantity of product', 'flexible-invoices' ) },
		{ label: '{ProductNetPrice}', description: __( 'Net price of one unit of product', 'flexible-invoices' ) },
		{ label: '{ProductNetAmount}', description: __( 'Total net price for the product', 'flexible-invoices' ) },
		{ label: '{ProductTaxRate}', description: __( 'Tax rate', 'flexible-invoices' ) },
		{
			label: '{ProductTaxAmount}',
			description: __( 'Total amount of tax for the product', 'flexible-invoices' )
		},
		{ label: '{ProductGrossAmount}', description: __( 'Gross price', 'flexible-invoices' ) },
	];

	return (
		<FlexibleTable
			attributes={ attributes }
			setAttributes={ setAttributes }
			placeholders={ placeholders }
		/>
	);
}
