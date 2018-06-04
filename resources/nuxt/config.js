
export const getHeader = function(){
	const tokenData =  JSON.parse(window.localStorage.getItem('jwt'))
	const headers = {
		'Accept': 'application/json',
		'Authorization':'Bearer ' +tokenData.access_token
	}
	return headers
}

// export const baseURL 			= 'http://api.dofuu.com'
export const baseURL 			= 'http://www.dofuu.com' || 'https://dofuu.com' || 'https://www.dofuu.com' || 'http://dofuu.com' 

export const fetchCityURL       = '/api/FetchCities'
export const getCityHasDealURL  = '/api/GetCityInformationHasDeal'


export const fetchTypeURL       = '/api/FetchTypes'
//API STORE
export const getStoreURL        = '/api/GetStore'
export const getStoreHasDealURL = '/api/LoadStore'
//API AUTH
export const loginURL           = '/api/login'
export const registerURL        = '/api/register'
export const logoutURL          = '/api/logout'
export const tokenURL           = '/oauth/token'
export const getUserURL         = '/api/df'