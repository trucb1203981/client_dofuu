import axios from 'axios'
import {fetchCityURL, getCityURL, getHeader} from '~/config.js'
import Cookies from 'js-cookie'
var cookieparser = require('cookieparser')

const state = {
	tracking: false,
	message: ''

}

const mutations = {
	TRACKING_ON: function(state) {
		state.tracking = true
	},
	TRACKING_OFF: function(state, message) {
		state.tracking = false
		state.message  = message
	}
}

const actions = {
	trackingLocation: ({commit, dispatch}) => new Promise((resolve, reject) => {
		if(navigator.geolocation) {					
			navigator.geolocation.watchPosition(function(position) {
				resolve(true)
				commit('TRACKING_ON')
			},
			function (error) { 
				console.log(error)
				if (error.code == error.PERMISSION_DENIED) {
					console.log('ok')
					resolve(false)
					dispatch('currentLocation')
					commit('TRACKING_OFF', 'Quyền truy cập vị trí bị chặn. Bạn có thể cho phép chúng tôi lấy vị trí hiện tại của bạn bằng cách chọn biểu tượng bên cạnh địa chỉ URL. Sau đó làm mới lại trang web.')
				} 
			});
		} else {
			alert("Trình duyệt không hỗ trợ chức năng tìm vị trí.")
		}
	}), 
}

const getters = {

}

export default {
	state, mutations, actions, getters
}