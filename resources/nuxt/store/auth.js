import Vue from 'vue'
import axios from 'axios'
import Cookies from 'js-cookie'
import {getHeader} from '@/config'
import Auth from '@/utils/auth'
Vue.use(Auth)
var cookieparser = require('cookieparser')

const inBrowser = typeof window !== 'undefined'

const state = {
	tempUser: null,
	currentUser: null,
	isAuth: !!window.localStorage.getItem('jwt') || !!Cookies.get('jwt'),
	token: null,
	imageDialog: false
}

const mutations = {
	SET_USER: function(state, payload) {
		state.isAuth = true
		state.currentUser = payload.data
	},
	SET_TOKEN: function(state, token) {
		const jwt = {access_token: token.access_token, expires_in: Date.now()+token.expires_in}
		state.token = token.access_token
		if(inBrowser) {
			if(!state.token) {
				window.localStorage.removeItem('jwt')
				Cookies.remove('jwt', null)
			}
			window.localStorage.setItem('jwt', JSON.stringify(jwt))
			Cookies.set('jwt', jwt, {expires: Math.floor(token.expires_in/(24*60*60))})
		}
	},
	REVOKE_TOKEN: function(state) {
		state.token  = null
		state.isAuth = false
		window.localStorage.removeItem('jwt')
		Cookies.remove('jwt', null)	
	},
	LOGOUT: function(state, token) {
		state.currentUser = null
		state.isAuth      = false
		state.token       = null
		window.localStorage.removeItem('jwt')
		Cookies.remove('jwt', null)
	},
	SET_TEMP_USER: function(state, payload) {
		state.tempUser = payload
	},
	DESTROY_TEMP_USER: function(state) {
		state.tempUser = null
	},
	SHOW_IMAGE_DIALOG: function(state) {
		state.imageDialog = true
	},
	CLOSE_IMAGE_DIALOG: function(state) {
		state.imageDialog = false
	}
}

const actions = {
	// nuxtServerInit({commit} , {req}) {
	// 	console.log(req)
	// 	let accessToken = null
	// 	if(!process.server) {
	// 		if(req.headers.cookie) {
	// 			var parsed = cookieparser.parse(req.headers.cookie)
	// 			accessToken = JSON.parse(parsed.auth)
	// 			commit('SET_TOKEN', accessToken)
	// 		} 
	// 	} else {
	// 		if(inBrowser) {
	// 			accessToken = window.localStorage.getItem('auth') || Cookies.get('auth')	
	// 		}
	// 	}
	// },
	getUser: ({commit}) => new Promise((resolve, reject) => {
		const data = []
		axios.post('/api/auth/df', data ,{headers: getHeader()}).then(response => {
			if(response.status == 200) {
				commit('SET_USER', response.data)
			}
			resolve(response)
		}).catch(error => {
			if(error.response.status === 401) {
				commit('LOGOUT')
			}
			reject(error)
		})
	}),
	refreshToken: ({commit}, payload) => new Promise((resolve, reject) => {
		const data = []
		axios.post('/api/auth/refresh', data, {headers: getHeader()}).then(response => {
			if(response.status == 200) {
				commit("SET_TOKEN", response.data)
			}
		})
	}),
	logout: ({commit}) => new Promise((resolve, reject)=> {
		const data = []
		axios.post('/api/auth/logout', data, {headers: getHeader()}).then(response => {
			if(response.status == 200) {
				commit('LOGOUT')
			}
		})
	}),
	// loadToken ({commit}, req) {
	// 	const cookiesStr = inBrowser ? document.cookie : req.headers.cookie
	// 	const cookies = Cookie.parse(cookiesStr || '') || {}
	// 	const token = cookies.auth
	// 	commit('SET_TOKEN')
	// }
}

const getters = {
	isAuth(state) {
		return state.isAuth
	},
	getUser() {
		return state.currentUser
	}
}

export default {
	state, mutations, actions, getters
}