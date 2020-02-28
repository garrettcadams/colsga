// webpack.config.js
const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
	entry: {
		activeListItem: './assets/dev/js/activeListItem.js',
		MagnificGalleryPopup: './assets/dev/js/MagnificGalleryPopup.js',
		addlisting: './assets/dev/js/addlisting.js',
		customLogin: './assets/dev/js/customLogin.js',
		app: './assets/dev/js/app.js',
		dashboard: './assets/dev/js/dashboard.js',
		FavoriteStatistics: './assets/dev/js/FavoriteStatistics.js',
		Follow: './assets/dev/js/Follow.js',
		general: './assets/dev/js/general.js',
		index: './assets/dev/js/index.js',
		map: './assets/dev/js/map.js',
		'mapbox': './assets/dev/js/map/mapbox.js',
		'googlemap': './assets/dev/js/map/GoogleMap.js',
		'single-mapbox': './assets/dev/js/map/SingleMapbox.js',
		'single-google-map': './assets/dev/js/map/SingleGoogleMap.js',
		'no-map-search': './assets/dev/js/no-map-search.js',
		'quick-search': './assets/dev/js/quick-search.js',
		'resetPassword': './assets/dev/js/resetPassword.js',
		'review': './assets/dev/js/review.js',
		'shortcodes': './assets/dev/js/shortcodes.js',
		'single-event': './assets/dev/js/single-event.js',
		'single-listing': './assets/dev/js/single-listing.js',
		'WilokeDirectBankTransfer': './assets/dev/js/WilokeDirectBankTransfer.js',
		'WilokeGoogleMap': './assets/dev/js/WilokeGoogleMap.js',
		'WilokePayPal': './assets/dev/js/WilokePayPal.js',
		'WilokeStripe': './assets/dev/js/WilokeStripe.js',
		'WilokeSubmissionCouponCode': './assets/dev/js/WilokeSubmitCouponCode.js'
	},
	output: {
		filename: '[name].min.js',
		path: path.resolve(__dirname, 'assets/production/js')
	},
	watch: true,
	module: {
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},
			// this will apply to both plain `.js` files
			// AND `<script>` blocks in `.vue` files
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			// this will apply to both plain `.css` files
			// AND `<style>` blocks in `.vue` files
			{
				test: /\.css$/,
				use: [
					'vue-style-loader',
					'css-loader'
				]
			}
		]
	},
	plugins: [
		// make sure to include the plugin for the magic
		new VueLoaderPlugin()
	],
	optimization: {
		splitChunks: {
			chunks: "async",
			cacheGroups: {// Cache Group
				vendors: {
					test: /[\/]node_modules[\/]/,
					priority: -10
				},
				default: {
					minChunks: 2,
					priority: -20,
					reuseExistingChunk: true
				}
			}
		}
	}
}