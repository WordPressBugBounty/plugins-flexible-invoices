<?php

namespace WPDeskFIVendor\WPDesk\Forms;

interface Escaper
{
    /** @param mixed $value */
    public function escape($value): string;
}
