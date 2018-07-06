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

Vue.filter('formatDateTimeHumanize', function(date) {
	if(date) {
		var start = moment(date, 'DD-MM-YYYY HH:mm')
		return start.startOf().locale('vi').fromNow()
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

Vue.filter('formatPhone', function(phone) {
	
	return phone.replace(/(\d{4}|\d{5})(\d{3})(\d{3})/,"($1) $2-$3")
})