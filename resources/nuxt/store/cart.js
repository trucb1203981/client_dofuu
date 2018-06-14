
const inBrowser = typeof window !== 'undefined'

const state = {
	cart: {
		instance: 0,
		items: []
	},
	coupon: null,
	dialog: false
}

const mutations = {
	FETCH_CART(state, payload) {
		state.cart = payload
	},
	ADD_TO_CART(state, product) {
		console.log(product)
		const productIndex  = state.cart.items.findIndex((item) => {
			return item.id === product.id
		})
		if(productIndex > -1) {
			state.cart.items[productIndex].qty++
		} else {
			product.qty = 1
			state.cart.items.push(product)
		}
		window.localStorage.setItem('cart', JSON.stringify(state.cart))
	},
	UPDATE_CART(state, payload) {
		
	},
	DESTROY_CART(state, payload) {
		state.cart = {
			instance: 0,
			items: []
		}
	},
	SHOW_CHECKOUT(state, payload) {
		state.dialog = !state.dialog
	},
	CLOSE_CHECKOUT(state) {
		state.dialog = false
	},
	ADD_COUPON(state, payload) {
		state.coupon = payload.data
	},
	REMOVE_COUPON(state) {
		state.coupon = null
	}
}

const actions = {
	getToCart:({commit}, id) => new Promise((resolve, reject) => {
		var temp = Object.assign({}, {instance: id, items: []})
		var storage = window.localStorage.getItem('cart')
		if(typeof storage == 'undefined' || storage == null ) {
			window.localStorage.setItem('cart', JSON.stringify({instance:0, items:[]}))
		} else {	
			let cartTemp = new Array(JSON.parse(storage))			
			cartTemp = cartTemp.find(item => {
				return parseInt(item.instance) === parseInt(id)
			})
			if(typeof cartTemp == 'undefined' || cartTemp == null) {
				commit('FETCH_CART', temp)
				commit('CHANGE_TAB', 0)
			} else {
				temp = Object.assign({}, cartTemp)
				commit('FETCH_CART', temp)
				commit('CHANGE_TAB', 1)
			}

		}
	})
}
const getters = {
	counts: (state) => {
		return (state.cart.items.length>0) ? state.cart.items.length : 0
	},
	subTotal: (state) => {
		let total = 0
		state.cart.items.forEach(item => {
			total = total + (parseFloat(item.price) * parseInt(item.qty))					
			return total
		})
		return total
	},
	total: (state) => {

	},
	discount: (state) => {
		return (state.coupon != null && state.coupon.discountPercent > 0) ? state.coupon.discountPercent : 0
	}
}

export default {
	state, mutations, actions, getters
}