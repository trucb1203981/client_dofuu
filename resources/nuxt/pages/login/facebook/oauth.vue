<template>
	<v-container align-center justify-center fill-height>
		<v-flex d-flex>
			<v-progress-circular indeterminate color="red"></v-progress-circular>
		</v-flex>			
	</v-container>
</template>
<script>
import axios from 'axios' 
import {baseURL} from '@/config'
export default {
	layout: 'redirect',
	methods: {
		oauth: async function() {
			var vm = this
			const data = {active_code: await vm.$route.params.token}
			FB.login(function(response) {
				if(response.authResponse) {
					FB.api('/me', null , {'fields': 'email, name, birthday, gender, location, picture'}, function(response) {
						var data = response
						axios.post('/api/facebook/auth', data).then(response => {
							if(response.status === 200) {
								vm.$store.commit('SET_TOKEN', response.data)
								vm.$store.dispatch('getUser').then(response => {
									if(response.data.type == 'success') {
										if(typeof vm.redirect == 'undefined') {
											vm.$router.push({path: '/'})
										} else {
											vm.$router.push({path: vm.redirect})	
										}	
									}
									else if (response.data.type == 'error') {
										vm.$store.commit('REVOKE_TOKEN')
										vm.$store.dispatch('alert', {name: vm.$route.name, alert: {message: response.data.message, type: 'warning'}})
									}

								})
							}
							if(response.status === 204) {
								vm.$router.replace({name: 'login'})
							} else {
								vm.$router.replace({path: '/login'})
							}
						})
					});
				} else {
					console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope: 'email,user_likes, user_birthday, user_location, user_gender'})
		}
	},
	created() {
		this.auth()
	}
}
</script>
