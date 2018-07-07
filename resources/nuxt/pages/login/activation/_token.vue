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
		active: async function() {
			var vm = this
			const data = {active_code: await vm.$route.params.token}
			axios.post(baseURL+'/api/user/active', data).then(response => {
				if(response.status === 200) {
					if(response.data != '') {
						vm.$store.dispatch('alert', {index:0, name: 'login', show:true, type: 'success', message: response.data, close:true})
					}					
					this.$router.replace({name: 'login'})
				}
			})
		}
	},
	created() {
		this.active()
	}
}
</script>
