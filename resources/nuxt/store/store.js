import axios from 'axios'
import Cookies from 'js-cookie'
import Cookie from 'cookie'
import CookieParser from 'cookieparser'
import {getStoreURL, getHeader} from '@/config'

const state = {
	tabIndex:0,
	store: null,
	loading:false,
	rightDrawer:false,

	headers: [
	{title: 'Danh mục'},
	{title: 'Giỏ hàng'}
	]
}

const mutations = {
	CHANGE_TAB(state, tabIndex) {
		if(state.tabIndex != tabIndex ){
			state.tabIndex = tabIndex
		}
	},
	GET_STORE(state, payload) {
		state.store = payload
	},
	LOADING_STORE(state) {
		state.loading = !state.loading
	},
	STORE_NAV_RIGHT(state) {
		state.rightDrawer = !state.rightDrawer
	},
	DESTROY_STORE(state) {
		state.store = null
	}
}

const actions = {
	getStore: ({commit}, params) => new Promise((resolve, reject) => {
		commit('LOADING_STORE')
		axios.get('/api/GetStore', {params, withCredentials:true}).then(async (response) => {
			if(response.status == 200) {
				commit('GET_STORE', response.data.store)
			}
			resolve(response)
		}).catch(error => {
			reject(error)
		}).finally(() => {
			commit('LOADING_STORE')
		})

	}),

	removeFavoriteStore: ({commit}, storeId) => new Promise((resolve, reject) => {
		const data   = {}
		const params = {name: 'favoriteEndpoint'}
		axios.post('/api/FavoriteStore/'+storeId+'/Remove', data, {params, headers: getHeader(), withCredentials:true}).then(response => {
			if(response.status === 200) {
				resolve(response)
			}
		})
	})
	
}

const getters = {
}

export default {
	state, mutations, actions, getters
}