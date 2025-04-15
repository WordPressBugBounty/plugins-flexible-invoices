export const validateCountryVatNumberFormat = ( country, vatNumber ) => {
	const regexExpressions = {
		AT: /^(AT)?U[A-Z\d]{8}$/,
		BE: /^(BE)?0\d{9}$/,
		BG: /^(BG)?\d{9,10}$/,
		CY: /^(CY)?\d{8}[A-Z]$/,
		CZ: /^(CZ)?\d{8,10}$/,
		DE: /^(DE)?\d{9}$/,
		DK: /^(DK)?(\d{2} ?){3}\d{2}$/,
		EE: /^(EE)?\d{9}$/,
		EL: /^(EL)?\d{9}$/,
		ES: /^(ES)?[A-Z]\d{7}[A-Z]|\d{8}[A-Z]|[A-Z]\d{8}$/,
		FI: /^(FI)?\d{8}$/,
		FR: /^(FR)?([A-Z]{2}|[A-Z0-9]{2})?\d{9}$/,
		XI: /^(XI)?\d{9}|\d{12}|(GD|HA)\d{3}$/,
		HR: /^(HR)?\d{11}$/,
		HU: /^(HU)?\d{8}$/,
		IE: /^(IE)?[A-Z\d]{8,10}$/,
		IT: /^(IT)?\d{11}$/,
		LT: /^(LT)?(\d{9}|\d{12})/,
		LU: /^(LU)?\d{8}$/,
		LV: /^(LV)?\d{11}$/,
		MT: /^(MT)?\d{8}$/,
		NL: /^(NL)?\d{9}B\d{2}$/,
		PL: /^(PL)?\d{10}$/,
		PT: /^(PT)?\d{9}$/,
		RO: /^(RO)?\d{2,10}$/,
		SE: /^(SE)?\d{12}$/,
		SI: /^(SI)?\d{8}$/,
		SK: /^(SK)?\d{10}$/,
	};

	if( regexExpressions[ country ] ) {
		let regex = regexExpressions[ country ];
		if( country === 'PL' ) {
			return regex.test( vatNumber ) && isValidPlVatNumber( vatNumber );
		}
		return regex.test( vatNumber );
	}
	// Return always true for other countries.
	return true;

}

function isValidPlVatNumber( number ) {
	const arrSteps = [ 6, 5, 7, 2, 3, 4, 5, 6, 7 ];
	let intSum     = 0;

	for( let i = 0; i < 9; i++ ) {
		intSum += arrSteps[ i ] * number[ i ];
	}

	let int          = intSum % 11;
	let intControlNr = int === 10 ? 0 : int;

	return intControlNr === parseInt( number[ 9 ] );
}

export const ExemplarTaxNumber = ( country ) => {
	switch ( country ) {
		case 'AT':
			return 'ATU12345678';
		case 'BE':
			return 'BE123456789 / BE0123456789';
		case 'BG':
			return 'BG123456789 / BG0123456789';
		case 'CY':
			return 'CY12345678X';
		case 'CZ':
			return 'CZ12345678 / CZ123456789 / CZ1234567890';
		case 'DE':
			return 'DE123456789';
		case 'DK':
			return 'DK12345678';
		case 'EE':
			return 'EE123456789';
		case 'GR':
		case 'EL':
			return 'EL123456789';
		case 'ES':
			return 'ESX12345678 / ES12345678X / ESX1234567X';
		case 'EU':
			return 'EU123456789';
		case 'FI':
			return 'FI12345678';
		case 'FR':
			return 'FR12345678901 / FRX1234567890 / FR1X123456789 / FRXX123456789';
		case 'GB':
			return 'GB123456789';
		case 'HR':
			return 'HR12345678901';
		case 'HU':
			return 'HU12345678';
		case 'IE':
			return 'IE1234567WA / IE1234567FA';
		case 'IT':
			return 'IT12345678901';
		case 'LV':
			return 'LV12345678901';
		case 'LT':
			return 'LT123456789 / LT123456789012 ';
		case 'LU':
			return 'LU12345678';
		case 'MT':
			return 'MT12345678';
		case 'NL':
			return 'NL123456789B01';
		case 'NO':
			return 'NO123456789';
		case 'PL':
			return 'PL1234567890';
		case 'PT':
			return 'PT123456789';
		case 'RO':
			return 'RO1234567890';
		case 'RS':
			return 'RS123456789';
		case 'SI':
			return 'SI12345678';
		case 'SK':
			return 'SK1234567890';
		case 'SE':
			return 'SE123456789012';
		default:
			return '';
	}
}

/**
 * Given some block attributes, gets attributes from the dataset or uses defaults.
 *
 * @param {Object} blockAttributes Object containing block attributes.
 * @param {Array}  rawAttributes   Dataset from DOM.
 * @return {Array} Array of parsed attributes.
 */
export const getValidBlockAttributes = ( blockAttributes, rawAttributes ) => {
	const attributes = [];

	Object.keys( blockAttributes ).forEach( ( key ) => {
		if( typeof rawAttributes[ key ] !== 'undefined' ) {
			switch ( blockAttributes[ key ].type ) {
				case 'boolean':
					attributes[ key ] =
						rawAttributes[ key ] !== 'false' &&
						rawAttributes[ key ] !== false;
					break;
				case 'number':
					attributes[ key ] = parseInt( rawAttributes[ key ], 10 );
					break;
				case 'array':
				case 'object':
					attributes[ key ] = JSON.parse( rawAttributes[ key ] );
					break;
				default:
					attributes[ key ] = rawAttributes[ key ];
					break;
			}
		} else {
			attributes[ key ] = blockAttributes[ key ].default;
		}
	} );

	return attributes;
}

/**
 * HOC that filters given attributes by valid block attribute values, or uses defaults if undefined.
 *
 * @param {Object} blockAttributes Component being wrapped.
 */
export const withFilteredAttributes = ( blockAttributes ) => ( OriginalComponent ) => {
	return ( ownProps ) => {
		const validBlockAttributes = getValidBlockAttributes(
			blockAttributes,
			ownProps
		);

		return (
			<OriginalComponent
				{ ...ownProps }
				{ ...validBlockAttributes }
			/>
		);
	};
};
