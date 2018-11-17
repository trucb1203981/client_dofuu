import Vue from 'vue'
import axios from 'axios'
import Cookies from 'js-cookie'
import Auth from '@/utils/auth'
// axios.defaults.baseURL = 'http://127.0.0.1:8001'
// axios.defaults.headers.common['Content-type']   = 'application/json';
// axios.defaults.headers.put['X-Requested-With']  = 'XMLHttpRequest';
// axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
Vue.use(Auth)

axios.interceptors.request.use(config => {
	config.headers.common           	= {'Accept' : 'application/json', 'X-Requested-With' : 'XMLHttpRequest'}
	config.headers.Authorization        = `Bearer `+ Vue.auth.getToken()
	return config
})

axios.interceptors.response.use(response => {
	const token = response.headers['Authorization'] || response.data['token']
	token && window.localStorage.setItem('jwt', token)
	return response
},function (error) {

	if(error.response.status === 401) {
		window.localStorage.removeItem('jwt');
	}

	return Promise.reject(error);
})