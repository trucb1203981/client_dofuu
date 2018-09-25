import Vuex from 'vuex'
import typeStore from './type'
import alertStore from './alert'
import authStore from './auth'
import cityStore from './city'
import storeStore from './store'
import cartStore from './cart'

const store = () => {
	return new Vuex.Store({
		state: {
			leftDrawer: false,
			offsetTop: 0,
			myLocation: {
				address: null,
				lat: 0,
				lng: 0,
			}
		},
		mutations: {
			ON_SCROLL: function(state, value) {
				state.offsetTop = value
			},
			LEFT_NAVIGATION_SHOW(state) {
				state.leftDrawer = true
			},
			LEFT_NAVIGATION_CLOSE(state) {
				state.leftDrawer = false
			},
			UPDATE_LOCATION(state, payload) {
				state.myLocation.address = payload[0].formatted_address.slice(0, -10)
				state.myLocation.lat     = payload[0].geometry.location.lat()
				state.myLocation.lng     = payload[0].geometry.location.lng()
			}
		},
		actions: {
			currentLocation: ({commit}) => new Promise((resolve, reject) => {
				var vm      = this			
				var results = null	
				navigator.geolocation.getCurrentPosition(function(position){
					var geocoder = new google.maps.Geocoder()
					geocoder.geocode({'location': {lat: position.coords.latitude, lng: position.coords.longitude}}, function(results, status) {
						if(status === 'OK') {
							commit('UPDATE_LOCATION', results)
						}
					})
				})
				
			})
		},
		modules: {
			alertStore,
			authStore,
			cityStore,
			typeStore,
			storeStore,
			cartStore
		}
	})
}

export default store