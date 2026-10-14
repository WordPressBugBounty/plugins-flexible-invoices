const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
const path = require( 'path' );

const wcDepMap = {
	'@woocommerce/blocks-registry': [ 'wc', 'wcBlocksRegistry' ],
	'@woocommerce/settings': [ 'wc', 'wcSettings' ],
	'@woocommerce/blocks-checkout': [ 'wc', 'blocksCheckout' ],
	'@woocommerce/block-data': [ 'wc', 'wcBlocksData' ],
};

const wcHandleMap = {
	'@woocommerce/blocks-registry': 'wc-blocks-registry',
	'@woocommerce/settings': 'wc-settings',
	'@woocommerce/blocks-checkout': 'wc-blocks-checkout',
	'@woocommerce/block-data': 'wc-blocks-data-store',
};

const requestToExternal = ( request ) => {
	if ( wcDepMap[ request ] ) {
		return wcDepMap[ request ];
	}
};

const requestToHandle = ( request ) => {
	if ( wcHandleMap[ request ] ) {
		return wcHandleMap[ request ];
	}
};

module.exports = {
	...defaultConfig,
	entry: {
		'frontend': './assets-src/frontend.tsx',
		'editor': './assets-src/editor.tsx',
		'checkout': './assets-src/checkout.js',
	},
	output: {
		path: path.resolve( __dirname, 'assets/js' ),
		filename: '[name].js',
	},
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin( {
			requestToExternal,
			requestToHandle
		} )
	]
};
