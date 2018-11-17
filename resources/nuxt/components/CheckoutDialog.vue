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
					<v-layout row wrap grey lighten-4>
						<v-flex d-flex xs12 md6>
							<v-layout column>
								<v-flex xs12 md6 order-xs1>
									<v-card color="grey" height="250" >
										<div id="map"></div>
									</v-card>
								</v-flex>
								<v-flex xs12 md6 order-xs2>
									<v-card>
										<v-card-text>
											<v-layout row wrap>
												<v-flex xs12 md6>
													<v-text-field prepend-icon="person" label="Họ tên" v-model="editedItem.name"></v-text-field>
												</v-flex>

												<v-flex xs12 md6>
													<v-text-field mask="(####) ### - ####" prepend-icon="phone" label="Số điện thoại" v-model="editedItem.phone"></v-text-field>
												</v-flex>

												<v-flex xs12 md12>
													<v-layout row wrap>
														<v-flex :bind="$vuetify.breakpoint.mdAndUp ? 'xs8' : 'xs10'">
															<v-text-field prepend-icon="place" placeholder="Địa chỉ nhận" v-model="editedItem.address" id="auto-complete" ref="autocomplete" 
															:hint="`Chọn nút phía bên phải trường địa chỉ nhận sẽ xác định vị trí hiện tại`"
															@focus="autoComplete"
															@keydown.enter="searchPlace"
															persistent-hint>
															<template slot="append-outer">
																<v-icon @click.prevent="currentLocation">gps_fixed</v-icon>
															</template>
														</v-text-field>
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
												hint="Vui lòng dặn dò hoặc mô tả địa điểm nhận đơn hàng nếu có"
												rows="3"
												persistent-hint
												></v-textarea>
											</v-flex>
										</v-layout>	
									</v-card-text>
								</v-card>
							</v-flex>

						</v-layout>							
					</v-flex>
					<v-flex d-flex xs12 md6>		
						<v-layout column>
							<v-flex xs12 md6 d-flex style="max-height:258px">
								<v-card color="white" height="250">
									<v-toolbar dense flat color="white" class="elevation-0" card>
										<v-list class="px-4" dense>									
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

									<v-card-text class="scroll-y" style="max-height:202px">
										<v-list dense>
											<v-list-tile avatar v-for="(item, i) in cart.items" :key="i">
												<v-list-tile-content>
													<v-list-tile-title>{{item.name}}</v-list-tile-title>
													<v-list-tile-sub-title v-if="item.toppings.length > 0">Thêm: <span v-for="(topping, i) in item.toppings" :key="i">{{topping.name}} ({{topping.price | formatPrice}}). </span></v-list-tile-sub-title>
													<v-list-tile-sub-title v-if="item.memo != null" class="font-weight-bold">Ghi chú: {{item.memo}}</v-list-tile-sub-title>
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
								</v-card>
							</v-flex>
							<v-flex xs12 md6 d-flex>
								<v-card height="365">
									<v-card-text>
										<v-list dense>
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
													<v-list-tile-title class="text-xs-right" v-if="coupon != null"><h4 class="red--text">{{subTotal + dealPrice | formatPrice}}</h4></v-list-tile-title>
												</v-list-tile-content>
											</v-list-tile>

											<v-list-tile v-if="coupon != null">
												<v-list-tile-action>
													<span>Khuyến mãi:</span>
												</v-list-tile-action>
												<v-list-tile-content>
													<v-chip color="yellow" small text-color="red" v-if="coupon != null">
														<h4>{{coupon.code}}</h4>
													</v-chip>
												</v-list-tile-content>
												<v-list-tile-action>										
													<h4 class="red--text font-italic">Đã giảm 
														<span v-if="coupon.discountPercent>0">{{coupon.discountPercent}}%</span>
														<span v-if="coupon.discountPrice>0">{{coupon.discountPrice | formatPrice}}</span></h4>
													</v-list-tile-action>
												</v-list-tile>

												<v-list-tile>
													<v-list-tile-content>
														<span>Phí vận chuyển: <strong class="red--text">{{matrix.distance}}</strong> <v-icon>help</v-icon></span>
													</v-list-tile-content>
													<v-list-tile-content class="align-end font-weight-bold">
														{{deliveryPrice | formatPrice}}
													</v-list-tile-content>
												</v-list-tile>

												<v-list-tile v-if="reduceShippingCost > 0">
													<v-list-tile-content>
														<span>Hỗ trợ phí: </span>
													</v-list-tile-content>
													<v-list-tile-content class="align-end font-weight-bold font-italic red--text">
														-{{reduceShippingCost | formatPrice}}
													</v-list-tile-content>

												</v-list-tile>							
											</v-list>
										</v-card-text>

										<v-divider></v-divider>

										<v-card-text>
											<v-list dense>
												<v-list-tile>
													<v-list-tile-content>
														Thành tiền:
													</v-list-tile-content>
													<v-list-tile-content></v-list-tile-content>
													<v-list-tile-action><strong>{{total | formatPrice}}</strong></v-list-tile-action>
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
							</v-layout>								
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
							Phạm vi giao hàng của dofuu tối đa <strong class="red--text">{{maxRange}} km</strong>
						</div>
						<div><strong>Mong quý khách thông cảm. Cám ơn !</strong></div>
					</v-card-text>
					<v-card-actions>
						<v-btn color="green darken-1"  dark @click.native="dialog = false" block>Đồng ý</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>
		</v-dialog>
		<vue-alert ref="alert"/> 
	</div>
</template>
<script>
	import { mapState } from 'vuex'
	import numeral from 'numeral'
	import moment from 'moment-timezone'
	import AutoComplete from '@/components/AutoComplete'
	import AlertDialog from '@/components/commons/alertDialog'
	import axios from 'axios'
	import index from '@/mixins/index'
	import { geocoder, initMap, calculateDirection, makeMarker} from '@/utils/google-map'
	import { getHeader } from '@/config'
	function formatDate(str) {
		if(str != null) {
			return str.substring(8, 10)+'/'+str.substring(5, 7)+'/'+str.substring(0, 4)
		}
		return null
	}
	export default {
		props: ['store'],
		mixins: [index],
		components: {
			'vue-autocomplete' : AutoComplete,
			'vue-alert': AlertDialog,
		},
		data() {
			return {
				editedItem: {
					name: '',
					phone: '',
					place: null,
					address: null,
					lat: 0,
					lng: 0,
					date: new Date().toISOString().substr(0, 10),
					time: null,
					memo: 'Càng sớm càng tốt',
					googleAddress: ''
				},
				modal: false,
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
						geocoder('address', vm.editedItem.address).then(result => {
							vm.setPlace(result[0])
						})
					} else {
						vm.setPlace(place)
					}					
				})
			},
			searchPlace() {
				var vm = this
				console.log(vm.editedItem.address)
				geocoder('address', vm.editedItem.address).then(result => {
					vm.setPlace(result[0])
				})
			},
			setPlace(result) {
				var place = result
				if(place) {
					this.editedItem.address = place.formatted_address
					this.editedItem.lat     = place.geometry.location.lat()
					this.editedItem.lng     = place.geometry.location.lng()
					
					if(!place.types.some(type => type === "street_address" || type === "route")) {
						this.editedItem.memo = place.name
					} else {
						this.editedItem.memo = "Càng nhanh càng tốt."
					}					
					this.calculateRoute({lat: place.geometry.location.lat(), lng: place.geometry.location.lng()})
				}
			},
			calculateRoute:async function(destination) {
				var vm        = this
				var start     = vm.store
				var end       = destination
				var distance  = '0 Km'
				var duration  = '0 phút'
				var map       = initMap(vm.store)
				var endIcon   = { url: '/map_icons/home.png',  scaledSize: new google.maps.Size(32, 32)}
				var startIcon = vm.typeIcon(vm.store.type.name, 'red')
				vm.calculate  = true
				calculateDirection(map, start, end).then(response => {
					var leg            = response.routes[0].legs[0];
					distance           = leg.distance.text
					duration           = leg.duration.text
					makeMarker(leg.start_location, startIcon, map);
					makeMarker(leg.end_location, endIcon, map);
					vm.matrix.distance =  distance
					vm.matrix.duration =  duration
					vm.calculate       = false
				})
			},
			geocoder: function(type, location) {
				var vm = this
				geocoder(type, location).then(response => {
					var result            = response[0]
					var position          = response[0].geometry.location
					vm.editedItem.address = result.formatted_address.slice(0, -10)
					vm.editedItem.lat     = position.lat()
					vm.editedItem.lng     = position.lng()
					vm.calculateRoute({lat: position.lat(), lng: position.lng()})
				})
			},
			currentLocation: function() {
				var vm             = this
				vm.loadingLocation = true
				if(vm.tracking) {
					vm.geocoder('latlng', vm.myLocation)
				} else {
					if(!!vm.message) {						
						vm.$refs.alert.open('Thông báo', vm.message)
					} else {
						vm.$refs.alert.open('Thông báo', 'Chưa cấp phép truy cập vị trí hiện tại của bạn.')
					}
				}
			},
			calculateDeliveryPrice(distance) {
				var vm           = this
				const service    = vm.currentCity.service
				const deliveries = vm.currentCity.deliveries
				var shipPrice    = 0
				var unit         = 1000
				vm.maxRange      = service.maxRange
				// CHECK CONDITION MAX RANGE
				if(distance > service.maxRange) {
					deliveries.forEach(item => {
						shipPrice = item.price*distance
					})
					vm.calculate  = true
					vm.dialog     = true
				} else {
					shipPrice = this.calculateShipPrice(service, deliveries, distance)
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
				var vm   = this				
				var data = {
					'userId'            : this.user.id,
					'confirmed'         : true,
					'name'              : this.editedItem.name, 
					'phone'             : this.editedItem.phone, 
					'address'           : this.editedItem.address,
					'lat'               : this.editedItem.lat,
					'lng'               : this.editedItem.lng,
					'date'              : this.editedItem.date, 
					'time'              : this.editedItem.time, 
					'memo'              : this.editedItem.memo, 
					'items'             : this.cart.items,
					'subTotal'          : this.subTotal,
					'total'             : this.total,
					'store'             : this.store,
					'city'              : this.currentCity,
					'estimateTime'      : this.intendTime,
					'distance'          : numeral(this.matrix.distance.split(' ')[0]).value(),
					'deliveryPrice'     : this.deliveryPrice,
					'reduceShippingCost': this.reduceShippingCost,
					'paymentMethod'     : 1,
					'coupon'            : this.coupon
				}
				if(!vm.processCheckout) {
					vm.processCheckout = true 
					setTimeout(() => {
						axios.post('/api/Dofuu/CheckOut', data, {headers: getHeader(), withCredentials:true}).then( (response) => {
							if(response.status == 201) {

							}
						}).finally(() => {
							vm.processCheckout = false 
						})
					}, 200)
				}
				
				this.$store.dispatch('getUser')
				this.matrix = {
					distance: null,
					duration: null
				} 
				var cart = { instance: this.store.id, items: []}
				await window.localStorage.setItem('cart', JSON.stringify(cart))
				await this.$store.commit('FETCH_CART', cart)
				await this.$store.commit('REMOVE_COUPON')
				await this.$store.commit('CLOSE_CHECKOUT')
				this.$store.commit('CHECKOUT_SUCCESS')
			},
			calculateDeal: function(price, coupon) {
				const maxPrice = coupon.maxPrice			
				if(price > maxPrice && maxPrice != 0) {
					return - numeral(maxPrice).value()
				} else {
					return - price
				}
			},
			calculateShipPrice: function(service, deliveries, distance) {
				var shipPrice = 0
				const unit    = 1000
				var user      = this.user
				deliveries.forEach(item => {
					if(item.from <= distance && item.to >= distance && service.minRange >= distance) {
						if(user.freeShip) {
							shipPrice = 0
						} else {
							shipPrice = parseFloat(item.price)
						}	
						
					} else if(item.from <= distance && item.to >= distance && service.maxRange >= distance && service.minRange < distance) {
						shipPrice = Math.round(parseFloat(item.price)*distance/unit)*unit
					}
				})
				return shipPrice
			},
			decreaseShippingCost: function() {
				
			}
		},
		computed: {
			...mapState({
				user: state        => state.authStore.currentUser,
				currentCity: state => state.cityStore.currentCity,
				show : state       => state.cartStore.dialog,
				cart: state        => state.cartStore.cart,
				coupon: state  	   => state.cartStore.coupon,
				myLocation: state  => state.myLocation,
				tracking: state    => state.locationStore.tracking
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
				const unit = 1000
				return Math.round((numeral(this.subTotal).value() + this.dealPrice + numeral(this.deliveryPrice).value() - numeral(this.reduceShippingCost).value())/unit)*unit
			},
			dealPrice: function() {
				if(!!this.coupon) {
					const price = Math.floor(numeral(this.subTotal).value()*numeral(this.coupon.discountPercent).value()/100 + numeral(this.coupon.discountPrice).value())
					return this.calculateDeal(price, this.coupon)
				}
				return 0
			},
			reduceShippingCost: function() {
				var coupon      = this.coupon
				var shipCost    = 0 
				var regex       = new RegExp(':', 'g') // Regular 
				var now         = moment().tz('Asia/Ho_Chi_Minh')
				var timeNow     = now.format('HH:mm')
				var currentDay  = now.weekday() 
				const startTime = '08:00'
				const endTime   = '16:00'
				// if(!coupon && this.store.verified) {
				// 	if(currentDay>0 && currentDay < 6) {
				// 		if(parseInt(timeNow.replace(regex, ''), 10) >= parseInt(startTime.replace(regex, ''), 10) && parseInt(timeNow.replace(regex, ''), 10) <= parseInt(endTime.replace(regex, ''), 10)) {
				// 			return shipCost = 12000
				// 		} 	
				// 	}	
				// }
				return shipCost
			},
			minTime: function() {
				var activities = this.store.activities // Active time of store
				var day        = null // Day current of store
				var now        = moment().tz('Asia/Ho_Chi_Minh') // Date time now 
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
				var now        = moment().tz('Asia/Ho_Chi_Minh') // Date time now 
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
					var now         = moment().tz('Asia/Ho_Chi_Minh') // Date time current
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
							vm.geocoder('address', vm.user.address)
						}, 300)

					} else {
						if(this.myLocation.address != null) {
							vm.geocoder('latlng', this.myLocation)
						} else {
							var map = initMap(vm.store)
							var marker 	= new google.maps.Marker({
								position: {lat:vm.store.lat, lng:vm.store.lng},
								map: map
							});
						}						
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
			'user': function(val) {
				if(val) {
					this.editedItem = Object.assign(this.editedItem, {name:val.name, phone: val.phone, address:val.address, lat: val.lat, lng: val.lng})
				}
			},
			'editedItem.date': function(val) {
				this.datestring = formatDate(val)
			},
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