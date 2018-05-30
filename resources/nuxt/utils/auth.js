
import Cookies from 'js-cookie'
import cookieparser from 'cookieparser'

export  const setToken = (token) => {
	if(process.server) {
		return
	}
	window.localStorage.setItem('jwt', token)
	Cookies.set('jwt', token)
}

export const getToken = () => {
	if(process.client) {
		const token = window.localStorage.getItem('jwt') || Cookies.get('jwt')
		if(typeof token !== 'undefined' || token !== null) {
			return token
		}
	}
}

export  const removeToken = () => {
	if(process.server) {
		return
	}
	window.localStorage.removeItem('jwt')
	Cookies.remove('jwt')
}

