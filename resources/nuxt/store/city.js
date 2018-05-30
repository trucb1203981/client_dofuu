import axios from 'axios'
import {fetchCityURL, getCityURL, getHeader} from '~/config.js'
import Cookies from 'js-cookie'
var cookieparser = require('cookieparser')

const state = {
	cities: [],
	currentCity: null,
	currentID: null
}

const mutations = {
	FETCH_CITY: function(state, payload) {
		state.cities = payload.data
	},
	GET_CITY: function(state, payload) {
		state.currentCity = payload.data
		state.currentID   = payload.data.id
	},
	CHANGE_CITY: function(state, id) {
		state.currentID = id
	},
	UPDATE_CITY: function(state, city) {
		state.currentCity = city
	},
	DESTROY_CITY: function(state) {
		cities    = []
	}
}

const actions = {
	//FETCH CITIES
	fetchCity: ({commit}) => new Promise((resolve, reject) => {
		axios.get('/api/FetchCities', {withCredentials:true}).then(response => {
			if(response.status == 200) {
				commit('FETCH_CITY', response.data)
			}
			resolve(response)
		})
	}).catch(error => {
		reject(error)
	}),
	// FETCH DISTRICT AND TYPE BY CITY ID
	getCityCurrent: ({commit}, id) => new Promise((resolve, reject) => {
		const data = {cityId: id}
		axios.post('/api/GetCityCurrent/'+ id,  data,{withCredentials:true}).then(response => {
			if(response.status == 200) {
				commit('GET_CITY', response.data)
			}
			resolve(response)
		}).catch(error => {
			reject(error)
		})
	}),
}

const getters = {
	//GET CITY ID CURRENT 
	getCurrentID: (state) => {
		return state.currentID
	},
	getCity: (state) => {
		return state.currentCity
	},
	// FETCH ALL CITY
	getAllCity: (state) => {
		return state.cities
	},
	// GET CITY BY ID
	getCityByID: (state) => id => {
		return state.cities.find(item => item.id === id)
	},
	// GET CITY BY SLUG
	getCityBySlug: (state) => slug => {
		return state.cities.find(item => item.slug === slug)
	},
}

export default {
	state, mutations, actions, getters
}