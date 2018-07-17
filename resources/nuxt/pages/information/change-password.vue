<template>
	<v-container>
		<v-dialog v-model="loading" hide-overlay persistent width="300">
			<v-card	color="red darken-3"	dark>
				<v-card-text>
					Xin Quý khách vui lòng chờ trong giây lát...
					<v-progress-linear
					indeterminate
					color="white"
					class="mb-0"
					></v-progress-linear>
				</v-card-text>
			</v-card>
		</v-dialog>
		
		<v-layout justify-center v-if="!loading">
			<v-flex xs12>
				<v-card color="grey lighten-4">
					<v-toolbar color="transparent" dense class="elevation-0">
						<v-toolbar-title>
							{{title}}
						</v-toolbar-title>
					</v-toolbar>			
					<v-card flat>
						<v-container>
							<v-layout column>
								<v-flex xs12>
									<v-alert :color="alert.type" dismissible :value="alert.show" outline v-show="alert.index === 0 && $route.name == alert.name" :icon="alert.type == 'error' ? 'warning' : 'priority_high' ">
										{{alert.message}}
									</v-alert>
								</v-flex>

								<v-flex xs12 md6>
									<v-text-field color="red accent-3"  
									prepend-icon="lock" 
									v-model.trim="editedItem.oldPassword" 
									label="Mật khẩu cũ" 
									type="password"
									v-validate="'required|min:8|max:36'"
									data-vv-name="oldPassword"
									:error-messages="errors.collect('oldPassword')"
									data-vv-delay="300"
									:type="showPassword ? 'text' : 'password'"
									:append-icon="showPassword ? 'visibility_off' : 'visibility'"
									@click:append="showPassword = !showPassword"></v-text-field>							
								</v-flex>
								<v-divider></v-divider>
								<v-flex xs12 md6>
									<v-text-field color="red accent-3"
									prepend-icon="lock" 
									v-model.trim="editedItem.newPassword" l
									label="Mật khẩu mới" 
									type="password"
									v-validate="'required|min:8|max:36'"
									data-vv-name="newPassword"
									:error-messages="errors.collect('newPassword')"
									data-vv-delay="300"
									:type="showPassword ? 'text' : 'password'"
									:append-icon="showPassword ? 'visibility_off' : 'visibility'"
									@click:append="showPassword = !showPassword"
									hint="Sử dụng 8 ký tự trở lên và kết hợp chữ cái, chữ số và biểu tượng"
									persistent-hint></v-text-field>
								</v-flex>
								<v-flex xs12 md6>
									<v-text-field color="red accent-3"  
									prepend-icon="lock" v-model.trim="confirm" 
									name="confirm" 
									label="Xác nhận mật khẩu mới" type="password"
									v-validate="{required: true, is:editedItem.newPassword}"
									data-vv-name="confirm"
									:error-messages="errors.collect('confirm')"
									data-vv-delay="300"
									:type="showPassword ? 'text' : 'password'"
									:append-icon="showPassword ? 'visibility_off' : 'visibility'"
									@click:append="showPassword = !showPassword"
									></v-text-field>
								</v-flex>
							</v-layout>
						</v-container>
					</v-card>
					<v-card-actions class="justify-center">
						<v-btn color="error" small round :to="{name: 'information'}">Quay lại</v-btn>
						<v-spacer></v-spacer>
						<v-btn color="success" round small :loading="process" :disabled="disabled" @click.stop="save">Hoàn thành</v-btn>
					</v-card-actions>				
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
import axios from 'axios'
import {mapState} from 'vuex'
import { Validator } from 'vee-validate';
import vietnam from 'vee-validate/dist/locale/vi';
export default {
	middleware: 'notAuthenticated',
	data() {
		return {
			loading: false,
			editedItem: {
				oldPassword: '',
				newPassword: '',
			},
			title: 'Thay đổi mật khẩu',
			confirm: '',
			showPassword:false,
			loading: false,
			process: false,
			locale: 'vi',
			disabled: true
		}
	},
	methods: {
		save: function() {
			var vm   = this
			var data = Object.assign({}, vm.editedItem)
			vm.$validator.validateAll().then(async function(result) {
				if(result) {
					vm.process = true
					axios.post('/api/Dofuu/GetUser/ChangePassword', data).then(response => {
						if(response.status === 200) {
							vm.editedItem = Object.assign({}, {
								oldPassword: '',
								newPassword: ''
							})
							vm.confirm = ''
							vm.$store.dispatch('alert', {index:0, name: vm.$route.name, message: response.data.message, type:"success", close:true })
						}
						if(response.status === 202) {
							console.log('ok')
							vm.$store.dispatch('alert', {index:0, name: vm.$route.name, message: response.data.message, type:"error", close:true })
						}
					}).finally(() => {
						vm.$validator.reset()
						vm.process = false
						vm.disabled = true
					})
				}
			})
		},
	},
	computed: {
		...mapState({
			alert: state => state.alertStore.alert
		})
	},
	watch: {
		'editedItem.oldPassword': function(val, oldVal) {
			var vm = this
			if(val != '') {
				this.disabled = false
			} else {
				this.disabled = true
			}
		},
		'editedItem.newPassword': function(val, oldVal) {
			var vm = this
			if(val != '') {
				this.disabled = false
			} else {
				this.disabled = true
			}
		}
	},
	created() {
		this.$validator.localize(this.locale, {
			messages:vietnam.messages,
			attributes: {
				'oldPassword': 'Mật khẩu cũ',
				'newPassword': 'Mật khẩu mới',
				'confirm': 'Xác nhận mật khẩu mới'
			}
		})
		this.$validator.localize(this.locale)
	},

}
</script>

<style>
#map {
	width: 100%;
	height: 250px;
	background: #F5F5F5
}
#google-autocomplete {
	width: 100%
}
</style>