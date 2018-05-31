import axios from 'axios'
import Cookies from 'js-cookie'
// axios.defaults.baseURL = 'http://127.0.0.1:8001'
axios.defaults.headers.common['Content-type']   = 'application/json';
axios.defaults.headers.put['X-Requested-With']  = 'XMLHttpRequest';
axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
axios.interceptors.response.use(function (response) {
	return response
}, function (error) {
	if(error.response.status === 401) {
		window.localStorage.removeItem('jwt')
		Cookies.remove('jwt')	
	}
	return Promise.reject(error)
})
