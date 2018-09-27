<template>
	<v-dialog v-model="dialog" persistent max-width="400"  @keydown.esc="cancel">
		<v-card>
			<v-toolbar color="transparent" dense flat>
				<v-toolbar-title>{{ title }}</v-toolbar-title>
			</v-toolbar>
			<v-divider></v-divider>
			<v-card-text v-show="!!message">Bạn có chắc muốn {{message}} này không?</v-card-text>
			<v-divider></v-divider>
			<v-card-actions>
				<!-- <slot name="actions"></slot> -->
				<v-spacer></v-spacer>
				<v-btn flat color="red darken-1" @click.native="cancel" small class="white--text px-0" round>Hủy</v-btn>
				<v-btn round color="blue" @click.native="agree" small class="white--text px-0">Chấp nhận</v-btn>
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
			open(title, message) {
				this.dialog  = true
				this.title   = title
				this.message = message
				return new Promise((resolve, reject) => {
					this.resolve = resolve
					this.reject  = reject
				})

			},
			agree() {
				this.resolve(true)
				this.dialog = false
			},
			cancel() {
				this.resolve(false)
				this.dialog = false
			}
		}		
	}
</script>