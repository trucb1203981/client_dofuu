<template>
	<div>
		<v-dialog :value="show" fullscreen hide-overlay  persistent transition="scale-transition" origin="center center" v-if="store != null" >
			<v-card>
				<v-toolbar dense flat fixed>
					<v-btn icon @click.native="$store.commit('CLOSE_CHECKOUT')">
						<v-icon color="grey">close</v-icon>
					</v-btn>

					<v-toolbar-title class="hidden-md-and-up subheading">Xác nhận</v-toolbar-title>

					<v-toolbar-title class="hidden-sm-and-down">Xác nhận đơn đặt hàng</v-toolbar-title>
					<v-spacer></v-spacer>
					<v-btn color="green darken-3 white--text" :disabled="disabledCheckout" :loading="processCheckout" @click.prevent="save" small round>
						Hoàn thành | {{total | formatPrice}}
						<v-icon >chevron_right</v-icon>
					</v-btn>
				</v-toolbar>
				<v-container fluid grid-list-md class="pt-5">
					<v-layout class="grey lighten-2" row wrap>
						<v-flex md6 >

							<v-flex xs12 order-md2 order-xs2>
								<v-card height="250" color="grey" >
									<div id="map"></div>
								</v-card>
							</v-flex>

							<v-flex d-flex  xs12 order-md1 order-xs1>
								<v-card :bind="$vuetify.breakpoint.mdAndUp ? `height='370'`: ''">
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
														<v-text-field prepend-icon="place" placeholder="Địa chỉ nhận" v-model="editedItem.address" id="auto-complete" ref="autocomplete" append-outer-icon="gps_fixed" @click:append-outer="currentLocation"
														:hint="`Chọn nút phía bên phải trường địa chỉ nhận sẽ xác định vị trí hiện tại`"
														@focus="autoComplete"
														persistent-hint></v-text-field>
													</v-flex>											
												</v-layout>
											</v-flex>

											<v-flex xs12 md6>
												<v-dialog ref="dialog" persistent v-model="modal" lazy full-width width="290px" :return-value.sync="editedItem.date" >
													<v-text-field slot="activator" label="Ngày nhận" v-model="datestring" prepend-icon="event" readonly ></v-text-field>
													<v-date-picker v-model="editedItem.date" scrollable locale="vn-vi">
														<v-spacer></v-spacer>
														<v-btn flat color="primary" @click="modal = false" >Hủy</v-btn>
														<v-btn flat color="primary" @click="$refs.dialog.save(editedItem.date)">Chọn</v-btn>
													</v-date-picker>
												</v-dialog>
											</v-flex>

											<v-flex md6>
												<v-menu ref="menu" :close-on-content-click="false" v-model="menuTime" :nudge-right="40" :return-value.sync="editedItem.time" lazy transition="scale-transition" offset-y full-width max-width="290px" min-width="290px">
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
													<v-btn flat color="primary" @click="menuTime = false">Hủy</v-btn>
													<v-btn flat color="primary" @click="$refs.menu.save(editedItem.time)">Chọn</v-btn></v-time-picker>
												</v-menu>										
											</v-flex>

											<v-flex xs12>
												<v-textarea
												prepend-icon="short_text"
												v-model="editedItem.memo"
												label="Ghi chú"
												hint="Vui lòng dặn dò nếu có"
												rows="3"
												persistent-hint
												></v-textarea>
											</v-flex>
										</v-layout>	
									</v-card-text>
								</v-card>
							</v-flex>
						</v-flex>
						<v-flex xs12 md6>
							<v-flex d-flex>
								<v-card height="250" flat color="white">
									<v-toolbar dense extended flat color="white" class="elevation-0" card>

										<v-toolbar-title>
											Chi tiết đơn hàng
										</v-toolbar-title>

										<v-list slot="extension" class="px-4">									
											<v-list-tile avatar>
												<v-list-tile-content>
													<v-list-tile-title><h4>Mặt hàng</h4></v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-avatar>
													<h4>SL</h4>
												</v-list-tile-avatar>										
												<v-list-tile-action class="mr-2">
													<h4>Số tiền</h4>
												</v-list-tile-action>
											</v-list-tile>
										</v-list>
									</v-toolbar>

									<v-divider></v-divider>

									<v-card-text class="scroll-y" style="max-height:150px">
										<v-list>
											<v-list-tile avatar v-for="(item, i) in cart.items" :key="i">
												<v-list-tile-content>
													<v-list-tile-title>{{item.name}}</v-list-tile-title>
													<v-list-tile-sub-title v-if="item.toppings.length > 0"><span v-for="(topping, i) in item.toppings" :key="i">{{topping.name}} ({{topping.price | formatPrice}}). </span></v-list-tile-sub-title>
													<v-list-tile-sub-title v-if="item.memo != null">{{item.memo}}</v-list-tile-sub-title>
												</v-list-tile-content>
												<v-list-tile-avatar>
													<v-avatar class="red" size="18">
														<span class="white--text">{{item.qty}}</span>
													</v-avatar>
												</v-list-tile-avatar>										
												<v-list-tile-action>
													<strong>{{item.subTotal | formatPrice}}</strong>
												</v-list-tile-action>
											</v-list-tile>
										</v-list>
									</v-card-text>
									<v-divider></v-divider>
								</v-card>
							</v-flex>

							<v-flex d-flex>
								<v-card height="374">
									<v-card-text>
										<v-list>
											<v-list-tile>
												<v-list-tile-content>
													<span>Tổng <strong class="red--text">{{counts}}</strong> món:</span>
												</v-list-tile-content>
												<v-list-tile-content>
													<v-list-tile-title></v-list-tile-title>
													<v-list-tile-title></v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-content>
													<v-list-tile-title class="text-xs-right"><h4 :style="coupon !=null ? `text-decoration : line-through` : '' ">{{subTotal | formatPrice}}</h4></v-list-tile-title>
													<v-list-tile-title class="text-xs-right" v-if="coupon != null"><h4 class="red--text">{{dealPrice | formatPrice}}</h4></v-list-tile-title>
												</v-list-tile-content>
											</v-list-tile>

											<v-list-tile>
												<v-list-tile-action>
													<span>Khuyến mãi:</span>
												</v-list-tile-action>
												<v-list-tile-content>
													<v-chip color="yellow" small text-color="red" v-if="coupon != null">
														<h4>{{coupon.coupon}}</h4>
													</v-chip>
												</v-list-tile-content>
												<v-list-tile-action>										
													<h4 class="red--text">-{{discount}}%</h4>
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
										</v-list>
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
												<v-list-tile-action><h4>Tiền mặt</h4></v-list-tile-action>
											</v-list-tile>
										</v-list>
									</v-card-text>
								</v-card>
							</v-flex>
						</v-flex>
					</v-layout>	
				</v-container>
			</v-card>
			<v-dialog v-model="dialog" max-width="400">
				<v-card>
					<v-toolbar dense flat class="elevation-0">
						<v-avatar size="24px">
							<img src="~/static/dofuu24x24.png">
						</v-avatar>
						<v-toolbar-title>
							Thông báo
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
		</v-dialog>
	</div>
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
				distance: null,
				duration: null
			},
			deliveryPrice: 0,
			datestring: formatDate(new Date().toISOString().substr(0, 10)),			
			processCheckout: false,
			loadingLocation:false,
			disabled: true,
			menuTime: false,
			dialog:false,
			maxRange: 20,
			calculate: true
		}
	},
	methods: {
		autoComplete() {
			var vm           = this
			var flag 		 = false
			var input        = document.getElementById('auto-complete')
			var autocomplete = new google.maps.places.Autocomplete(input)
			autocomplete.addListener('place_changed', function() {
				var place = autocomplete.getPlace()
				if(!place.geometry) {
					vm.geocoder('address', input.value).then(response => {
						vm.editedItem.address = response[0].formatted_address.slice(0, -10)
						vm.calculateRoute({lat: response[0].geometry.location.lat(), lng: response[0].geometry.location.lng()})
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
			this.calculate = true
			var vm         = this
			var map        = await new google.maps.Map(document.getElementById('map'), {
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

									arrayDistance[0]  = await numeral(distance.split(' ')[0]).divide(1000).value()
									arrayDistance[1]  = 'km'
									distance          = await arrayDistance.join(' ').replace('.', ',')
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
			})
			this.calculate = false
		},
		geocoder: function(type, location) {
			return new Promise((resolve, reject) => {
				var geocoder = new google.maps.Geocoder()
				if(type === 'latlng') {
					geocoder.geocode({'location': {lat: location.lat, lng: location.lng}}, function(results, status) {
						if(status === 'OK') {
							resolve(results)
						}
					})
				} else {
					geocoder.geocode({address: location}, function(results, status) {
						if(status == 'OK') {
							resolve(results)
						}
					})
				}
			})
		},
		currentLocation: async function() {
			var vm             = this
			vm.loadingLocation = true
			if(navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(async function(position){
					await vm.geocoder('latlng', {lat: position.coords.latitude, lng: position.coords.longitude}).then(response => {
						vm.editedItem.address = response[0].formatted_address.slice(0, -10)
						vm.calculateRoute({lat: position.coords.latitude, lng: position.coords.longitude})
					})
					vm.loadingLocation = false
				})
			}
		},
		calculateDeliveryPrice(distance) {
			var vm           = this
			const service    = vm.currentCity.service
			const deliveries = vm.currentCity.deliveries
			var shipPrice    = 0
			var oddPrice     = 0
			vm.maxRange      = service.maxRange
			// CHECK CONDITION MAX RANGE
			if(distance > service.maxRange) {
				deliveries.forEach(item => {
					shipPrice = item.price*distance
				})
				vm.dialog     = true
			} else {
				if(service.deliveryActived) {
					deliveries.forEach(item => {
						if(item.from <= distance && item.to >= distance && service.minRange >= distance) {
							if(vm.user.freeShip) {
								shipPrice = 0	
							} else {								
								shipPrice = parseFloat(item.price)
							}
						} else if(item.from <= distance && item.to >= distance && service.maxRange >= distance && service.minRange < distance) {
							shipPrice = new String(parseFloat(item.price)*distance)
							oddPrice  = numeral(shipPrice.slice(-3)).value()
							if(oddPrice >= 500) {
								shipPrice = numeral(shipPrice).value() + (1000 - oddPrice) 
							} else {
								shipPrice = numeral(shipPrice).value() - oddPrice
							}
						}
					})
				} 
			}
			vm.deliveryPrice = shipPrice
		},
		allowedHours: function(v) {
			return v
		},
		allowedMinutes: function(v) {			
			return v == 0 || v == 15 || v == 30 || v == 45
		},
		save: async function() {

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
				'city': this.currentCity,
				'estimateTime': this.intendTime,
				'distance': numeral(this.matrix.distance.split(' ')[0]).value(),
				'deliveryPrice': this.deliveryPrice,
				'paymentMethod': 1,
				'coupon': this.coupon
			}
			this.processCheckout = await true

			axios.post('/api/Dofuu/CheckOut', data, {headers: getHeader(), withCredentials:true}).then(async (response) => {
				if(response.status == 201) {
					
				}
			})
			this.$store.dispatch('getUser')
			this.matrix = {
				distance: null,
				duration: null
			},
			await window.localStorage.setItem('cart', JSON.stringify(cart))
			await this.$store.commit('FETCH_CART', cart)
			await this.$store.commit('REMOVE_COUPON')
			await this.$store.commit('CLOSE_CHECKOUT')
			this.$store.commit('CHECKOUT_SUCCESS')
			this.processCheckout = false
		}
	},
	computed: {
		...mapState({
			user: state        => state.authStore.currentUser,
			currentCity: state => state.cityStore.currentCity,
			show : state       => state.cartStore.dialog,
			cart: state        => state.cartStore.cart,
			coupon: state  	   => state.cartStore.coupon,
			myLocation: state  => state.myLocation
		}),
		disabledCheckout: function() {
			if(this.editedItem.name.length > 0 && this.editedItem.phone.length > 0 && this.editedItem.address != null && !this.calculate && this.matrix.distance != null && this.matrix.duration != null) {
				return false
			} else {
				return true
			}
		},
		counts: function() {
			return this.$store.getters.counts
		},
		discount: function() {
			return this.$store.getters.discount
		},
		subTotal: function() {
			return this.$store.getters.subTotal
		},
		total: function() {
			return Math.floor(numeral(this.subTotal).value() - numeral(this.subTotal).value()*this.discount/100 + numeral(this.deliveryPrice).value())
		},
		dealPrice: function() {
			if(this.coupon != null) {
				return this.subTotal - Math.floor(numeral(this.subTotal).value()*numeral(this.coupon.discountPercent).value()/100)
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
			if(this.matrix.duration != null) {
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

		}
	}, 
	filters: {
		formatPrice(price) {
			return numeral(price).format('0,0$')
		},
	},
	watch: {
		'show': function(val) {
			var vm = this

			if(val) {

				if(vm.user.address != null) {

					setTimeout(() => {
						vm.geocoder('address', vm.user.address).then(response => {
							vm.editedItem.address = response[0].formatted_address.slice(0, -10)
							vm.calculateRoute({lat: response[0].geometry.location.lat(), lng: response[0].geometry.location.lng()})
						})
					}, 300)

				} else {
					if(this.myLocation.address != null) {
						vm.geocoder('latlng', this.myLocation).then(response => {
							vm.editedItem.address = response[0].formatted_address.slice(0, -10)
							vm.calculateRoute({lat: response[0].geometry.location.lat(), lng: response[0].geometry.location.lng()})
						})
					}
					setTimeout(() => {
						var map 	= new google.maps.Map(document.getElementById('map'), {
							zoom: 17,
							center: {lat:vm.store.lat, lng:vm.store.lng}
						});

						var marker 	= new google.maps.Marker({
							position: {lat:vm.store.lat, lng:vm.store.lng},
							map: map
						});
					}, 100)

				}
			}
		},
		'matrix.distance': function(val) {
			if(val != null) {
				var distance = numeral(val.split(' ')[0]).value()
				if(val) {
					this.calculateDeliveryPrice(distance)
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