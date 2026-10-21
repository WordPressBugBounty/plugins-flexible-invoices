<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
/**
 * Validates the length and format of a VAT number using country-specific regular expressions.
 */
class LengthValidation implements VatValidator
{
    /**
     * @inheritDoc
     */
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        //phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
        $stored_vat_number = strtoupper(trim($vat_number->get_raw()));
        if ('' === $stored_vat_number) {
            return ValidationResult::success();
        }
        if (!$country instanceof Country) {
            return ValidationResult::failure(ValidationResult::ERROR_COUNTRY_REQUIRED, __('Country is required for VAT validation.', 'flexible-invoices'));
        }
        switch ($country->get_code()) {
            case 'AT':
                // AUSTRIA.
                $is_valid = (bool) preg_match('/^(AT)U(\d{8})$/', $stored_vat_number);
                break;
            case 'BE':
                // BELGIUM.
                $is_valid = (bool) preg_match('/^(BE)(0\d{9}|1\d{9})$/', $stored_vat_number);
                break;
            case 'BG':
                // BULGARIA.
                $is_valid = (bool) preg_match('/^(BG)(\d{9,10})$/', $stored_vat_number);
                break;
            case 'CHE':
                // Switzerland.
                $is_valid = (bool) preg_match('/^(CHE)(\d{9})(MWST)?$/', $stored_vat_number);
                break;
            case 'CY':
                // CYPRUS.
                $is_valid = (bool) preg_match('/^(CY)(\d{8}[A-Z])$/', $stored_vat_number);
                break;
            case 'CZ':
                // CZECH REPUBLIC.
                $is_valid = (bool) preg_match('/^(CZ)(\d{8,10})(\d{3})?$/', $stored_vat_number);
                break;
            case 'DE':
                // GERMANY.
                $is_valid = (bool) preg_match('/^(DE)([1-9]\d{8})$/', $stored_vat_number);
                break;
            case 'DK':
                // DENMARK.
                $is_valid = (bool) preg_match('/^(DK)(\d{8})$/', $stored_vat_number);
                break;
            case 'EE':
                // ESTONIA.
                $is_valid = (bool) preg_match('/^(EE)(10\d{7})$/', $stored_vat_number);
                break;
            case 'GR':
            case 'EL':
                // GREECE.
                $is_valid = (bool) preg_match('/^(EL)(\d{9})$/', $stored_vat_number);
                break;
            case 'ES':
                // SPAIN.
                $is_valid = preg_match('/^(ES)([A-Z]\d{8})$/', $stored_vat_number) || preg_match('/^(ES)([A-H|N-S|W]\d{7}[A-J])$/', $stored_vat_number) || preg_match('/^(ES)([0-9|Y|Z]\d{7}[A-Z])$/', $stored_vat_number) || preg_match('/^(ES)([K|L|M|X]\d{7}[A-Z])$/', $stored_vat_number);
                break;
            case 'EU':
                // EU type.
                $is_valid = (bool) preg_match('/^(EU)(\d{9})$/', $stored_vat_number);
                break;
            case 'FI':
                // FINLAND.
                $is_valid = (bool) preg_match('/^(FI)(\d{8})$/', $stored_vat_number);
                break;
            case 'FR':
                // FRANCE.
                $is_valid = preg_match('/^(FR)(\d{11})$/', $stored_vat_number) || preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]\d{10})$/', $stored_vat_number) || preg_match('/^(FR)(\d[(A-H)|(J-N)|(P-Z)]\d{9})$/', $stored_vat_number) || preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]{2}\d{9})$/', $stored_vat_number);
                break;
            case 'GB':
                // GREAT BRITAIN.
                $is_valid = (bool) preg_match('/^(GB)?(\d{9})$/', $stored_vat_number) || preg_match('/^(GB)?(\d{12})$/', $stored_vat_number) || preg_match('/^(GB)?(GD\d{3})$/', $stored_vat_number) || preg_match('/^(GB)?(HA\d{3})$/', $stored_vat_number);
                break;
            case 'HR':
                // CROATIA.
                $is_valid = (bool) preg_match('/^(HR)(\d{11})$/', $stored_vat_number);
                break;
            case 'HU':
                // HUNGARY.
                $is_valid = (bool) preg_match('/^(HU)(\d{8})$/', $stored_vat_number);
                break;
            case 'IE':
                // IRELAND.
                $is_valid = preg_match('/^(IE)(\d{7}[A-W])$/', $stored_vat_number) || preg_match('/^(IE)([7-9][A-Z\*\+)]\d{5}[A-W])$/', $stored_vat_number) || preg_match('/^(IE)(\d{7}[A-W][AH])$/', $stored_vat_number);
                break;
            case 'IT':
                // ITALY.
                $is_valid = (bool) preg_match('/^(IT)(\d{11})$/', $stored_vat_number);
                break;
            case 'LV':
                // LATVIA.
                $is_valid = (bool) preg_match('/^(LV)(\d{11})$/', $stored_vat_number);
                break;
            case 'LT':
                // LITHUNIA.
                $is_valid = (bool) preg_match('/^(LT)(\d{9}|\d{12})$/', $stored_vat_number);
                break;
            case 'LU':
                // LUXEMBOURG.
                $is_valid = (bool) preg_match('/^(LU)(\d{8})$/', $stored_vat_number);
                break;
            case 'MT':
                // MALTA.
                $is_valid = (bool) preg_match('/^(MT)([1-9]\d{7})$/', $stored_vat_number);
                break;
            case 'NL':
                // NETHERLAND.
                $is_valid = (bool) preg_match('/^(NL)(\d{9})B\d{2}$/', $stored_vat_number);
                break;
            case 'NO':
                // NORWAY.
                $is_valid = (bool) preg_match('/^(NO)(\d{9})$/', $stored_vat_number);
                break;
            case 'PL':
                // POLAND.
                $is_valid = (bool) preg_match('/^(PL)?(\d{10})$/', $stored_vat_number);
                break;
            case 'PT':
                // PORTUGAL.
                $is_valid = (bool) preg_match('/^(PT)(\d{9})$/', $stored_vat_number);
                break;
            case 'RO':
                // ROMANIA.
                $is_valid = (bool) preg_match('/^(RO)([1-9]\d{1,9})$/', $stored_vat_number);
                break;
            case 'RS':
                // SERBIA.
                $is_valid = (bool) preg_match('/^(RS)(\d{9})$/', $stored_vat_number);
                break;
            case 'SI':
                // SLOVENIA.
                $is_valid = (bool) preg_match('/^(SI)([1-9]\d{7})$/', $stored_vat_number);
                break;
            case 'SK':
                // SLOVAK REPUBLIC.
                $is_valid = (bool) preg_match('/^(SK)([1-9]\d[(2-4)|(6-9)]\d{7})$/', $stored_vat_number);
                break;
            case 'SE':
                // SWEDEN.
                $is_valid = (bool) preg_match('/^(SE)(\d{10}01)$/', $stored_vat_number);
                break;
            default:
                $is_valid = \true;
        }
        if ($is_valid) {
            return ValidationResult::success();
        }
        return ValidationResult::failure(ValidationResult::ERROR_INVALID_LENGTH, $this->get_invalid_format_message($country->get_code()));
    }
    private function get_invalid_format_message(string $country_code): string
    {
        $example = $this->get_format_example($country_code);
        if ('' === $example) {
            return __('This VAT number is not in the correct format. Check the number of digits and try again.', 'flexible-invoices');
        }
        if ($this->country_requires_vat_prefix($country_code)) {
            return sprintf(
                /* translators: %s: example VAT number including the country prefix */
                __('This VAT number is not in the correct format. Include the country prefix, for example %s.', 'flexible-invoices'),
                $example
            );
        }
        return sprintf(
            /* translators: %s: example VAT number without a country prefix */
            __('This VAT number is not in the correct format. Enter it as issued, for example %s.', 'flexible-invoices'),
            $example
        );
    }
    private function country_requires_vat_prefix(string $country_code): bool
    {
        return !in_array($country_code, ['PL', 'GB'], \true);
    }
    private function get_format_example(string $country_code): string
    {
        $examples = ['AT' => 'ATU12345678', 'BE' => 'BE0123456789', 'BG' => 'BG123456789', 'CHE' => 'CHE123456789', 'CY' => 'CY12345678X', 'CZ' => 'CZ12345678', 'DE' => 'DE123456789', 'DK' => 'DK12345678', 'EE' => 'EE101234567', 'EL' => 'EL123456789', 'GR' => 'EL123456789', 'ES' => 'ESA12345678', 'EU' => 'EU123456789', 'FI' => 'FI12345678', 'FR' => 'FR12345678901', 'GB' => '123456789', 'HR' => 'HR12345678901', 'HU' => 'HU12345678', 'IE' => 'IE1234567T', 'IT' => 'IT12345678901', 'LV' => 'LV12345678901', 'LT' => 'LT123456789', 'LU' => 'LU12345678', 'MT' => 'MT12345678', 'NL' => 'NL123456789B01', 'NO' => 'NO123456789', 'PL' => '1234563218', 'PT' => 'PT123456789', 'RO' => 'RO123456789', 'RS' => 'RS123456789', 'SI' => 'SI12345678', 'SK' => 'SK2012345678', 'SE' => 'SE123456789001'];
        return $examples[$country_code] ?? '';
    }
}
