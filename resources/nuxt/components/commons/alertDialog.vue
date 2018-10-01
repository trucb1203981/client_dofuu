<template>
	<v-dialog v-model="dialog" max-width="400" >
		<v-card>
			<v-toolbar color="grey lighten-4" dense flat>
				Thông báo
			</v-toolbar>

			<v-card-text>
				<slot></slot>
				<div v-if="!!message">{{message}}</div>
			</v-card-text>
			<v-divider></v-divider>
			<v-card-actions>
				<v-btn block color="grey lighten-4" small round @click="cancel">
					Đóng
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
	export default {
		data() {
			return {
				dialog: false,
				title: null,
				message:null,
				resolve: null,
				reject: null,
			}
		},
		methods: {
			open(title, message = null) {
				this.dialog  = true
				this.title   = title
				this.message = message
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