import axios from 'axios'
import Cookies from 'js-cookie'
import Cookie from 'cookie'
import CookieParser from 'cookieparser'

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
	
}

const getters = {
}

export default {
	state, mutations, actions, getters
}