import axios from 'axios'
import {fetchTypeURL, getHeader} from '~/config.js'
import Cookies from 'js-cookie'

const state = {
	types: [],
}

const mutations = {
	FETCH_TYPE: function (state, types) {
		state.types = types.data
	}
}

const actions = {
	fetchType: ({commit}, payload) => new Promise((resolve, reject) => {
		axios.get('/api/FetchTypes', {withCredentials:true}).then(response => {
			if(response.status == 200) {
				commit('FETCH_TYPE', response.data)
			}
			resolve(response)
		}).catch(error => {
			reject(error)
		})
	})
}

const getters = {
	//GET ALL TYPE
	getAllType: (state) => {
		return state.types
	},
	// GET TYPE BY SLUG
	getTypeBySlug: (state) => slug => {
		return state.types.find(item => item.slug === slug)
	},
}

export default {
	state, mutations, actions, getters
}