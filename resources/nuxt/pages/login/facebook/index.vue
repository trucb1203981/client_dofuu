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

						<v-form>
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
								<v-text-field color="red accent-3"  prepend-icon="lock" v-model.trim="editedItem.password" label="Mật khẩu" id="password" type="password"
								v-validate="'required|min:8|max:36'"
								data-vv-name="password"
								:error-messages="errors.collect('password')"
								data-vv-delay="300"
								hint="Sử dụng 8 ký tự trở lên và kết hợp chữ cái, chữ số và biểu tượng"
								persistent-hint></v-text-field>
							</v-flex>

							<v-flex xs12>
								<v-text-field color="red accent-3"  prepend-icon="lock" v-model.trim="confirm" name="confirm" label="Xác nhận mật khẩu" id="confirm" type="password"
								v-validate="{required: true, is:editedItem.password}"
								data-vv-name="confirm"
								:error-messages="errors.collect('confirm')"
								data-vv-delay="300"
								></v-text-field>
							</v-flex>
						</v-form>
						<v-spacer></v-spacer>
					</v-card-text>
					<v-card-actions>
						<v-btn round color="success" dark block :loading="loading" @click.stop.prevent="save" small>Hoàn thành</v-btn>
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
export default {
	data() {
		return {
			editedItem: {
				phone: '',
				password: ''
			},
			confirm: '',
			loading: false
		}
	},
	computed: {
		...mapState({
			alert: state => state.alertStore.alert,
			user: state  => state.authStore.tempUser
		})
	},
	methods: {
		save() {
			this.user.birthday = moment(this.user.birthday).format('YYYY-MM-DD')
			var data = Object.assign(this.editedItem, this.user)
			axios.post('/api/facebook/register', data).then(response => {
				if(response.status === 201) {

				}
			})
		}
	},
	watch: {
		
	},
	created() {
	
	}
}
</script>