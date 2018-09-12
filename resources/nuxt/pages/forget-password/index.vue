<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center>
			<v-flex xs12 sm8 md4>
				<v-card class="card-radius">
					<v-toolbar color="transparent" dense class="elevation-0">
						<v-layout row wrap align-center justify-center>
							<v-toolbar-title>Quên mật khẩu</v-toolbar-title>
						</v-layout> 
					</v-toolbar>
					<v-card-text class="white">
						<v-form>
							<v-text-field color="red accent-3" prepend-icon="email" v-model="email" name="email" label="Email" type="text"></v-text-field>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-btn flat color="blue" :to="{path: '/login'}" small>
							Đăng nhập
						</v-btn>
						<v-spacer></v-spacer>
						<v-btn color="red accent-3" dark @click.stop.prevent="login" small>Gửi</v-btn>
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