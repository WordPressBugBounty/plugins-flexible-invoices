const defaultConfig                                = require( '@wordpress/scripts/config/webpack.config' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
const path        = require( 'path' );
const CopyWebpack = require( 'copy-webpack-plugin' );


const wcDepMap = {
	'@woocommerce/blocks-registry': [ 'wc', 'wcBlocksRegistry' ],
	'@woocommerce/settings': [ 'wc', 'wcSettings' ]
};

const wcHandleMap = {
	'@woocommerce/blocks-registry': 'wc-blocks-registry',
	'@woocommerce/settings': 'wc-settings'
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

// Export configuration.
module.exports = {
	...defaultConfig,
	externals: {
		"@wordpress/i18n": ["wp", "i18n"]
	},
	entry: {

		//Admin functions
		'admin': '/assets-src/js/admin.js',
		'checkout': '/assets-src/js/checkout.js',
		'jquery.tipTip': '/assets-src/js/jquery.tipTip.js',
		'products': '/assets-src/js/products.js',
		'products_calculate': '/assets-src/js/products_calculate.js',
		'select2.min': '/assets-src/js/select2.min.js',
		'select2-pl': '/assets-src/js/select2-pl.js',
		'settings': '/assets-src/js/settings.js',
		'template-settings': '/assets-src/js/template-settings.js',

		//Template post type toggle handler.
		'admin/template-toggle-manager': '/assets-src/admin/template-toggle-manager.js',

		//Block editor.
		'blocks/post-meta/template-settings/index': '/assets-src/sidebar/template-settings/sidebar-settings.js',
		'blocks/customer/index': '/assets-src/blocks/customer/index.js',
		'blocks/company/index': '/assets-src/blocks/company/index.js',
		'blocks/dates/index': '/assets-src/blocks/dates/index.js',
		'blocks/logo/index': '/assets-src/blocks/logo/index.js',
		'blocks/notes/index': '/assets-src/blocks/notes/index.js',
		'blocks/number/index': '/assets-src/blocks/number/index.js',
		'blocks/order-number/index': '/assets-src/blocks/order-number/index.js',
		'blocks/payment/index': '/assets-src/blocks/payment/index.js',
		'blocks/payment-link/index': '/assets-src/blocks/payment-link/index.js',
		'blocks/payment-status/index': './assets-src/blocks/payment-status/index.js',
		'blocks/price-summary/index': '/assets-src/blocks/price-summary/index.js',
		'blocks/recipient/index': '/assets-src/blocks/recipient/index.js',
		'blocks/signature/index': '/assets-src/blocks/signature/index.js',
		'blocks/table-products/index': '/assets-src/blocks/table-products/index.js',
		'blocks/table-summary/index': '/assets-src/blocks/table-summary/index.js',
		'blocks/table-of-conversion/index': '/assets-src/blocks/table-of-conversion/index.js',
		'blocks/text/index': '/assets-src/blocks/text/index.js',
		'blocks/row/index': '/assets-src/blocks/row/row/index.js',
		'blocks/wrapper/index': '/assets-src/blocks/row/wrapper/index.js',
	},
	module: {
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.(js|jsx)$/,
				use: {
					loader: "babel-loader",
					options: {
						presets: ["@babel/preset-env", "@babel/preset-react"]
					}
				}
		}
		]
	},
	output: {
		path: path.resolve( __dirname, 'assets/js' ),
		filename: '[name].js',
	},
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
			plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new CopyWebpack(
			{
				patterns: [
					{ from: 'assets-src/blocks/company/block.json', to: 'blocks/company/block.json' },
					{ from: 'assets-src/blocks/customer/block.json', to: 'blocks/customer/block.json' },
					{ from: 'assets-src/blocks/dates/block.json', to: 'blocks/dates/block.json' },
					{ from: 'assets-src/blocks/logo/block.json', to: 'blocks/logo/block.json' },
					{ from: 'assets-src/blocks/notes/block.json', to: 'blocks/notes/block.json' },
					{ from: 'assets-src/blocks/number/block.json', to: 'blocks/number/block.json' },
					{ from: 'assets-src/blocks/order-number/block.json', to: 'blocks/order-number/block.json' },
					{ from: 'assets-src/blocks/payment/block.json', to: 'blocks/payment/block.json' },
					{ from: 'assets-src/blocks/payment-link/block.json', to: 'blocks/payment-link/block.json' },
					{ from: "assets-src/blocks/payment-status/block.json", to: "blocks/payment-status/block.json" },
					{ from: 'assets-src/blocks/price-summary/block.json', to: 'blocks/price-summary/block.json' },
					{ from: 'assets-src/blocks/recipient/block.json', to: 'blocks/recipient/block.json' },
					{ from: 'assets-src/blocks/signature/block.json', to: 'blocks/signature/block.json' },
					{ from: 'assets-src/blocks/table-products/render.php', to: 'blocks/table-products/render.php' },
					{ from: 'assets-src/blocks/table-products/block.json', to: 'blocks/table-products/block.json' },
					{ from: 'assets-src/blocks/table-summary/block.json', to: 'blocks/table-summary/block.json' },
					{ from: 'assets-src/blocks/table-summary/render.php', to: 'blocks/table-summary/render.php' },
					{ from: 'assets-src/blocks/table-of-conversion/block.json', to: 'blocks/table-of-conversion/block.json' },
					{ from: 'assets-src/blocks/table-of-conversion/render.php', to: 'blocks/table-of-conversion/render.php' },
					{ from: 'assets-src/blocks/row/row/block.json', to: 'blocks/row/block.json' },
					{ from: 'assets-src/blocks/row/wrapper/block.json', to: 'blocks/wrapper/block.json' },
					{ from: 'assets-src/blocks/text/block.json', to: 'blocks/text/block.json' },
					{ from: 'assets-src/sidebar/template-settings/images/block-searching.gif', to: 'blocks/post-meta/template-settings/images/block-searching.gif'},
					{ from: 'assets-src/sidebar/template-settings/images/block-settings.gif', to: 'blocks/post-meta/template-settings/images/block-settings.gif'},
					{ from: 'assets-src/sidebar/template-settings/images/general-settings.gif', to: 'blocks/post-meta/template-settings/images/general-settings.gif'},
					{ from: 'assets-src/sidebar/template-settings/images/roadmap.jpg', to: 'blocks/post-meta/template-settings/images/roadmap.jpg'}
					]
			}
		),

		new WooCommerceDependencyExtractionWebpackPlugin(
			{
					requestToExternal,
					requestToHandle
			}
		)
	]
};
