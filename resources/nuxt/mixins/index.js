import { baseURL } from '@/config.js'
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
		}
		// storeURL: function(slug) {
		// 	var flag_c = parseInt(Cookies.get("flag_c"))
		// 	if (typeof flag_c != "undefined" ||	flag_c != null) {
		// 		const city = this.$store.getters.getCityByID(flag_c);
		// 		return city.slug + "/" + slug;
		// 	}
		// },
	}
}