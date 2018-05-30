
import Cookies from 'js-cookie'
import cookieparser from 'cookieparser'

export default ({ app }) => {
	if(process.browser) {
		app.router.beforeEach((to, from, next) => {
			console.log(to)
		})
	}
}