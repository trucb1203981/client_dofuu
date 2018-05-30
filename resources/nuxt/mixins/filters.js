import Vue from 'vue'
import moment from 'moment'
import numeral from 'numeral'
Vue.filter('upperCase', function(value) {
	if(value) {
		return value.toUpperCase()
	}
})

Vue.filter('formatTime', function(time) {
	if(time) {
		return moment(time,'HH:mm:ss').format('HH:mm')
	}
})

Vue.filter('formatDate', function(date) {
	if(date) {
		return moment(date, 'YYYY-MM-DD').format('DD-MM-YYYY')
	}
})

Vue.filter('formatPrice', function(price) {
	if(price) {		
		return numeral(price).format('0,0$')
	}
})

Vue.filter('subPrice', function(price, qty) {
	return numeral(Math.floor(price*qty)).format('0,0$')
})