/**
 * External dependencies
 */
import { sprintf, __ } from '@wordpress/i18n';
import { CheckboxControl } from '@woocommerce/blocks-checkout';
import {
	useState,
	createInterpolateElement,
	useEffect,
} from '@wordpress/element';
import { getSetting } from '@woocommerce/settings';

/**
 * Renders a warning when the customer's IP address does not match the billing country they chose.
 *
 * @param {Object} props Incoming props for the component.
 * @param {Object} props.validation Object containing WooCommerce Blocks validation methods.
 * @param {Object} props.checkoutExtensionData Object containing setCheckoutData to allow us to pass data to the checkout endpoint.
 * @return {JSX.Element|null} The component to render, or null if there's nothing to render.
 */
export const VatNotice = ( { validation, checkoutExtensionData, } ) => {
	const { setExtensionData } = checkoutExtensionData;


	const { setValidationErrors, clearValidationError, getValidationError } = validation;

	const [ isChecked, setIsChecked ] = useState( false );
	const [ isDirty, setIsDirty ]     = useState( false );
	const validationErrorId           = 'billing_vat_number_tax_notice';

	const validationErrorMessage = __( 'If your company is already registered for EU VAT check this field.', 'flexible-invoices-woocommerce' );

	useEffect( () => {
		if( ! isChecked ) {
			setValidationErrors( {
				[ validationErrorId ]: {
					message: validationErrorMessage,
					hidden: ! isDirty,
				},
			} );
		}
		if( isChecked ) {
			clearValidationError( validationErrorId );
		}

		// When unmounting we need to clear the error to allow checkout to continue.
		return () => {
			clearValidationError( 'billing_vat_number_error' );
			clearValidationError( validationErrorId );
		};
	}, [ setValidationErrors, isChecked, isDirty ] );


	const validationError = getValidationError( validationErrorId );

	return (
		<div className="wc-eu-vat-checkout-vat-notice">
			<div className="wc-eu-vat-checkout-vat-notice__checkbox-container">
				<CheckboxControl
					name={ 'location_confirmation' }
					className={
						validationError?.hidden === false ? 'has-error' : ''
					}
					label={ __(	'I am already registered in VAT EU','flexible-invoices-woocommerce') }
					checked={ isChecked }
					onChange={ ( checked ) => {
						if( ! isDirty ) {
							setIsDirty( true );
						}
						setIsChecked( checked );
						setExtensionData(
							'woocommerce-eu-vat-number',
							'location_confirmation',
							checked
						);
					} }
				/>
			</div>
			{ validationError?.message && ! validationError?.hidden ? (
				<div className="wc-block-components-validation-error" role="alert">
					<p>
						{ validationError.message }
					</p>
				</div>
			) : null }
		</div>
	);
};
