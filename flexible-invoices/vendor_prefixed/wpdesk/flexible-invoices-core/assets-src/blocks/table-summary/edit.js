import { __ } from "@wordpress/i18n";
import { FlexibleTable } from "@wpdesk/wordpress-ui-components";

export default function Edit( { attributes, setAttributes } ) {

	const placeholders = [
		{ label: '{SummaryTotalNet}', description: __( 'Total net amount', 'flexible-invoices' ) },
		{ label: '{SummaryTotalTax}', description: __( 'Total amount of tax', 'flexible-invoices' ) },
		{ label: '{SummaryTotalGross}', description: __( 'Total gross amount', 'flexible-invoices' ) },
		{ label: '{SummaryTaxNet}', description: __( 'Net amount of tax', 'flexible-invoices' ) },
		{ label: '{SummaryTaxName}', description: __( 'Name of tax', 'flexible-invoices' ) },
		{ label: '{SummaryTax}', description: __( 'Amount of tax', 'flexible-invoices' ) },
		{ label: '{SummaryTaxGross}', description: __( 'Amount of tax gross', 'flexible-invoices' ) },
	];
	return (
		<FlexibleTable
			attributes={attributes}
			setAttributes={setAttributes}
			placeholders={placeholders}
		/>
	);
}
