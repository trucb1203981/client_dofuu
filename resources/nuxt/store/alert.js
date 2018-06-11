const state = {
	name: '',
	alert: {
		show: false,
		type: 'success',
		message: ''
	}
}

const mutations = {
	SHOW_ALERT(state, payload) {
		state.name = payload.name
		state.alert = Object.assign({}, {show:true, type: payload.alert.type, message: payload.alert.message})
	},
	CLOSE_ALERT(state) {
		state.name = ''
		state.alert = Object.assign({}, {show:false, type: 'success', message: ''})
	}
}
const actions = {
	alert({commit}, payload) {
		commit('SHOW_ALERT', payload)
		if(payload.close) {
			setTimeout(() => {
				commit('CLOSE_ALERT')
			}, 5000)
		}
		
	}
}

export default {
	state, mutations, actions
}