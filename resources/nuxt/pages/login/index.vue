<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center>
			<v-flex xs12 sm8 md4>
				<v-card class="card-radius">
					
					<v-toolbar color="transparent" dense class="elevation-0" > 
						<v-layout row wrap justify-center align-center>							
							<v-toolbar-title>
								Đăng nhập
							</v-toolbar-title>
						</v-layout>
					</v-toolbar>
					<v-progress-linear indeterminate background-color="grey lighten-3"	color="white" class="py-0 my-0" v-if="process"></v-progress-linear>
					
					<!-- LOGIN FACEBOOK -->
					<v-card-actions>
						<v-btn color="indigo lighten-1" dark block :loading="process" @click.stop.prevent="fbLogin" small><v-icon left>fab fa-facebook-square</v-icon>Đăng nhập bằng facebook</v-btn>
					</v-card-actions>

					<v-card-text class="white">
						
						<v-alert :color="alert.type" dismissible :value="alert.show" outline v-show="alert.index === 0 && $route.name == alert.name">
							{{alert.message}}
						</v-alert>

						<v-form>
							<v-text-field 
							color="red accent-3"
							prepend-icon="email" 
							v-model.trim="email"  
							label="Email" 
							type="text" 
							:error-messages="errors.collect('email')"
							v-validate="'required|email'"
							data-vv-name="email" ></v-text-field>

							<v-text-field 
							color="red accent-3"
							prepend-icon="lock" 
							v-model="password" name="password" 
							label="Mật khẩu" 
							id="password" 
							type="password"
							:error-messages="errors.collect('password')"
							v-validate="'required'"
							data-vv-name="password"
							:type="showPassword ? 'text' : 'password'"
							:append-icon="showPassword ? 'visibility' : 'visibility_off'"
							@click:append="showPassword = !showPassword" 
							@keyup.enter.exact.prevent="login"></v-text-field>
						</v-form>
						<v-spacer></v-spacer>
						<nuxt-link :to="{path: '/forget-password'}" class="blue--text" >Quên mật khẩu?</nuxt-link>
					</v-card-text>
					<v-card-actions>
						<v-btn flat :loading="process":to="{path: '/register'}" small class="blue--text">Tạo tài khoản</v-btn>
						<v-spacer></v-spacer>
						<v-btn color="red accent-3" dark :loading="process" @click.prevent="login" small>Đăng nhập</v-btn>
					</v-card-actions>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
	import {mapState} from 'vuex'
	import axios from 'axios'
	import Cookies from 'js-cookie'
	import {clientID, clientSecret} from '@/env'
	import {tokenURL, loginURL} from '@/config'
	import vietnam from 'vee-validate/dist/locale/vi';
	export default {
		middleware: 'authenticated',
		layout: 'credential',
		head() {
			return {
				title: 'Đăng nhập'
			}
		},
		asyncData() {
			return {
				email: '',
				password: '',
				loading: false,
				locale: 'vi',
				process: false,
				showPassword: false,
			}
		},
		methods: {
			login() {
				var vm = this
				let data = {email: vm.email, password: vm.password}
				vm.$validator.validateAll().then(async function( result ) {
					if( result ) {
						if(!vm.process) {
							vm.process = true
							setTimeout(() => {	

								axios.post('/api/auth/login', data).then(response => {
									if(response.status == 200) {
										if(response.data.type === 'error') {

											vm.$store.dispatch('alert', {
												name: vm.$route.name,
												index:0,
												show: true, 
												type: 'error', 
												message: response.data.message,
												close: true
											})

										}  else {

											vm.$store.dispatch('setToken', response.data)

											vm.$store.dispatch('getUser').then(response => {
												if(response.data.type == 'success') {
													if(typeof vm.redirect == 'undefined') {
														vm.$router.push({path: '/'})
													} else {
														vm.$router.push({path: vm.redirect})	
													}	
												}
												else if (response.data.type == 'error') {
													vm.loginFail(response.data.message)							
												}

											})
										}
									}
								}).catch(error => {
									if(error.response.status === 401) {
										vm.$store.dispatch('alert', {
											name: vm.$route.name,
											index:0,
											show: true, 
											type: 'error', 
											message: 'Email hoặc mật khẩu không đúng',
											close: true
										})		
									}
								}).finally(() => {
									vm.process = false
								})	

							}, 100)
						}
					}
				})			
			},
			fbLogin() {
				var data = []
				var vm   = this
				if(!vm.process) {
					vm.process = true
					setTimeout(() => {

						FB.login(function(response) {
							if(response.authResponse) {
								FB.api('/me', null , {'fields': 'email, name, birthday, gender, location, picture.width(350).height(350)'}, function(response) {
									data = response
									axios.post('/api/facebook/auth', data).then(response => {
										if(response.status === 200) {
											vm.$store.dispatch('setToken', response.data)
											vm.$store.dispatch('getUser').then(response => {
												if(response.data.type == 'success') {
													if(typeof vm.redirect == 'undefined') {
														vm.$router.push({path: '/'})
													} else {
														vm.$router.push({path: vm.redirect})	
													}	
												}
												else if (response.data.type == 'error') {
													vm.loginFail(response.data.message)	
												}

											})
										}
										if(response.status === 204) {
											vm.$store.dispatch('setFBUser', data)
											vm.$router.push({name: 'login-facebook'})
										}
									}).finally(() => {
										vm.process = false
									})
								});
							} else {
								console.log('User cancelled login or did not fully authorize.');
							}
						}, {scope: 'email,user_likes,user_birthday,user_location,user_gender'})

					}, 100)
				}				
			},
			loginFail(message) {
				this.$store.dispatch('removeToken')
				this.$store.dispatch('alert', {
					name: vm.$route.name,
					index:0,
					show: true, 
					type: 'warning', 
					message: message,
					close: true
				})		
			}
		},
		created() {
			this.$validator.localize(this.locale, {
				messages:vietnam.messages,
				attributes: {
					email: 'Email',
					password: 'Mật khẩu'
				}
			})
			this.$validator.localize(this.locale)
		},
		computed: {
			redirect() {
				return this.$route.query.redirect
			},
			...mapState({
				alert: state => state.alertStore.alert
			}), 
			show() {
				if(this.$route.name == this.alert.name && this.alert.show) {
					return true
				}
				return false
			}
		}
	}
</script>

