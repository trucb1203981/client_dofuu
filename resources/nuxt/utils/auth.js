
import Cookies from 'js-cookie'
import cookieparser from 'cookieparser'

// export  const setToken = (token) => {
// 	if(process.server) {
// 		return
// 	}
// 	window.localStorage.setItem('jwt', token)
// 	Cookies.set('jwt', token)
// }

// export const getToken = () => {
// 	if(process.client) {
// 		const token = window.localStorage.getItem('jwt') || Cookies.get('jwt')
// 		if(typeof token !== 'undefined' || token !== null) {
// 			return token
// 		}
// 	}
// }

// export  const removeToken = () => {
// 	if(process.server) {
// 		return
// 	}
// 	window.localStorage.removeItem('jwt')
// 	Cookies.remove('jwt')
// }

export default function (Vue) {
	Vue.auth = {
		setToken(token) {
			if(process.server) {
				return
			}
			localStorage.setItem('jwt', JSON.stringify(token))
			Cookies.set('jwt', token)
		},
		getToken() {
			if(process.client) {
				var auth = JSON.parse(localStorage.getItem('jwt'))
				if(!auth) {
					return null
				}
				if(Date.now()>parseInt(auth.expires_in)) {
					this.destroyToken();
					return null;
				}
				else{
					return auth.access_token 
				}
			}
			
		},
		destroyToken() {
			if(process.server) {
				return
			}
			localStorage.removeItem('jwt')
		},
		isAuthenticated() {
			if(this.getToken()) {
				return true
			} else {
				return false
			}
		}
	}
	Object.defineProperties(Vue.prototype, {
		$auth: {
			get() {
				return Vue.auth
			}
		}
	})
}

