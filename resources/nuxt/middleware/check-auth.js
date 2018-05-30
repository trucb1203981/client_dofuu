
import Cookies from 'js-cookie'
import cookieparser from 'cookieparser'

export default function({store, req, redirect}) {
	const token = window.localStorage.getItem('jwt') || Cookies.get('jwt')
	if(typeof token != 'undefined' || token != null ){
		store.dispatch('getUser').catch(error => {
			
		})
	} else {
		window.localStorage.removeItem('jwt')
		Cookies.remove('jwt')
	}
}