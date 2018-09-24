<template>
	<v-snackbar v-model="snackbar" :timeout="timeout" bottom >
		{{ message }}
		<v-btn color="pink" flat @click="snackbar = false" >
			Đóng
		</v-btn>
	</v-snackbar>
</template>

<script>
	export default {
		data() {
			return{
				snackbar: false,
				message: null,
				resolve: null,
				reject: null,
				timeout: 3000
			}
		},
		methods: {
			open: function(message, timeout = 3000) {
				this.snackbar = true
				this.message  = message
				this.timeout  = timeout
				return new Promise((resolve, reject) => {
					this.resolve = resolve
					this.reject  = reject
				})
			},
			cancel() {
				this.resolve(false)
				this.dialog = false
			}
		}
	}
</script>