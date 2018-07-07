<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center>
			<v-flex xs12 sm8 md4>
				<v-card class="grey lighten-4">
					<v-toolbar color="transparent" dense class="elevation-0"> 
						<v-toolbar-title>Quên mật khẩu</v-toolbar-title>
					</v-toolbar>
					<v-card-text class="white">
						<v-form>
							<v-text-field color="red accent-3" prepend-icon="email" v-model="email" name="email" label="Email" type="text"></v-text-field>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-btn color="red accent-3" dark block @click.stop.prevent="login">Gửi</v-btn>
					</v-card-actions>
					<v-card-actions>
						<v-btn flat color="primary" :to="{path: '/login'}">
							<v-icon>chevron_left</v-icon>
							Quay lại
						</v-btn>
					</v-card-actions>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
export default {
	layout: 'credential',
	asyncData() {
		return {
			email: ''
		}
	},
	methods: {

	}
}
</script>