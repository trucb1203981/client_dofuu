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
			
		}
	},
	created() {
		this.auth()
	}
}
</script>
