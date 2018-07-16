<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center>
			<v-flex xs12 sm8 md4>
				<v-card class="grey lighten-4">
					<v-progress-linear indeterminate v-if="loading"></v-progress-linear>
					<v-toolbar color="transparent" dense class="elevation-0" > 
						<v-toolbar-title>
							Bổ sung thông tin
						</v-toolbar-title>
					</v-toolbar>
					<v-card-text class="white">
						
						<v-alert :color="alert.type" dismissible :value="alert.show" outline v-show="alert.index === 0 && $route.name == alert.name">
							{{alert.message}}
						</v-alert>

						<form>

							<v-flex xs12 md12>
								<v-text-field color="red accent-3"  prepend-icon="phone" v-model="editedItem.phone" name="confirm" label="Số điện thoại"
								v-validate="'required|numeric|min:10|max:11'"
								data-vv-name="phone"
								:error-messages="errors.collect('phone')"
								data-vv-delay="300"
								hint="Số điện thoại chỉ đăng ký được một tài khoản"
								persistent-hint></v-text-field>
							</v-flex>

							<v-flex xs12>
								<v-text-field color="red accent-3"  prepend-icon="lock" v-model.trim="editedItem.password" label="Mật khẩu mới" id="password" type="password"
								v-validate="'required|min:8|max:36'"
								data-vv-name="password"
								:error-messages="errors.collect('password')"
								data-vv-delay="300"
								hint="Khuyên sử dụng 8 ký tự trở lên và kết hợp chữ cái, chữ số và biểu tượng"
								persistent-hint></v-text-field>
							</v-flex>

							<v-flex xs12>
								<v-text-field color="red accent-3"  prepend-icon="lock" v-model.trim="confirm" name="confirm" label="Xác nhận lại mật khẩu" id="confirm" type="password"
								v-validate="{required: true, is:editedItem.password}"
								data-vv-name="confirm"
								:error-messages="errors.collect('confirm')"
								data-vv-delay="300"
								></v-text-field>
							</v-flex>
							
						</form>
						<v-spacer></v-spacer>
					</v-card-text>
					<v-card-actions>
						<v-btn round color="success" class="text--white" block :loading="process" @click.stop.prevent="save" :disabled="disabled" small>Hoàn thành</v-btn>
					</v-card-actions>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
import moment from 'moment'
import axios from 'axios'
import {mapState} from 'vuex'
import vietnam from 'vee-validate/dist/locale/vi';
export default {
	middleware: 'authenticated',
	layout: 'credential',
	data() {
		return {
			editedItem: {
				name: '',
				email: '',
				phone: '',
				password: '',
				gender: false,
				birthday: '2018-05-02',
			},
			confirm: '',
			loading: false,
			process: false,
			locale: 'vi',
		}
	},
	computed: {
		...mapState({
			alert: state => state.alertStore.alert,
			user: state  => state.authStore.facebookUser,
			alert: state => state.alertStore.alert
		}),
		disabled: function() {
			if(this.editedItem.phone.length > 0 && this.editedItem.password.length > 0 && this.confirm.length > 0) {
				return false
			} else {
				return true
			}
		}
	},
	methods: {
		save() {
			var vm = this
			vm.user.birthday = moment(vm.user.birthday).format('YYYY-MM-DD')

			var data = Object.assign(vm.editedItem, vm.user)

			if(vm.user.gender === 'male') {
				data.gender = true
			} else {
				data.gender = false
			}
			vm.$validator.validateAll().then(async function(result) {
				if(result) {
					vm.process = true
					axios.post('/api/facebook/register', data).then(response => {
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
					}).catch(error => {
						if(error.response.status == 422) {

							var mes = ''

							Object.values(error.response.data.errors).map((item) => {
								mes += item[0] + ' ';
							})

							vm.$store.dispatch('alert', {
								name: vm.$route.name,
								index:0,
								type: 'error',
								message: mes,
								close: true
							})

						}
					}).finally(() => {
						vm.process = false 
					})
				}				
			})
		}
	},
	watch: {
	},
	created() {
		this.$validator.localize(this.locale, {
			messages:vietnam.messages,
			attributes: {
				name: 'Họ tên',
				email: 'Email',
				password: 'Mật khẩu',
				confirm: 'Xác nhận mật khẩu',
				phone: 'Số điện thoại'
			}
		})
		this.$validator.localize(this.locale)
	},
	mounted() {
		this.$nextTick(() => {
			var vm   = this
			FB.login(function(response) {
				if(response.authResponse) {
					FB.api('/me', 'GET', {'fields': 'email,name,birthday,gender,location,picture.width(100).height(100)'}, function(response) {
						var data = response
						axios.post('/api/facebook/auth', data).then(response => {
							if(response.status === 204) {
								vm.$store.commit('SET_FB_USER', data)
							} else {
								vm.$router.replace({path: '/'})
							}
						})
					});
				} else {
					console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope: 'email,user_likes, user_birthday, user_location, user_gender'})
		})
	}
}
</script>