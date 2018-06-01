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
			offsetTop: 0
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
			}
		},
		actions: {
			nuxtServerInit ({ commit }, { req }) {

			},
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