// rollup.config.js
var min = '.min';
import vue from 'rollup-plugin-vue';
import css from 'rollup-plugin-css-only';
import commonjs from 'rollup-plugin-commonjs';
import buble from 'rollup-plugin-buble';

import resolve from 'rollup-plugin-node-resolve';
import uglify from 'rollup-plugin-uglify';
import { minify } from 'uglify-es';
import async from 'rollup-plugin-async';

// let feInput = './assets/dev/js/';
// let feOutput = './assets/production/js/';
// import livereload from 'rollup-plugin-livereload'
import serve from 'rollup-plugin-serve'


const aPlugins = [
	commonjs(),
	vue(),
	css(),
	buble(),
	resolve({
		jsnext: true,
		main: true,
		browser: true,
	}),
	async(),
	serve(),
	uglify({}, minify)
];

export default [
	{
		name: 'WilCity',
		input: 'assets/dev/js/index.js',
		output: {
			file: 'assets/production/js/bundle'+min+'.js',
			format: 'iife',
		},
		sourcemap: false,
		treeshake: true,
		plugins: aPlugins
	},
	{
		name: 'Dashboard',
		input: 'assets/dev/js/dashboard.js',
		output: {
			file: 'assets/production/js/dashboard'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'FavoriteStatistics',
		input: 'assets/dev/js/FavoriteStatistics.js',
		output: {
			file: 'assets/production/js/FavoriteStatistics'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'MagnificGalleryPopup',
		input: 'assets/dev/js/MagnificGalleryPopup.js',
		output: {
			file: 'assets/production/js/MagnificGalleryPopup'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'NoMapSearch',
		input: 'assets/dev/js/no-map-search.js',
		output: {
			file: 'assets/production/js/no-map-search'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityMap',
		input: 'assets/dev/js/map.js',
		output: {
			file: 'assets/production/js/map'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityQuickSearch',
		input: 'assets/dev/js/quick-search.js',
		output: {
			file: 'assets/production/js/quick-search'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityReview',
		input: 'assets/dev/js/review.js',
		output: {
			file: 'assets/production/js/review'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityShortcodes',
		input: 'assets/dev/js/shortcodes.js',
		output: {
			file: 'assets/production/js/shortcodes'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityAddListing',
		input: 'assets/dev/js/addlisting.js',
		output: {
			file: 'assets/production/js/addlisting'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityApp',
		input: 'assets/dev/js/app.js',
		output: {
			file: 'assets/production/js/app'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilCityResetPassword',
		input: 'assets/dev/js/resetPassword.js',
		output: {
			file: 'assets/production/js/resetPassword'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcitySingleListing',
		input: 'assets/dev/js/single-listing.js',
		output: {
			file: 'assets/production/js/single-listing'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcitySingleEvent',
		input: 'assets/dev/js/single-event.js',
		output: {
			file: 'assets/production/js/single-event' + min + '.js',
			format: 'iife'
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcitySingleMapbox',
		input: 'assets/dev/js/map/SingleMapbox.js',
		output: {
			file: 'assets/production/js/single-mapbox' + min + '.js',
			format: 'iife'
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcitySingleGoogleMap',
		input: 'assets/dev/js/map/SingleGoogleMap.js',
		output: {
			file: 'assets/production/js/single-google-map' + min + '.js',
			format: 'iife'
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityGoogleMap',
		input: 'assets/dev/js/map/GoogleMap.js',
		output: {
			file: 'assets/production/js/googlemap' + min + '.js',
			format: 'iife'
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityMapbox',
		input: 'assets/dev/js/map/Mapbox.js',
		output: {
			file: 'assets/production/js/mapbox' + min + '.js',
			format: 'iife'
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityCustomLogin',
		input: 'assets/dev/js/customLogin.js',
		output: {
			file: 'assets/production/js/customLogin'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	},
	{
		name: 'WilcityactiveListItem',
		input: 'assets/dev/js/activeListItem.js',
		output: {
			file: 'assets/production/js/activeListItem'+min+'.js',
			format: 'iife',
		},
		sourcemaps: true,
		plugins: aPlugins
	}
]