
const inBrowser = typeof window !== 'undefined'

const state = {
	cart: {
		instance: 0,
		items: []
	},
	coupon: null,
	dialog: false,
	cartDrawer: false,
	checkOutSuccess:false
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
	},
	SHOW_CART(state) {
		state.cartDrawer = true
	},
	CLOSE_CART(state) {
		state.cartDrawer = false
	},
	CHECKOUT_SUCCESS(state) {
		state.checkOutSuccess = true
	},
	CLOSE_CHECKOUT_SUCCESS(state) {
		state.checkOutSuccess = false
	}
}

const actions = {
	getToCart:({commit}, id) => new Promise(async (resolve, reject) => {
		
		var temp = Object.assign({}, {instance: id, items: []})
		var storage = window.localStorage.getItem('cart')

		if (typeof storage == 'undefined' || storage == null ) {
			window.localStorage.setItem('cart', JSON.stringify(temp))
		} else {	

			let cartTemp = JSON.parse(storage)		
			
			if(id === cartTemp.instance) {
				
				temp = Object.assign({}, cartTemp)
				commit('FETCH_CART', temp)
				commit('CHANGE_TAB', 1)

			} else {
				window.localStorage.setItem('cart', JSON.stringify(temp))
				commit('FETCH_CART', temp)
				commit('CHANGE_TAB', 0)
			}
			
		}
	}),
	removeCoupon:({commit}) => {
		commit('REMOVE_COUPON')
	} 
}
const getters = {
	counts: (state) => {
		return (state.cart.items.length>0) ? state.cart.items.length : 0
	},
	subTotal: (state) => {
		
		let total        = 0
		
		if(state.cart.items.length>0) {
			state.cart.items.forEach(item => {
				total = total + item.subTotal
			})
			return total
		}

		return 0
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