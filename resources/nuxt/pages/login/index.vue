<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center>
			<v-flex xs12 sm8 md4>
				<v-card class="grey lighten-4">
					<v-progress-linear indeterminate v-if="loading"></v-progress-linear>
					<v-toolbar color="transparent" dense class="elevation-0" > 
						<v-toolbar-title>
							Đăng nhập
						</v-toolbar-title>
					</v-toolbar>
					
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
							@keyup.enter="login"></v-text-field>
						</v-form>
						<v-spacer></v-spacer>
						<nuxt-link :to="{path: '/forget-password'}">Quên mật khẩu?</nuxt-link>
					</v-card-text>
					<v-card-actions>
						<v-btn round color="red accent-3" dark block :loading="process" @click.stop.prevent="login" small>Đăng nhập</v-btn>
					</v-card-actions>
					<v-card-actions>
						<v-layout row wrap justify-center>
							<v-flex xs12>
								Bạn chưa có tài khoản?
								<nuxt-link :to="{path: '/register'}">Đăng ký</nuxt-link>
							</v-flex>
						</v-layout>	
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
	asyncData() {
		return {
			email: '',
			password: '',
			loading: false,
			locale: 'vi',
			process:false,
		}
	},
	methods: {
		login() {
			var vm = this
			let data = {email: vm.email, password: vm.password}
			vm.$validator.validateAll().then(async function( result ) {
				if( result ) {
					vm.process = true
					axios.post('/api/auth/login', data).then(response => {
						if(response.status == 200) {
							if(response.data.type === 'error') {
								vm.$store.dispatch('alert', {name: vm.$route.name, alert:  {show: true, message: response.data.message, type: 'error'}})
							}  else {
								const data = response.data
								vm.$store.commit('SET_TOKEN', data)
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
						}
					}).catch(error => {
						if(error.response.status === 401) {
							vm.$store.dispatch('alert', {index:0, name: vm.$route.name, show: true, message: 'Email hoặc mật khẩu không đúng', type: 'error', close:true})
						}
					}).finally(() => {
						vm.process = false
					})					
				}
			})			
		},
		fbLogin() {
			var data = []
			var vm   = this
			vm.process = true
			FB.login(function(response) {
				if(response.authResponse) {
					FB.api('/me', null , {'fields': 'email, name, birthday, gender, location, picture'}, function(response) {
						data = response
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
								vm.$store.commit('SET_FB_USER', data)
								vm.$router.push({name: 'login-facebook'})
							}
						}).finally(() => {
							vm.process = false
						})
					});
				} else {
					console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope: 'email,user_likes, user_birthday, user_location, user_gender'})
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
			name: state  => state.alertStore.name,
			alert: state => state.alertStore.alert
		}), 
		show() {
			if(this.$route.name == this.name && this.alert.show) {
				return true
			}
			return false
		}
	}
}
</script>

<style>

</style>
