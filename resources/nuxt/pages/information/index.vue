<template>
	<v-container :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-dialog v-model="loading" hide-overlay persistent width="300">
			<v-card	color="red darken-3" dark>
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
				<v-card color="grey lighten-4" class="card-radius">
					<v-toolbar color="white" dense class="elevation-0">
						<v-toolbar-title>
							Thông tin tài khoản
						</v-toolbar-title>
						<v-spacer></v-spacer>
						<v-tooltip top>
							<span slot="activator"><v-icon color="red accent-3">scatter_plot</v-icon> <span class="font-weight-bold">{{currentUser.points}}</span></span>
							<span>Điểm df dùng đổi thưởng</span>
						</v-tooltip>
					</v-toolbar>
					<v-container>
						<v-layout row wrap class="justify-center">

							<!-- <a @click.stop="$store.commit('SHOW_IMAGE_DIALOG')"> -->
								<v-avatar  size="150" color="grey" style="border" @mouseover="hoverImage = true" @mouseleave="hoverImage = false">
									<img :src="image(currentUser.image)" alt="avatar">
								</v-avatar>
								<v-badge color="blue-grey" top right overlap v-model="show">
									<v-icon slot="badge" dark >camera_alt</v-icon>
								</v-badge>	
								<!-- </a> -->
							</v-layout>
						</v-container>				
						<v-list>
							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="primary">person</v-icon>							
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.name}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="purple">cake</v-icon>							
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.birthday | formatDate}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="pink">wc</v-icon>							
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.gender ? 'Nam' : 'Nữ'}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="red">mail</v-icon>
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.email}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile >
								<v-list-tile-action>
									<v-icon color="green darken-3">phone</v-icon>
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.phone | formatPhone}}</v-list-tile-title>
								</v-list-tile-content>
								<v-list-tile-action @click.prevent="open">
									<v-btn color="blue" flat small>Chỉnh sửa</v-btn>
								</v-list-tile-action>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="indigo">location_on</v-icon>
								</v-list-tile-action>

								<v-list-tile-content>
									<v-list-tile-title>{{currentUser.address==null ? 'Chưa có' : currentUser.address}}</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>	
						<v-card-actions class="justify-center">
							<v-btn color="success" round small :to="{path: 'information/edit'}">Thay đổi thông tin</v-btn>
							<v-btn color="success" round small :to="{path: 'information/change-password'}">Thay đổi mật khẩu</v-btn>
						</v-card-actions>				
					</v-card>
				</v-flex>
			</v-layout>
			<vue-image></vue-image>
			<v-dialog v-model="showUpdate" persistent max-width="290" @keydown.esc="cancel">
				<v-card>
					<v-toolbar color="transparent" dense flat>
						<v-toolbar-title>{{ editedItem.title }}</v-toolbar-title>
					</v-toolbar>
					<v-divider></v-divider>
					<v-card-text>
						<v-text-field
						label="Số điện thoại"
						v-model="editedItem.phone"
						mask="(####) ### - ####"
						v-validate="'required|numeric|min:10|max:10'"	
						color="black"		            
						background-color="grey lighten-3"
						:error-messages="errors.collect('phone')"
						data-vv-name="phone">
						></v-text-field>
					</v-card-text>
					<v-divider></v-divider>
					<v-card-actions>
						<v-btn flat @click.native="cancel" color="error" small :loading="processUpdate" class="px-0" round>Hủy</v-btn>
						<v-spacer></v-spacer>
						<v-btn color="blue" @click.native="update" :disabled="disabled" :loading="processUpdate" small class="white--text px-0" round>Chấp nhận</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>
		</v-container>
	</template>

	<script>
		import ImageDialog from '@/components/ImageDialog'
		import index from '@/mixins/index'
		import axios from 'axios'
		import {mapState} from 'vuex'
		import vietnam from 'vee-validate/dist/locale/vi';
		const endpoint = 'authEndPoint'
		export default {
			middleware: 'notAuthenticated',
			mixins: [index],
			components: {
				'vue-image': ImageDialog
			},
			data() {
				return {
					loading: false,
					hoverImage:false,
					showImage:false,
					showUpdate: false,
					editedItem: {
						title: 'Cập nhật số điện thoại',
						phone: ''
					},
					locale: 'vi',
					disabled: true,
					processUpdate: false
				}
			},
			methods: {
				open: function() {
					var vm              = this
					vm.showUpdate       = !vm.showUpdate
					vm.editedItem.phone = vm.currentUser.phone
				},
				cancel: function() {
					var vm              = this
					vm.showUpdate       = !vm.showUpdate
					vm.editedItem.phone = ''
					vm.disabled			= true
					vm.$validator.reset()
				},
				update: function() {
					var vm        = this
					const data    = { phone: vm.editedItem.phone }
					const params  = { endpoint: endpoint }
					vm.$validator.validateAll().then(async function(result) {
						if(result) {
							if(!vm.processUpdate) {
								vm.processUpdate = !vm.processUpdate
								setTimeout(() => {
									axios.post('/api/Auth/PhoneNumber/Update', data, {params, withCredentials: true}).then(response => {
										if(response.status == 200) {
											vm.currentUser.phone = vm.editedItem.phone
											vm.cancel()
										}
									}).finally(() => {
										vm.processUpdate = !vm.processUpdate
									})
								}, 100)
							}
						}
					})
				}
			},
			computed: {
				...mapState({
					currentUser: state => state.authStore.currentUser
				}),
				show: function() {
					if(this.$vuetify.breakpoint.smAndDown) {
						return true
					}
					if(this.hoverImage) {
						return true
					} else {
						return false
					}
				}
			},
			watch: {
				'editedItem.phone': function(val, oldVal) {
					if(val != '' && oldVal != '' && val.length === 10) {
						this.disabled = false
					} else {					
						this.disabled = true
					}
				},
			},
			created() {
				this.$validator.localize(this.locale, {
					messages:vietnam.messages,
					attributes: {
						phone: 'Số điện thoại'
					}
				})
				this.$validator.localize(this.locale)

				this.loading = true
				this.$store.dispatch('getUser').finally(() => {
					this.loading = false
				})
			}
		}
	</script>