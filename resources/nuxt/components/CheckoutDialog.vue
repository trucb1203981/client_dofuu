<template>
	<v-layout>
		<v-dialog :value="show" fullscreen hide-overlay  persistent transition="dialog-bottom-transition" v-if="store != null">
			<v-card>
				<v-toolbar dense flat fixed>
					<v-btn icon @click.native="$store.commit('CLOSE_CHECKOUT')">
						<v-icon color="grey">close</v-icon>
					</v-btn>
					<!-- MOBILE -->
					<v-toolbar-title class="hidden-md-and-up subheading">Xác nhận</v-toolbar-title>
					<!-- DESKTOP -->
					<v-toolbar-title class="hidden-sm-and-down">Xác nhận đơn đặt hàng</v-toolbar-title>
					<v-spacer></v-spacer>
					<v-btn color="primary" :disabled="editedItem.address == null" @click.prevent="save">
						Hoàn thành | {{total | formatPrice}}
						<v-icon >chevron_right</v-icon>
					</v-btn>
				</v-toolbar>
				<v-container fluid grid-list-md class="pt-5">
					<v-layout class="grey lighten-2" row wrap>
						<v-flex md6 >
							<v-flex d-flex>
								<v-card height="250" color="grey" >
									<div id="map"></div>
								</v-card>
							</v-flex>
							<v-flex d-flex>
								<v-card  :bind="$vuetify.breakpoint.mdAndUp ? `height='370'`: ''">
									<v-card-text>
										<v-layout row wrap>
											<v-flex xs12 md6>
												<v-text-field prepend-icon="person" label="Họ tên" v-model="editedItem.name"></v-text-field>
											</v-flex>

											<v-flex xs12 md6>
												<v-text-field prepend-icon="phone" label="Số điện thoại" v-model="editedItem.phone"></v-text-field>	
											</v-flex>

											<v-flex xs12 md12>
												<v-layout row wrap>
													<v-flex :bind="$vuetify.breakpoint.mdAndUp ? 'xs8' : 'xs10'">
														<v-text-field prepend-icon="place" placeholder="Địa chỉ nhận" v-model="editedItem.address" id="auto-complete" ref="autocomplete"></v-text-field>
													</v-flex>
													<!-- DESKTOP -->
													<v-flex xs4 class="hidden-sm-and-down">
														<v-btn small :loading="loadingLocation" color="primary" dark @click="currentLocation" >
															<span>Vị trí hiện tại</span>
															<v-icon right>gps_fixed</v-icon>
														</v-btn>														
													</v-flex>
													<!-- MOBILE -->													
													<v-flex xs2 class="hidden-md-and-up">
														<v-btn :loading="loadingLocation" color="primary" dark @click="currentLocation" icon>
															<v-icon>gps_fixed</v-icon>
														</v-btn>
													</v-flex>												
												</v-layout>
											</v-flex>

											<v-flex xs12 md6>
												<v-dialog
												ref="dialog"
												persistent
												v-model="modal"
												lazy
												full-width
												width="290px"
												:return-value.sync="editedItem.date"
												>
												<v-text-field
												slot="activator"
												label="Ngày nhận"
												v-model="datestring"
												prepend-icon="event"
												readonly
												></v-text-field>
												<v-date-picker v-model="editedItem.date" scrollable locale="vn-vi">
													<v-spacer></v-spacer>
													<v-btn flat color="primary" @click="modal = false" >Cancel</v-btn>
													<v-btn flat color="primary" @click="$refs.dialog.save(editedItem.date)">OK</v-btn>
												</v-date-picker>
											</v-dialog>
										</v-flex>

										<v-flex md6>
											<v-menu
											ref="menu"
											:close-on-content-click="false"
											v-model="menuTime"
											:nudge-right="40"
											:return-value.sync="editedItem.time"
											lazy
											transition="scale-transition"
											offset-y
											full-width
											max-width="290px"
											min-width="290px">
											<v-text-field
											slot="activator"
											v-model="editedItem.time"
											label="Thời gian nhận"
											prepend-icon="access_time"
											readonly
											:hint="`Thời gian dự kiến là ${intendTime} phút`"
											persistent-hint></v-text-field>

											<v-time-picker
											v-model="editedItem.time"
											:allowed-hours="allowedHours"
											:allowed-minutes="allowedMinutes"
											format="24hr"
											scrollable
											:min="minTime"
											:max="maxTime" actions>
											<v-spacer></v-spacer>
											<v-btn flat color="primary" @click="modal2 = false">Cancel</v-btn>
											<v-btn flat color="primary" @click="$refs.menu.save(editedItem.time)">OK</v-btn></v-time-picker>
										</v-menu>										
									</v-flex>

									<v-flex>
										<v-text-field
										label="Ghi chú"
										v-model="editedItem.memo"
										prepend-icon="short_text"
										multi-line
										:rows="2"
										:row-height="25"
										></v-text-field>
									</v-flex>
								</v-layout>	
							</v-card-text>
						</v-card>
					</v-flex>
				</v-flex>
				<v-flex xs12 md6>
					<v-flex d-flex>
						<v-card height="250" flat color="white">
							<v-toolbar dense flat color="white" class="elevation-0" card>
								<v-toolbar-title>
									Chi tiết đơn hàng
								</v-toolbar-title>
							</v-toolbar>
							<v-divider></v-divider>
							<v-card-text class="scroll-y" style="max-height:200px">
								<v-list subheader>
									<v-list-tile avatar v-for="item in cart.items" :key="item.name" @click="">
										<v-list-tile-avatar>
											<v-avatar class="red" size="18">
												<span class="white--text">{{item.qty}}</span>
											</v-avatar>
										</v-list-tile-avatar>
										<v-list-tile-content>
											<v-list-tile-title>{{item.name}}</v-list-tile-title>
										</v-list-tile-content>
										<v-list-tile-action>
											<strong>{{item.price | subPrice(item.qty)}}</strong>
										</v-list-tile-action>
									</v-list-tile>
								</v-list>
							</v-card-text>
							<v-divider></v-divider>
						</v-card>
					</v-flex>

					<v-flex d-flex>
						<v-card height="370">
							<v-card-text>
								<v-list>
									<v-list-tile>
										<v-list-tile-content>
											<span>Tổng <strong class="red--text">{{counts}}</strong> món:</span>
										</v-list-tile-content>
										<v-list-tile-content>
											<v-list-tile-title></v-list-tile-title>
										</v-list-tile-content>
										<v-list-tile-action >
											<strong>{{subTotal | formatPrice}}</strong>
										</v-list-tile-action>
									</v-list-tile>
									<v-list-tile>
										<v-list-tile-content>
											<span>Phí vận chuyển: <strong class="red--text">{{matrix.distance}}</strong> <v-icon>help</v-icon></span>
										</v-list-tile-content>
										<v-list-tile-content>
											<v-list-tile-title></v-list-tile-title>
										</v-list-tile-content>
										<v-list-tile-action>
											<strong>{{deliveryPrice | formatPrice}}</strong>
										</v-list-tile-action>
									</v-list-tile>
									
									<v-list-tile>
										<v-list-tile-content>
											<span>Mã khuyến mãi:</span>
										</v-list-tile-content>					
									</v-list-tile>
									
									<v-list-tile class="yellow accent-3">		
										<v-list-tile-content>
											<v-text-field solo class="my-1" flat color="purple" v-model="code" label="Nhập mã"></v-text-field>
										</v-list-tile-content>
										<v-btn small color="primary" @click.stop="checkCoupon()" :loading="loadingCoupon">Áp dụng</v-btn>				
										<v-list-tile-action>
											<strong class="red--text text--accent-3">
												<span v-if="coupon.secret != null">(Giảm {{coupon.discountPercent}}%)</span>
												{{dealPrice | formatPrice}}			
											</strong> 
										</v-list-tile-action>
									</v-list-tile>
								</v-list>
								<span v-if="alert.show" class="red--text text-lg-right">{{alert.message}}</span>
							</v-card-text>
							<v-divider></v-divider>
							<v-card-text>
								<v-list>
									<v-list-tile>
										<v-list-tile-content>
											Thành tiền:
										</v-list-tile-content>
										<v-list-tile-content></v-list-tile-content>
										<v-list-tile-action><strong>{{total |formatPrice}}</strong></v-list-tile-action>
									</v-list-tile>
									<v-list-tile>
										<v-list-tile-content>
											Phương thức thanh toán:
										</v-list-tile-content>
										<v-list-tile-content></v-list-tile-content>
										<v-list-tile-action>Tiền mặt</v-list-tile-action>
									</v-list-tile>
								</v-list>
							</v-card-text>

						</v-card>
					</v-flex>
				</v-flex>
			</v-layout>	
		</v-container>
	</v-card>
</v-dialog>
<v-dialog v-model="dialog" max-width="400">
	<v-card>
		<v-toolbar dense flat class="elevation-0">
			<v-toolbar-title>
				<v-icon>notifications_active</v-icon>Thông báo
			</v-toolbar-title>
		</v-toolbar>
		<v-divider></v-divider>
		<v-card-text >
			<div><strong>Dofuu xin lỗi quý khách hàng !</strong></div>
			<div>
				Phạm vi giao hàng của dofuu tối đa <strong class="red--text">{{maxRange}}  km</strong>
			</div>
			<div><strong>Mong quý khách thông cảm. Cám ơn !</strong></div>
		</v-card-text>
		<v-card-actions>
			<v-btn color="green darken-1"  dark @click.native="dialog = false" block>Đồng ý</v-btn>
		</v-card-actions>
	</v-card>
</v-dialog>
</v-layout>
</template>
<script>
import {mapState} from 'vuex'
import numeral from 'numeral'
import moment from 'moment'
import AutoComplete from '@/components/AutoComplete'
import axios from 'axios'
import {getHeader} from '@/config'
function formatDate(str) {
	if(str != null) {
		return str.substring(8, 10)+'/'+str.substring(5, 7)+'/'+str.substring(0, 4)
	}
	return null
}
export default {
	components: {
		'vue-autocomplete' : AutoComplete
	},
	props: ['store'],
	data() {
		return {
			code: null,
			editedItem: {
				name: '',
				phone: '',
				address: null,
				lat: 0,
				lng: 0,
				date: new Date().toISOString().substr(0, 10),
				time: null,
				memo: 'Càng sớm càng tốt',
				googleAddress: ''
			},
			modal: false,
			position: {
				lat:0,
				lng:0
			},
			center: {
				lat: 0,
				lng: 0
			},
			matrix: {
				distance:'0 km',
				duration:'0 phút'
			},
			deliveryPrice: 0,
			coupon: {
				secret: null
			},
			datestring: formatDate(new Date().toISOString().substr(0, 10)),
			alert: {
				show: false,
				message: '',
				type: 'error'
			},
			loadingCoupon: false,
			loadingCheckout: false,
			loadingLocation:false,
			disabled: true,
			menuTime: false,
			dialog:false,
			maxRange: 20
		}
	},
	methods: {
		//CHECK COUPON
		checkCoupon: async function() {
			var vm = this
			if(vm.code == null) {
				vm.alert = Object.assign({}, {show: true, message: 'Vui lòng nhập mã giảm giá', type: 'error'})
			} else {
				const data = Object.assign({}, {storeID: this.store.id, coupon: this.code, subTotal: this.subTotal, districtID: this.store.districtId, cityID: this.currentCity.id, 'deliveryPrice': this.deliveryPrice})
				vm.loadingCoupon = await !vm.loadingCoupon
				await setTimeout(function() {
					axios.post('/api/Dofuu/CheckCouponCode', data, {headers: getHeader(), withCredentials:true}).then(response => {
						if(response.data.type === 'success') {
							vm.coupon = response.data.data
							vm.coupon = Object.assign(vm.coupon, {'secret': response.data.secret})
						}
						vm.alert = Object.assign({}, {show: true, message: response.data.message, type: 'error'})
					})
				},300)
				setTimeout(() => {
					vm.loadingCoupon = !vm.loadingCoupon
				}, 500)
				setTimeout(() => {
					vm.alert = Object.assign({}, {show: false, message: '', type: 'error'})
				},3000)			
			}
			
		},
		autoComplete() {
			var vm           = this
			var flag 		 = false
			var input        = document.getElementById('auto-complete')
			var autocomplete = new google.maps.places.Autocomplete(input)
			autocomplete.addListener('place_changed', function() {
				var place = autocomplete.getPlace()
				if(!place.geometry) {
					var geocoder = new google.maps.Geocoder();
					geocoder.geocode({address: input.value}, function(results, status) {
						if(status === 'OK') {
							vm.calculateRoute({lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()})
						}
					})
					return
				}
				vm.setPlace(place)
			})
		},
		setPlace(place) {
			this.place = place
			if(this.place) {
				
				this.position 	= {
					lat: this.place.latitude,
					lng: this.place.longitude
				}

				this.center 	= {
					lat: this.place.latitude,
					lng: this.place.longitude
				}
				this.editedItem.address = this.place.formatted_address
				this.editedItem.lat     = this.place.geometry.location.lat()
				this.editedItem.lng     = this.place.geometry.location.lng()
				this.calculateRoute({lat: this.place.geometry.location.lat(), lng: this.place.geometry.location.lng()})
			}
		},
		calculateRoute:async function(des) {
			var vm  = this
			var map = await new google.maps.Map(document.getElementById('map'), {
				zoom: 17,
				center: {lat:vm.store.lat, lng:vm.store.lng}
			});
			var directionsService = new google.maps.DirectionsService;
			var directionsDisplay = new google.maps.DirectionsRenderer;
			
			directionsDisplay.setMap(null)
			directionsDisplay.setMap(map)

			var start   = new google.maps.LatLng(vm.store.lat, vm.store.lng);
			var end     = new google.maps.LatLng(des.lat, des.lng);
			var request = {
				origin: start,
				destination: end,
				travelMode: google.maps.DirectionsTravelMode.DRIVING
			}
			directionsService.route(request, function(response, status) {
				if (status === 'OK') 
				{				
					directionsDisplay.setDirections(response);
					var response = response
					var service  = new google.maps.DistanceMatrixService()
					service.getDistanceMatrix(
					{
						origins: [response.request.origin.location],
						destinations: [response.request.destination.location],
						travelMode: 'DRIVING'
					},async function(res, status) {
						var leg            = response.routes[ 0 ].legs[ 0 ];
						if (status === 'OK') {
							var originList      = res.originAddresses
							var destinationList = res.destinationAddresses
							var distance        = '0 Km'
							var duration        = '0 phút'
							for (var i = 0; i < originList.length; i++) {
								var results = res.rows[i].elements
								for (var j = 0; j < results.length; j++) {
									var element = results[j]
									distance    = element.distance.text
									duration    = element.duration.text
								}
								var arrayDistance = distance.split(' ')
								if(distance.split(' ')[1] == 'm') {
									arrayDistance[0]  = await numeral(distance.split(' ')[0]).value() / 1000
									distance          = await arrayDistance.join(' ')
									vm.editedItem.lat = leg.end_location.lat()
									vm.editedItem.lng = leg.end_location.lng()
									directionsDisplay.setDirections(response);
								} else {
									if(numeral(distance.split(' ')[0]).value() > vm.currentCity.service.maxRange)
									{
										directionsDisplay.setMap(null)
										var map = new google.maps.Map(document.getElementById('map'), {
											zoom: 17,
											center: {lat:vm.store.lat, lng:vm.store.lng}
										});
										var marker = new google.maps.Marker({
											position: {lat:vm.store.lat, lng:vm.store.lng},
											map: map,
											title: 'Hello World!'
										});
										vm.disabled           = true
										vm.editedItem.address = null	
									} else {
										vm.editedItem.lat  = leg.end_location.lat()
										vm.editedItem.lng  = leg.end_location.lng()
										directionsDisplay.setDirections(response);
									}
								}
								vm.matrix.distance = distance
								vm.matrix.duration = duration
							}
						}					
					})

				} else {
					window.alert('Directions request failed due to ' + status);
				}
				var leg   = response.routes[ 0 ].legs[ 0 ];
			})
		},
		currentLocation: function() {
			var vm             = this
			var geocoder       = new google.maps.Geocoder();
			vm.loadingLocation = true
			if(navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(async function(position){
					vm.editedItem.lat = position.coords.latitude
					vm.editedItem.lng = position.coords.longitude
					var latlng        = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
					await geocoder.geocode({'location': latlng}, function(results, status) {
						if(status == 'OK') {
							vm.editedItem.address = results[0].formatted_address
							vm.calculateRoute({lat: position.coords.latitude, lng: position.coords.longitude})
						}

					})
					vm.loadingLocation = false
				})
			}
		},
		allowedHours: function(v) {
			return v
		},
		allowedMinutes: function(v) {			
			return v == 0 || v == 15 || v == 30 || v == 45
		},
		save: function() {
			var cart = {
				instance: this.store.id,
				items: []
			}
			var data = {
				'userId':this.user.id,
				'confirmed': true,
				'name': this.editedItem.name, 
				'phone': this.editedItem.phone, 
				'address': this.editedItem.address,
				'lat': this.editedItem.lat,
				'lng': this.editedItem.lng,
				'date': this.editedItem.date, 
				'time': this.editedItem.time, 
				'memo': this.editedItem.memo, 
				'items' : this.cart.items,
				'subTotal': this.subTotal,
				'total': this.total,
				'store': this.store,
				'city': this.city,
				'estimateTime': this.intendTime,
				'distance': numeral(this.matrix.distance.split(' ')[0]).value(),
				'paymentMethod': 1
			}
			if(this.coupon.secret != null) {
				data = Object.assign(data, {'coupon': this.coupon, 'secret': this.coupon.secret})
			}
			axios.post('/api/Dofuu/CheckOut', data, {headers: getHeader(), withCredentials:true}).then(async (response) => {
				if(response.status == 201) {
					await window.localStorage.setItem('cart', JSON.stringify(cart))
					await this.$store.commit('FETCH_CART', cart)
					this.$store.commit('CLOSE_CHECKOUT')
				}
			})
		}
	},
	computed: {
		...mapState({
			city: state        => state.cityStore.currentCity,
			show : state       => state.cartStore.dialog,
			cart: state        => state.cartStore.cart,
			user: state        => state.authStore.currentUser,
			currentCity: state => state.cityStore.currentCity
		}),
		counts: function() {
			return this.$store.getters.counts
		},
		subTotal: function() {
			return this.$store.getters.subTotal
		},
		total: function() {
			return Math.floor(numeral(this.subTotal).value() + numeral(this.deliveryPrice).value() + (this.dealPrice))
		},
		dealPrice: function() {
			if(this.coupon != null) {
				return -Math.floor(numeral(this.subTotal).value() + numeral(this.deliveryPrice).value())*(numeral(this.coupon.discountPercent).value()/100)
			}
			return 0
		},
		minTime: function() {
			var activities = this.store.activities // Active time of store
			var day        = null // Day current of store
			var now        = moment().locale('vi') // Date time now 
			var n          = now.day()		// Day of week 
			var hs         = null // Hour start of store 
			var regex      = new RegExp(':', 'g') // Regular 
			// Find activity of store in current day
			day            = activities.find(item => {
				return item.number == n
			})
			// Find start time of store in current day
			day.times.filter(item => {
				if(parseInt(item.from.replace(regex, ''),10) > parseInt(now.format('HH:mm').replace(regex, ''),10)) {
					hs = item.from
				} else {
					hs = now.format('HH:mm')
				}
			})
			return hs
		},
		maxTime: function() {
			var activities = this.store.activities // Active time of store
			var day        = null // Day current of store
			var now        = moment().locale('vi') // Date time now 
			var n          = now.day()		// Day of week 
			var he         = null // End time of store 
			var regex      = new RegExp(':', 'g') // Regular 
			// Find activity of store in current day
			day            = activities.find(item => {
				return item.number == n
			})
			// Find end time of store in current day
			day.times.filter(item => {
				if(item.to) {
					he = item.to
				}
			})
			return he
		},
		intendTime: function() {
			const totalTime = numeral(this.matrix.duration.slice(0,-5)).value() + numeral(this.store.prepareTime).value() // Total time of prepare time and duration time
			var now         = moment().locale('vi') // Date time current
			var intendTime  = now.add(totalTime, 'minutes') //Intent time when after direction
			var mm          = Math.floor(parseInt(intendTime.format('mm'))/5) // Calculate minutes of intentime divide 5
			// Less than 15 minutes
			var time        = null
			
			if(mm >= 0 && mm < 3) {
				var minTemp = Math.floor(15 - parseInt(intendTime.format('mm')))	
				time = intendTime.add(minTemp, 'minutes')
			} else if(mm >= 3 && mm < 6) {
				var minTemp = Math.floor(30 - parseInt(intendTime.format('mm')))	
				time = intendTime.add(minTemp, 'minutes')
			} else if(mm >= 6 && mm < 9) {
				var minTemp = Math.floor(45 - parseInt(intendTime.format('mm')))	
				time = intendTime.add(minTemp, 'minutes')
			} else if (mm >= 9 && mm < 12) {
				var minTemp = Math.floor(60 - parseInt(intendTime.format('mm')))	
				time = intendTime.add(minTemp, 'minutes')
			}
			this.editedItem.time = time.format('HH:mm')

			return totalTime
		}
	}, 
	filters: {
		formatPrice(price) {
			return numeral(price).format('0,0$')
		},
	},
	watch: {
		'show': function(val) {
			if(val) {
				var vm = this
				vm.autoComplete()
				setTimeout(() => {
					var map 	= new google.maps.Map(document.getElementById('map'), {
						zoom: 17,
						center: {lat:vm.store.lat, lng:vm.store.lng}
					});

					var marker 	= new google.maps.Marker({
						position: {lat:vm.store.lat, lng:vm.store.lng},
						map: map,
						title: 'Hello World!'
					});
				})
				
				if(vm.user.address != null) {
					setTimeout(() => {
						vm.calculateRoute(vm.user.address)
					}, 500)
				} else {

				}	
			} 
		},
		'matrix.distance': function(val) {
			var distance = parseFloat(val.split(' ')[0])
			if(val) {
				if(distance > this.currentCity.service.maxRange) {
					this.dialog   = true
					this.maxRange = this.currentCity.service.maxRange
				} else {
					if(this.currentCity.service.deliveryActived) {

						this.currentCity.deliveries.forEach(item => {

							if(item.from <= distance && item.to >= distance && this.currentCity.service.minRange >= distance) {

								this.deliveryPrice = parseFloat(item.price)

							} else if(item.from <= distance && item.to >= distance && this.currentCity.service.maxRange >= distance && this.currentCity.service.minRange < distance){

								this.deliveryPrice = Math.floor(parseFloat(item.price)*distance)
							}
						})

					} else {
						this.deliveryPrice = 0
					}
				}				
			}
		},
		'editedItem.address': function(val) {

		},
		'user': function(val) {
			if(val) {
				this.editedItem = Object.assign(this.editedItem, {name:val.name, phone: val.phone, address:val.address, lat: val.lat, lng: val.lng})
			}
		},
		'editedItem.date': function(val) {
			this.datestring = formatDate(val)
		}
	}
}
</script>

<style>
#map {
	width: 100%;
	height: 250px;
}
#google-autocomplete {
	width: 100%
}

</style>