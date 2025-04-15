/**
 * External dependencies
 */
import { useState, useEffect, useCallback } from '@wordpress/element';
import { ValidatedTextInput, CheckboxControl } from '@woocommerce/blocks-checkout';
import { withInstanceId } from '@wordpress/compose';
import { extensionCartUpdate } from '@woocommerce/blocks-checkout';
import { getSetting } from '@woocommerce/settings';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { validateCountryVatNumberFormat, ExemplarTaxNumber } from '../utils';
import { IpAddressNotice } from '../ip-address-notice';
import FormStep from '../form-step';
import { VatNotice } from "../vat-notice";

const VatInput = ( props ) => {

	const { checkoutExtensionData }                                         = props;
	const { setValidationErrors, clearValidationError, getValidationError } = props.validation;

	const { cart, extensions }                                                                                 = props;
	const { billingAddress }                                                                                   = cart;
	const { title = __( 'VAT Number', 'flexible-invoices-woocommerce' ), description = '', showStepNumber = true } = props;

	const Settings         = getSetting( 'woocommerce-eu-vat-number_data' );
	const shouldValidateIp = Settings.validate_ip_country === 'yes';
	const IsEuVatEnabled   = Settings.is_eu_vat_enabled === 'yes';

	const textInputId = 'billing_vat_number';
	const className   = 'eu-vat-extra-css';

	const validationErrorId = 'billing_vat_number_error';
	const validationError   = getValidationError( validationErrorId );

	const showAskField                = Settings.generate_invoice === 'n' || Settings.generate_invoice === 'p';
	const [ isDirty, setIsDirty ]     = useState( false );
	const [ isChecked, setChecked ]   = useState( extensions[ 'woocommerce-eu-vat-number' ]?.billing_invoice_ask );
	const [ vat, setVat ]             = useState( extensions[ 'woocommerce-eu-vat-number' ]?.billing_vat_number );
	const [ available, setAvailable ] = useState( Settings.eu_countries.indexOf( billingAddress.country ) !== -1 );

	// set initial values.
	useEffect( () => {
		setChecked( extensions[ 'woocommerce-eu-vat-number' ]?.billing_invoice_ask );
		setVat( extensions[ 'woocommerce-eu-vat-number' ]?.billing_vat_number );
	}, [ extensions ] );

	useEffect( () => {
		checkoutExtensionData.setExtensionData( 'woocommerce-eu-vat-number', 'billing_invoice_ask', false );
		checkoutExtensionData.setExtensionData( 'woocommerce-eu-vat-number', 'vat_confirmation', false );
		checkoutExtensionData.setExtensionData( 'woocommerce-eu-vat-number', 'location_confirmation', false );
	}, [ checkoutExtensionData.setExtensionData ] );

	useEffect( () => {
		setAvailable( Settings.eu_countries.indexOf( billingAddress.country ) !== -1 );
	}, [ Settings.eu_countries, billingAddress.country ] );

	useEffect( () => {
		const has_error = extensions[ 'woocommerce-eu-vat-number' ]?.validation.error;
		if( has_error ) {
			setValidationErrors( {
				[ validationErrorId ]: {
					message: has_error,
					hidden: false,
				},
			} );
		}
	}, [ extensions, setValidationErrors, clearValidationError, vat ] );

	const verifySimpleVat = () => {
		let is_valid_format = validateCountryVatNumberFormat( billingAddress.country, vat );
		if( vat && ! is_valid_format ) {
			const validationErrorMessage = sprintf(
				__(
					'The entered VAT number (%s) is incorrect. Use the following format: %s',
					'woocommerce-eu-vat-number'
				),
				vat,
				ExemplarTaxNumber( billingAddress.country )
			);

			setValidationErrors( {
				[ validationErrorId ]: {
					message: validationErrorMessage,
					hidden: false,
				},
			} );
		}

		extensionCartUpdate( {
			namespace: 'woocommerce-eu-vat-number',
			data: {
				billing_vat_number: vat,
				validate_oss: false,
				billing_invoice_ask: isChecked,
			},
			cartPropsToReceive: [ 'extensions' ],
		} ).then( () => {
			setVat( vat );
		} );
	}

	const verifyEuVat = () => {
		let is_valid_format = validateCountryVatNumberFormat( billingAddress.country, vat );
		if( vat && ! is_valid_format ) {
			const validationErrorMessage = sprintf(
				__(
					'The entered VAT number (%s) is incorrect. Use the following format: %s',
					'woocommerce-eu-vat-number'
				),
				vat,
				ExemplarTaxNumber( billingAddress.country )
			);

			setValidationErrors( {
				[ validationErrorId ]: {
					message: validationErrorMessage,
					hidden: false,
				},
			} );
		}

		extensionCartUpdate( {
			namespace: 'woocommerce-eu-vat-number',
			data: {
				billing_vat_number: vat,
				validate_oss: available,
				billing_invoice_ask: isChecked,
			},
			cartPropsToReceive: [ 'extensions' ],
		} ).then( () => {
			setVat( vat );
			if( ! available && ! vat ) {
				clearValidationError( validationErrorId );
			}
		} );
	};

	const is_valid_vat = extensions[ 'woocommerce-eu-vat-number' ]?.validation.valid;

	useEffect( () => {
		const validationErrorMessage = sprintf(
			__(
				'The entered VAT number (%s) is incorrect. Use the following format: %s',
				'woocommerce-eu-vat-number'
			),
			vat,
			ExemplarTaxNumber( billingAddress.country )
		);


		let is_valid_format          = validateCountryVatNumberFormat( billingAddress.country, vat );
		if( vat && ! is_valid_format ) {

			setValidationErrors( {
				[ validationErrorId ]: {
					message: validationErrorMessage,
					hidden: true,
				},
			} );
		} else {
			clearValidationError( validationErrorId );
		}

		if( vat && is_valid_vat ) {
			clearValidationError( validationErrorId );
		}
	}, [ setValidationErrors, clearValidationError, isChecked, vat, billingAddress, is_valid_vat ] );

	return (
		<FormStep
			id="shipping-fields"
			className={ classnames(
				'wc-block-checkout__shipping-fields',
				className
			) }
			title={ Settings.label }
			description={ description }
			showStepNumber={ showStepNumber }
		>
			<div>
				{ showAskField && (
					<CheckboxControl
						id="billing_invoice_ask"
						checked={ isChecked }
						onChange={ ( value ) => {
							setChecked( value );
							if( ! value ) {
								setVat( '' );
							}
							if( ! isDirty ) {
								setIsDirty( true );
							}
						} }
						label={ __( 'I want an invoice.', 'flexible-invoices-woocommerce' ) }
						value="1"
					/>
				) }
				{ ( isChecked || ! showAskField ) && (
					<>
						<ValidatedTextInput
							id={ textInputId }
							type="text"
							className={
								validationError?.hidden === false ? 'has-error' : ''
							}
							label={ Settings.label }
							value={ vat }
							onChange={ ( value ) => {
								setVat( value );
								if( ! isDirty ) {
									setIsDirty( true );
								}
							} }
							onBlur={ () => {
								available && IsEuVatEnabled ? verifyEuVat() : verifySimpleVat();
							} }
							customFormatter={ ( value ) => {
								return value.replace( /[^0-9A-Za-z]/g, "" )
							} }
						/>
						{ validationError?.message && ! validationError?.hidden ? (
							<div className="wc-block-components-validation-error" role="alert"><p>{ validationError.message }</p></div>
						) : null }
					</>
				) }

				{ ( ( isChecked || ! showAskField ) && available && IsEuVatEnabled && ( ! vat && shouldValidateIp ) ) ? (
					<IpAddressNotice
						validation={ props.validation }
						ipAddress={ Settings.ip_address }
						ipCountry={ Settings.ip_country }
						billingCountry={ billingAddress.country }
						shouldValidateIp={ shouldValidateIp }
						checkoutExtensionData={ checkoutExtensionData }
					/>
				) : null }

				{ ( ( isChecked || ! showAskField ) && available && IsEuVatEnabled && ( vat && ! is_valid_vat ) ) ? (
					<VatNotice
						validation={ props.validation }
						checkoutExtensionData={ checkoutExtensionData }
					/>
				) : null }
			</div>
		</FormStep>
	);
};

export default withInstanceId( VatInput );
