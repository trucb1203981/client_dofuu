import { baseURL } from '@/config.js'
import moment from 'moment'
import Cookies from 'js-cookie'
export default {
	methods: {
		image(url) {
			if(url == null) {
				return baseURL+'/img/default.png'
			} else {
				if(url.slice(1, 8) === "storage") {
					return baseURL+url
					// return url
				} else {
					return url
				}
			}
		},
		status: function(status) {
			const _s = new String(status).toLowerCase();
			switch(_s) {
				case 'đang mở cửa': 
				return 1
				break;
				case 'đang đóng cửa':
				return 2
				break;
				case 'tạm ngưng hoạt động':
				return 3 
				break;
				case 'ngưng hoạt động':
				return 4
				break;
			}
		},
		formatDate: function(str) {
			if(str != null) {
				return str.substring(8, 10)+'/'+str.substring(5, 7)+'/'+str.substring(0, 4)
			}
			return null
		},
		activityTime(times) {
			var n   = moment().locale('vi').day()
			var day = times.find(day => {
				if(day.number === n) {
					return day
				} else {
					return false
				}
			})

			if(day) {
				var time =  day.times.map(time => {
					return time.from + ' - ' + time.to 
				})
				if(time.length == 1) {
					time = time[0]
				}
				return time
			} else {
				return 'Hôm nay nghỉ'
			}
		},
		typeIcon: function(value, color) {
			var status = new String(value).toLowerCase()
			switch(status) {
				case 'quán ăn':
				return { url: `/map_icons/${color}/quan-an.png`, scaledSize: new google.maps.Size(30, 38)}
				break
				case 'trà sữa':
				return { url: `/map_icons/${color}/tra-sua.png`, scaledSize: new google.maps.Size(30, 38) }
				break
				case 'cà phê':
				return { url: `/map_icons/${color}/ca-phe.png`, scaledSize: new google.maps.Size(30, 38) }
				break
				case 'ăn vặt':
				return { url: `/map_icons/${color}/an-vat.png`, scaledSize: new google.maps.Size(30, 38) }
				break
				case 'thức ăn nhanh':
				return { url: `/map_icons/${color}/fast-food.png`, scaledSize: new google.maps.Size(30, 38) }
				break
				case 'vỉa hè':
				return { url: `/map_icons/${color}/via-he.png`, scaledSize: new google.maps.Size(30, 38) }
				break
			}
		},
		// storeURL: function(slug) {
		// 	var flag_c = parseInt(Cookies.get("flag_c"))
		// 	if (typeof flag_c != "undefined" ||	flag_c != null) {
		// 		const city = this.$store.getters.getCityByID(flag_c);
		// 		return city.slug + "/" + slug;
		// 	}
		// },
	}
}