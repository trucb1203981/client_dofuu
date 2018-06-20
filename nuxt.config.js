const laravelNuxt = require("laravel-nuxt");

module.exports = laravelNuxt({
	mode: 'spa',
	router: {
		middleware: ['check-auth', 'navigation']
	},
	// Options such as mode, srcDir and generate.dir are already handled for you.
	head: {
		title: 'Dofuu website đặt và giao hàng trực tuyến',
		meta: [
		{ charset: 'utf-8' },
		{ name: 'viewport', content: 'width=device-width, initial-scale=1' },
		{ hid: 'description', name: 'description', content: 'Dofuu website' }
		],
		link: [
		{ rel: 'icon', type: 'image/x-icon', href: '/dofuu24x24.png'},
		{ rel: 'stylesheet', href: 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons'}
		]
	},
	loading: { color: '#3B8070' },
	/*
 	** Css Vuetify
 	*/
 	css: [{src: 'vuetify/dist/vuetify.min.css', lang: 'css'}],
 	modules: [['@nuxtjs/google-analytics']],
 	'google-analytics': {
 		id: 'UA-121049710-1'
 	},
	/*
	** Plugin 
	*/
	plugins: ['~/plugins/vuetify', '~/plugins/filters', '~/plugins/vee-validate', '~/plugins/numeral', '~/plugins/vue-google-map', {src:'~/plugins/ga', ssr:false}],
	build: {
		vendor: ['vuetify', 'js-cookie', 'moment', 'vee-validate', 'numeral', 'vue2-google-maps'],
		extend (config, { isDev, isClient }) {
			if(!isClient) {
				config.externals.splice(0, 0, function (context, request, callback) {
					if (/^vue2-google-maps($|\/)/.test(request)) {
						callback(null, false)
					} else {
						callback()
					}
				})
			}
		}
	}
});