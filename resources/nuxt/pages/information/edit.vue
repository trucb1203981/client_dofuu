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
				<v-card color="grey lighten-4 card-radius">
					<v-toolbar color="transparent" dense class="elevation-0">
						<v-toolbar-title>
							Chỉnh sửa thông tin
						</v-toolbar-title>
					</v-toolbar>			
					<v-card flat>
						<v-container>

							<v-alert :color="alert.type" dismissible :value="alert.show" outline v-show="alert.index === 0 && $route.name == alert.name" :icon="alert.type == 'error' ? 'warning' : 'priority_high' ">
								{{alert.message}}
							</v-alert>

							<v-layout column>
								<v-flex xs12 md6>
									<v-text-field
									color="red accent-3" 
									prepend-icon="person"
									v-validate="'required|max:50'"
									v-model="editedItem.name"
									:counter="50"
									:error-messages="errors.collect('name')"
									label="Họ tên"
									data-vv-name="name"
									required
									></v-text-field>									
								</v-flex>

								<v-flex xs12 md6>
									<v-menu	ref="menu" lazy :close-on-content-click="false" v-model="menu" transition="scale-transition" offset-y full-width :nudge-right="40" min-width="290px">
										<v-text-field
										color="red accent-3" 
										slot="activator"
										label="Ngày sinh"
										v-model="editedItem.birthday"
										prepend-icon="event"
										readonly
										v-validate="'required'"
										data-vv-name="birthday"
										:error-messages="errors.collect('birthday')"
										data-vv-delay="300"
										data-vv-scope="user"></v-text-field>
										<v-date-picker
										color="red darken-3"
										ref="picker"
										locale="vn-vi"
										v-model="editedItem.birthday"
										@change="changeDate"
										min="1950-01-01"
										:max="new Date().toISOString().substr(0, 10)"></v-date-picker>
									</v-menu>
								</v-flex>

								<v-flex xs12 md6>
									<v-radio-group 
									prepend-icon="wc"
									v-model="editedItem.gender" 
									row
									v-validate="{required:true}"
									:error-messages="errors.collect('gender')"
									data-vv-name="gender" 
									data-vv-scope="user">
									<v-radio color="red accent-3" label="Nam" :value="true" 
									@change="changeAttribute"></v-radio>
									<v-radio color="red accent-3" label="Nữ" :value="false" @change="changeAttribute"
									></v-radio></v-radio-group>
								</v-flex>

								<v-flex xs12 md6>
									<v-text-field
									id="auto-complete"
									color="red accent-3" 
									prepend-icon="location_on"
									v-validate="'required|max:100'"
									v-model="editedItem.address"
									:counter="100"
									:error-messages="errors.collect('address')"
									label="Địa chỉ"
									data-vv-name="address"
									@focus="autoComplete"
									required
									></v-text-field>									
								</v-flex>

								<v-flex xs12 md6 v-if="editedItem.address != null">
									<v-subheader >Chúng tôi lấy vị trí của bạn để tiện cho việc đặt và giao hàng.</v-subheader>
									<v-slide-y-transition>
										<div id="map"></div>	
									</v-slide-y-transition>
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
	import {getLocation, geocoder} from '@/utils/index'
	import index from '@/mixins/index'
	import {mapState} from 'vuex'
	import vietnam from 'vee-validate/dist/locale/vi';
	export default {
		middleware: 'notAuthenticated',
		mixins: [index],
		data() {
			return {
				loading: false,
				editedItem: {
					name: '',
					email: '',
					gender: false,
					birthday: '',
					phone: '',
					address: null,
					lat: 0,
					lng: 0
				},
				confirm: '',
				menu:false,
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
						if(!vm.process) {
							vm.process = true
							setTimeout(() => {
								axios.post('/api/Dofuu/GetUser/EditInformation', data).then(response => {
									if(response.status === 200) {
										vm.editedItem = Object.assign({}, response.data.data)
										vm.$store.dispatch('alert', {index:0, name: vm.$route.name, message: response.data.message, type:"success", close:true })
									}
								}).finally(() => {
									vm.process = false
									vm.disabled = true
								})
							}, 100)
						}
					}
				})
			},
			getUser() {
				this.loading = true
				axios.get('/api/Dofuu/GetUser', {withCredentials:true}).then(response=> {
					if(response.status === 200) {
						this.editedItem = Object.assign({}, response.data.data)
					}
				}).finally(() => {
					this.loading  = false
				})
			},
			autoComplete() {
				var vm           = this
				var marker       = null
				var input        = document.getElementById('auto-complete')
				var autocomplete = new google.maps.places.Autocomplete(input)
				autocomplete.addListener('place_changed', function() {
					var place = autocomplete.getPlace()
					if(!place.geometry) {
						geocoder('address', input.value).then(response => {
							vm.editedItem.address = response[0].formatted_address.slice(0, -10)				
							vm.editedItem.lat     = response[0].geometry.location.lat()
							vm.editedItem.lng     = response[0].geometry.location.lng()
						})

						vm.googleMap(vm.editedItem)

						return
					}
					vm.editedItem.address = place.formatted_address	
					vm.editedItem.lat     = place.geometry.location.lat()
					vm.editedItem.lng     = place.geometry.location.lng()
					vm.googleMap(vm.editedItem)

				})
			},
			googleMap(location) {

				var latlng 			  = new google.maps.LatLng(location.lat, location.lng);

				var map               = new google.maps.Map(document.getElementById('map'), {
					zoom: 17,
					center: latlng
				})

				var marker 			  = new google.maps.Marker({
					position: latlng,
					map: map
				});
			},
			changeDate() {
				this.$refs.menu.save(this.editedItem.birthday)
				this.disabled = false
			},
			changeAttribute() {
				this.disabled = false
			}
		},
		computed: {
			...mapState({
				currentUser: state => state.authStore.currentUser,
				alert: state       => state.alertStore.alert
			})
		},
		watch: {
			menu(val) {
				val && this.$nextTick(() => (this.$refs.picker.activePicker = 'YEAR'))
			},
			'editedItem.name': function(val, oldVal) {
				var vm = this
				if(oldVal != '') {
					this.disabled = false
				} else {
					this.disabled = true
				}
			},
			'editedItem.address': function(val, oldVal) {
				var vm = this
				if(val.length > 15) {
					geocoder('address', val).then(response => {			
						vm.editedItem.lat     = response[0].geometry.location.lat()
						vm.editedItem.lng     = response[0].geometry.location.lng()
					})
					setTimeout(() => {					
						vm.googleMap(vm.editedItem)
					}, 1000)

				} 
				if(oldVal != null) {
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
					name: 'Họ tên',
					birthday: 'Ngày sinh',
					gender: 'Giới tính',
					address: 'Địa chỉ'
				}
			})
			this.$validator.localize(this.locale)
			this.getUser()
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