<?php
/**
 * This class is defined only for backward compatibility.
 */
if ( ! class_exists( 'InvoicePost' ) ) {
	class InvoicePost {

		public static $instance;

		private function __construct() {
		}

		public static function getInstances() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			if ( self::$instance == false ) { //phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
				self::$instance = new InvoicePost();
			}

			return self::$instance;
		}
	}
}
