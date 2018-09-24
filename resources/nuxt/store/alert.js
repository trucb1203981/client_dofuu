const state = {
	alert: {
		close: false,
		name: '',
		index: 0,
		show: false,
		type: 'success',
		message: ''
	}
}

const mutations = {
	SHOW_ALERT(state, payload) {
		state.alert = Object.assign({}, payload)
	},
	CLOSE_ALERT(state) {
		state.alert = Object.assign({}, {name: '', index: 0, show:false, type: 'success', message: ''})
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