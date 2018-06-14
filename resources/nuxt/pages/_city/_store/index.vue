<template>
	<v-container fluid grid-list-lg>
		<v-layout child-flex wrap v-show="!loading">
			<v-flex xs12 md8>
				<!-- CARD DEAL START -->
				<v-card color="white" v-if="store.coupon" flat>
					<v-tooltip v-model="showTooltip" top>
						<div slot="activator"></div>
						<span>{{messageTooltip}}</span>
					</v-tooltip>					
					<v-alert outline color="warning" icon="card_giftcard" :value="true" class="py-0">
						<v-card color="transparent" flat>							
							<v-card-title class="pb-0 pl-1">
								<div>
									<div class="title">{{store.coupon.title}}</div>
									<div class="subheading">Nhập mã: <span class="red--text"><strong><u>{{store.coupon.code}}</u></strong></span> để được khuyến mãi {{store.coupon.discount}}%</div>
									<input type="hidden" id="coupon-code" :value="store.coupon.code">
								</div>
							</v-card-title>							
							<v-card-actions  class="pa-0 pb-2">
								<div>Khuyến mãi kết thúc sau <v-icon>alarm</v-icon> 
									<span class="subheading"><strong>{{time.dd}}</strong> Ngày <strong>{{time.hh}} : {{time.mm}} : {{time.ss}} </strong>
									{{start(store.coupon.endedAt)}}</span>
								</div>
								<v-spacer></v-spacer>
								<v-btn small flat @click="copyCode()">
									<v-icon>content_copy</v-icon>
									<span>Sao chép mã</span>
								</v-btn> 								
							</v-card-actions>							
						</v-card>						
					</v-alert>
				</v-card><!-- CARD DEAL END -->

				<v-content v-if="menu" v-for="(data, index) in menu" :key="index">
					<v-subheader v-if="data.products.length>0"  :id="'item_'+data.id"><span >{{data.name | upperCase }} <span v-if="data._name != null">({{data._name | upperCase}}) </span></span></v-subheader>
					<v-card hover class="mb-4" v-for="(item, i) in data.products" :key="i">	
						<v-layout row wrap>
							<v-flex xs12 md5 d-flex>
								<v-card-media :src="image(item.image)" height="200px">
								</v-card-media>
							</v-flex>
							<v-flex xs12 md7 d-flex>
								<v-card-text>
									<h3 class="title">{{item.name}}</h3>
									<span class="grey--text" v-if="item._name != null">{{item._name}}</span><br>
									<span v-if="item.description">Mô tả: {{item.description}}</span><br>
									<span class="grey--text">Đã được đặt <strong class="black--text">{{item.count}}</strong> lần</span><br>
								</v-card-text>										
							</v-flex>
							<v-flex xs12 md5 d-flex>
								<v-card-actions>
									<v-btn block color="red accent-2" dark @click.native="addToCart(item)">
										<span>{{item.price | formatPrice}}</span>
										<v-icon right>add_shopping_cart</v-icon>
									</v-btn>
								</v-card-actions>
							</v-flex>
						</v-layout>
					</v-card>
				</v-content>
			</v-flex>
			<!-- RIGHT NAVBAR DESKTOP -->
			<v-flex xs12 md4  ref="target_navbar_right" class="hidden-sm-and-down">
				<v-card :class="{'card--sticky' : offsetTop>offsetNavbarRight-50 && !$vuetify.breakpoint.xsOnly}" style="z-index:4">
					<v-tabs icons-and-text grow :value="`item-${tabIndex}`" color="">

						<v-tabs-slider color="yellow"></v-tabs-slider>

						<v-tab v-for="(tab, index) in tabs" :key="index" @click="changeTab(index)":href="`#item-${index}`" >
							{{tab.title}}
							<v-badge color="red" v-if="index == 1" overlap>
								<span slot="badge" v-if="counts>0">{{counts}}</span>
								<v-icon left>shopping_cart</v-icon>						 
							</v-badge>

							<v-icon left v-if="index == 0">assignment</v-icon>

						</v-tab>
					</v-tabs>
					<v-card-text v-if="tabIndex==0">
						<v-list>
							<v-list-tile v-for="item in store.catalogues" v-if="item.products.length>0" @click="goTo('#item_'+item.id)" :key="item.name">
								<v-list-tile-content>
									<v-list-tile-title>
										{{item.name}}
									</v-list-tile-title>
									<v-list-tile-sub-title>
										{{item._name}}
									</v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
					</v-card-text>
					<v-card v-else-if="tabIndex==1" class="transparent">
						<v-card-text>
							<v-data-table
							v-if="cart && cart.items.length>0" :headers="headers" :items="cart.items" class="elevation-1 scroll-y" hide-actions hide-headers style="max-height:280px; overflow-x:hidden"
							>
							<template slot="items" slot-scope="props">
								<tr>
									<td class="text-xs-center">
										<div>{{props.item.name}}</div>
										<div><v-btn  outline color="primary" icon @click="props.expanded = ! props.expanded" small><v-icon>edit</v-icon></v-btn></div>
									</td>
									<td class="text-xs-center">
										<span>
											<v-btn icon ripple @click.stop="addToCart(props.item)" class="ma-0">
												<v-icon color="green">add_box</v-icon>
											</v-btn>
										</span>
										<span class="py-0">												
											{{props.item.qty}}x
										</span>
										<span class="py-0">
											<v-btn icon ripple @click.stop="minusToCart(props.item)"  class="ma-0">
												<v-icon color="grey" >indeterminate_check_box</v-icon>
											</v-btn>
										</span>											
									</td>
									<td>{{props.item.price | formatPrice}}</td>	
								</tr>
							</template>
							<template slot="expand" slot-scope="props">
								<v-container fluid grid-list-md class="grey lighten-3">			
									<v-flex d-flex xs12>
										<v-text-field
										:label="`Ghi chú ${props.item.name}`"
										></v-text-field>
									</v-flex>
								</v-container>
							</template>
						</v-data-table>
					</v-card-text>

					<v-divider></v-divider>

					<v-list-tile class="yellow accent-3">

						<v-list-tile-content >			

							<v-text-field v-if="coupon == null" prepend-icon="redeem" single-line solo color="red accent-3" v-model="code" label="Nhập mã khuyến mãi" :append-icon="'send'" :append-icon-cb="checkCoupon"></v-text-field>		

							<v-chip close v-else :value="coupon != null" color="green darken-3" text-color="white" @input="removeCoupon">
								<v-icon left>redeem</v-icon>
								{{coupon.coupon}}											
							</v-chip>				

						</v-list-tile-content>

						<v-list-tile-action v-if="coupon != null">
							<strong class="red--text text--accent-3">Giảm {{coupon.discountPercent}}%</strong>
						</v-list-tile-action>

					</v-list-tile>

					<span v-if="alert.show" class="red--text text-lg-right">{{alert.message}}</span>
					
					<v-divider></v-divider>

					<v-layout row wrap>
						<v-flex	xs12>
							<v-list dense two-line> 
								
								<v-list-tile>
									
									<v-list-tile-content>Tạm tính:</v-list-tile-content>
									
									<v-list-tile-content class="align-end">
										<v-list-tile-title class="text-xs-right"><h3  :style="coupon !=null ? `text-decoration : line-through` : '' ">{{subTotal | formatPrice}}</h3></v-list-tile-title>
										<v-list-tile-title v-if="coupon != null" class="text-xs-right red--text text--accent-3"><h3>{{total | formatPrice}}</h3></v-list-tile-title>
									</v-list-tile-content>

								</v-list-tile>

								<v-divider></v-divider>
								<v-list-tile>
									<v-list-tile-content>											
										<v-menu	:close-on-content-click="false"	v-model="showInfoDelivery" v-if="currentCity != null" top left offset-y>
											<span slot="activator">Phí vận chuyển: <v-icon size="20" color="primary" v-on:mouseover="showDelivery" v-on:mouseleave="closeDelivery">help</v-icon></span>
											<v-card v-show="$vuetify.breakpoint.mdAndUp">
												<v-toolbar dense flat class="elevation-0">
													<v-avatar size="24px" tile>
														<img src="~/static/dofuu24x24.png">
													</v-avatar>
													<v-toolbar-title>
														Thông báo
													</v-toolbar-title>
												</v-toolbar>
												<v-card-text>
													<v-list> 
														<v-list-tile>
															<v-list-tile-content><span>Phí vận chuyển tối thiểu của đơn đặt hàng: <strong class="red--text">{{currentCity.deliveries[0].price | formatPrice}}</strong> </span></v-list-tile-content>
														</v-list-tile>
														<v-list-tile>
															<v-list-tile-content><span>Phí vận chuyển sẽ tính theo khoảng cách: <strong class="red--text">{{currentCity.deliveries[1].price | formatPrice}}/km</strong> </span></v-list-tile-content>
														</v-list-tile>
														<v-list-tile>
															<v-list-tile-content><strong>Phí vận chuyển có thể thay đổi tùy theo thời điểm</strong></v-list-tile-content>
															<v-list-tile-content class="align-end"></v-list-tile-content>
														</v-list-tile>
													</v-list>
												</v-card-text>
											</v-card>
										</v-menu>
									</v-list-tile-content>
									<v-list-tile-content class="align-end" v-if="currentCity != null">
										<h3>{{currentCity.deliveries[1].price | formatPrice}}/km</h3>
									</v-list-tile-content>
								</v-list-tile>
							</v-list>
						</v-flex>
					</v-layout>
					<v-divider></v-divider>
					<v-card-actions>
						<v-btn block :disabled="!cart || cart.items.length==0" color="red accent-2 white--text" dense @click.native="checkOut">
							Gửi đơn hàng
						</v-btn>
					</v-card-actions>
				</v-card>
			</v-card>			
		</v-flex>
	</v-layout>
	<vue-dialog :store.sync="store" v-if="store != null"></vue-dialog>
	<!-- RIGHT NAVBAR MOBILE START -->
	<v-navigation-drawer fixed :clipped="$vuetify.breakpoint.mdAndUp" v-model="drawer" right class="hidden-lg-only hidden-md-only">
		<v-toolbar	color="red"	dark extended flat>
			<v-text-field  prepend-icon="search" label="Tìm kiếm món" solo-inverted class="mx-3" v-model="search" flat/>

			<v-tabs icons-and-text mobile-break-point="1264" slot="extension" grow :value="`item-${tabIndex}`"   color="transparent">

				<v-tabs-slider color="yellow"></v-tabs-slider>

				<v-tab v-for="(tab, index) in tabs" :key="index" @click="changeTab(index)":href="`#item-${index}`" >

					{{tab.title}}

					<v-badge color="blue" v-if="index == 1" overlap>
						<span slot="badge" v-if="counts>0">{{counts}}</span>
						<v-icon left>shopping_cart</v-icon>						 
					</v-badge>

					<v-icon left v-if="index == 0">assignment</v-icon>

				</v-tab>
			</v-tabs>
		</v-toolbar>

		<v-list v-if="tabIndex==0" dense>
			<template>
				<v-list-tile v-for="item in store.catalogues" v-if="item.products.length>0" @click="goTo('#item_'+item.id)" :key="item.name">
					<v-list-tile-content>
						<v-list-tile-title>
							{{item.name}}
						</v-list-tile-title>
						<v-list-tile-sub-title>
							{{item._name}}
						</v-list-tile-sub-title>
					</v-list-tile-content>
				</v-list-tile>
			</template>
		</v-list>

		<v-card v-else-if="tabIndex==1" class="transparent" scrollable >
			<v-data-table v-if="cart && cart.items.length>0" :items="cart.items" class="elevation-1 mt-2 scroll-y" hide-actions hide-headers style="max-height:280px; overflow-x:hidden" >
				<template slot="items" slot-scope="props" >
					<td class="text-xs-left">{{props.item.name}}</td>
					<td>
						<v-layout column justify-center>
							<v-flex md2 d-flex>
								<v-btn icon ripple @click.stop="addToCart(props.item)">
									<v-icon color="green">add_box</v-icon>
								</v-btn>
							</v-flex>
							<v-flex md2 d-flex class="justify-center">
								{{props.item.qty}}x
							</v-flex>
							<v-flex md2 d-flex>
								<v-btn icon ripple @click.stop="minusToCart(props.item)">
									<v-icon color="grey" >indeterminate_check_box</v-icon>
								</v-btn>
							</v-flex>
						</v-layout>
					</td>
					<td>{{props.item.price | formatPrice}}</td>
				</template>
			</v-data-table>
			<v-divider></v-divider>
			<v-layout row wrap>
				<v-flex	xs12 sm12 md12 lg12>
					<v-list dense> 
						<v-list-tile>
							<v-list-tile-content>Tổng:</v-list-tile-content>
							<v-list-tile-content class="align-end font-weight-bold">{{subTotal | formatPrice}}</v-list-tile-content>
						</v-list-tile>
						<v-list-tile>
							<v-list-tile-content>Tạm tính:</v-list-tile-content>
							<v-list-tile-content class="align-end font-weight-bold">{{subTotal | formatPrice}}</v-list-tile-content>
						</v-list-tile>
						<v-list-tile>
							<v-list-tile-content>
								<span>Phí vận chuyển: <v-icon size="20" color="primary" @click="showInfoDelivery = true">help</v-icon></span>
							</v-list-tile-content>
							<v-list-tile-content class="align-end" v-if="currentCity != null">
								{{currentCity.deliveries[1].price | formatPrice}}/km
							</v-list-tile-content>
						</v-list-tile>
					</v-list>
					<v-dialog v-model="showInfoDelivery" max-width="500px" v-if="currentCity != null && $vuetify.breakpoint.smAndDown">
						<v-card>
							<v-toolbar dense flat class="elevation-0">
								<v-avatar size="24px" tile>
									<img src="~/static/dofuu24x24.png">
								</v-avatar>
								<v-toolbar-title>
									Thông báo
								</v-toolbar-title>
							</v-toolbar>
							<v-card-text>
								<v-list> 
									<v-list-tile>
										<v-list-tile-content><span>Phí vận chuyển tối thiểu của đơn đặt hàng: <strong class="red--text">{{currentCity.deliveries[0].price | formatPrice}}</strong> </span></v-list-tile-content>
									</v-list-tile>
									<v-list-tile>
										<v-list-tile-content><span>Phí vận chuyển sẽ được tính theo khoảng cách: <strong class="red--text">{{currentCity.deliveries[1].price | formatPrice}}/km</strong> </span></v-list-tile-content>
									</v-list-tile>
									<v-list-tile>
										<v-list-tile-content><strong>Phí vận chuyển có thể thay đổi tùy theo thời điểm</strong></v-list-tile-content>
										<v-list-tile-content class="align-end"></v-list-tile-content>
									</v-list-tile>
								</v-list>
							</v-card-text>
						</v-card>
					</v-dialog>
				</v-flex>
			</v-layout>
			<v-divider></v-divider>
			<v-card-actions>
				<v-btn block :disabled="!cart || cart.items.length==0" color="red accent-2 white--text" dense @click.native="checkOut">
					Gửi đơn hàng
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-navigation-drawer>

	<v-btn fixed bottom right icon color="red accent-3" dark @click.stop="drawer =! drawer" class="hidden-lg-only hidden-md-only">
		<v-badge color="blue">
			<span slot="badge" v-if="counts>0">{{counts}}</span>
			<v-icon>menu</v-icon>
		</v-badge>
	</v-btn>
	<!-- RIGHT NAVBAR MOBILE END -->
	<v-dialog v-model="dialog" max-width="400">
		<v-card>
			<v-toolbar dense color="transparent" class="elevation-0">
				<v-avatar size="24px" tile>
					<img src="~/static/dofuu24x24.png">
				</v-avatar>
				<v-toolbar-title>
					Thông báo
				</v-toolbar-title>
			</v-toolbar>
			<v-divider></v-divider>
			<v-card-text>
				<div><strong>Dofuu xin lỗi quý khách hàng !</strong></div>
				<div>{{message}}</div>
				<div>Quán phục vụ vào lúc 
					<span v-for="(item, i) in store.activities" v-if="i==0"> 
						<span v-for="(time, index) in item.times" class="green--text text--darken-1">
							<strong>
								{{time.from}} - {{time.to}} 
							</strong>
						</span>	
						<span :class="{'red--text accent-4--text': status(store.status) == 2, 'green--text accent-4--text': status(store.status) == 1, 'yellow--text accent-4--text': status(store.status) == 3}"><strong><i>({{store.status}})</i></strong></span>
					</span>
				</div>
				<div><strong>Mong quý khách thông cảm. Cám ơn !</strong></div>
			</v-card-text>
			<v-card-actions>
				<v-btn block color="green darken-1" dark @click.native="dialog = false">Đồng ý</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</v-container>
</template>

<script>
import numeral from 'numeral'
import Cookies from 'js-cookie'
import moment from 'moment'
import axios from 'axios'
import {getStoreURL, getHeader} from '@/config'
import index from '@/mixins/index'
import {mapState} from 'vuex'
const CheckoutDialog = () => ({
  // The component to load (should be a Promise)
  component: import('@/components/CheckoutDialog'),
  // Delay before showing the loading component. Default: 200ms.
  delay: 200,
  // The error component will be displayed if a timeout is
  // provided and exceeded. Default: Infinity.
  timeout: 3000
})
export default {
	mixins: [index],
	components: {
		'vue-dialog' :CheckoutDialog
	},
	asyncData() {
		return {
			duration: 300,
			offset: 0,
			easing: 'easeInOutCubic',
			offsetNavbarRight: 0,
			headers: [
			{
				text: 'Món',
				align: 'left',
				sortable: false
			},
			{ text: 'Số lượng'},
			{ text: 'Giá'}
			],
			bag: {
				intance: 20,
				items: []
			},
			time: {
				dd:0,
				hh:0,
				mm:0,
				ss:0
			},
			showTooltip: false,
			messageTooltip: '',
			storeInfo: null,
			search: '',
			drawer: true,
			dialog: false,
			message:'',
			products: [],
			showInfoDelivery: false,
			loadingCoupon: false,
			code: null,
			couponChip: true,
			alert: {
				show: false,
				message: '',
				type: 'error'
			},
		}
	},
	methods: {
		checkCoupon: async function() {
			var vm = this
			if(vm.code == null) {
				vm.alert = Object.assign({}, {show: true, message: 'Vui lòng nhập mã giảm giá', type: 'error'})
			} else {
				const data = Object.assign({}, {storeID: this.store.id, coupon: this.code, subTotal: this.subTotal, districtID: this.store.districtId, cityID: this.currentCity.id})
				vm.loadingCoupon = await !vm.loadingCoupon
				if(vm.loadingCoupon) {
					await setTimeout(function() {
						
						axios.post('/api/Dofuu/CheckCouponCode', data, {withCredentials:true}).then(response => {
							
							if(response.data.type === 'success') {
								vm.$store.commit('ADD_COUPON', response.data)
							}
							
							if(response.data.type === 'error') {
								vm.alert = Object.assign({}, {show: true, message: response.data.message, type: 'error'})	
							}

						})

					},300)
				}
				
				setTimeout(() => {
					vm.loadingCoupon = !vm.loadingCoupon
				}, 500)

				setTimeout(() => {
					vm.alert = Object.assign({}, {show: false, message: '', type: 'error'})
				},3000)			
			}
			
		},
		removeCoupon: function() {
			this.$store.commit('REMOVE_COUPON')
		},
		showDelivery: function() {
			this.showInfoDelivery = true
		},
		closeDelivery: function() {
			this.showInfoDelivery = false
		},
		//CHECK DATE
		checkDayOff: function() {
			return new Promise((resolve, reject) => {
				var n = moment().locale('vi').day()
				var day = this.store.activities.find(day => {
					if(day.number == n) {
						return day
					} else {
						return false
					}
				})
				if(day) {
					day.times.forEach(time => {
						if(moment(moment(), 'HH:mm:ss').format('HH:mm') >= moment(time.from, 'HH:mm:ss').format('HH:mm') && moment(moment(), 'HH:mm:ss').format('HH:mm') < moment(time.to, 'HH:mm:ss').format('HH:mm')) {
							resolve(true)
						} else {
							if(moment(moment(), 'HH:mm:ss').format('HH:mm') < moment(time.from, 'HH:mm:ss').format('HH:mm')) {
								this.dialog = true
								this.message     = 'Quán hiện tại chưa mở cửa'
							} else if(moment(moment(), 'HH:mm:ss').format('HH:mm') >= moment(time.to, 'HH:mm:ss').format('HH:mm')) {
								this.dialog = true
								this.message     = 'Quán hiện tại đã đóng cửa'
							}
						}
					})
				} else {
					this.dialog = true
					this.message     = 'Quán hôm nay nghỉ'
				}			
			})			
		},
		// SCROLLING TO CATALOGUE
		goTo: function (target) {
			this.drawer = false
			this.$vuetify.goTo(target, this.options)
		},
		//SET TIMEOUT AT END TIME FOR COUPON
		start: function (date) {
			setTimeout(() => {
				let timeNow   = new Date().getTime()
				
				let endedTime = new Date(date).getTime()				
				
				let distance  = Math.floor(endedTime - timeNow)/1000
				
				let day       = Math.floor(distance / (60 * 60 * 24));
				
				let hour      = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
				
				let minutes   = Math.floor((distance % (60 * 60)) / 60);
				
				var seconds   = Math.floor(distance % (60));
				
				this.time     = {dd: day, hh: hour, mm: minutes, ss: seconds}
			}, 1000)
		},
		// ADD ITEM TO CART
		addToCart: function (product) {
			this.checkDayOff().then(response => {
				if(response) {
					const productIndex  = this.cart.items.findIndex(item => {
						return item.id === product.id
					})
					if (productIndex > -1) {
						this.cart.items[productIndex].qty++
					} else {
						product.qty = 1
						this.cart.items.push(product)
					}
					this.$store.commit('FETCH_CART', this.cart)
					window.localStorage.setItem('cart', JSON.stringify(this.cart))
					this.$store.commit('CHANGE_TAB', 1)
				}
			})
		},
		// MINUS ITEM TO CART
		minusToCart: function (product) {
			const productIndex = this.cart.items.findIndex(item => {
				return item.id === product.id
			})
			if (productIndex > -1) {
				if (this.cart.items[productIndex].qty > 1){
					this.cart.items[productIndex].qty--
				} else if (this.cart.items[productIndex].qty == 1) {
					this.cart.items.splice(productIndex, 1)	
				}
				this.$store.commit('FETCH_CART', this.cart)
				window.localStorage.setItem('cart', JSON.stringify(this.cart))
			} 
		},
		//CHANGE TAB RIGHT NAVBAR
		changeTab (index) {
			if (this.tabIndex === index) {
				return false
			}
			this.$store.commit('CHANGE_TAB', index)
		},
		//COPY DEAL CODE
		copyCode() {
			var vm = this
			let code = document.querySelector('#coupon-code')
			code.setAttribute('type', 'text')

			code.select()

			try {
				var successful = document.execCommand('copy');
				this.messageTooltip = successful ? 'Sao chép mã khuyến mãi thành công' : 'Sao chép mã khuyến mãi thất bại'
			} catch (error) {
				this.messageTooltip = 'Không thể sao chép mã khuyến mãi'
			}
			code.setAttribute('type', 'hidden')
			window.getSelection().removeAllRanges()
			vm.showTooltip = !vm.showTooltip

			setTimeout(() => {
				vm.showTooltip = !vm.showTooltip
			}, 1000)
			
		},
		//CHECK OUT CART
		checkOut: async function() {
			var vm    = this
			vm.checkDayOff().then(response => {
				if(response) {
					const city = this.$store.getters.getCityBySlug(this.$route.params.city)
					vm.$store.dispatch('getUser').then(async (response) => {
						if(response.status == 200) {
							await vm.getProducts().then(async (response) => {
								if(response.status == 200) {
									var array = []
									await vm.products.find(product => {
										vm.cart.items.forEach(item => {
											if(product.id == item.id) {
												product.qty = item.qty
												array.push(product)
											}
										})
									})
									var cart = {
										instance: this.store.id,
										items: array	
									}
									this.$store.commit('FETCH_CART', cart)
								}
							})
							vm.$store.commit('SHOW_CHECKOUT')
						}
					}).catch(error => {
						vm.$router.push({name: 'login', query: {redirect: this.$route.path}})
					})
				}
			})	
		},
		//SEARCH PRODUCT BY KEYWORD
		getByKeyWords: function(list, value) {
			let search = new String(value).toLowerCase()
			let data   = list
			if(!search.length) {
				return data
			}
			let temp   = data.map(item => {
				let product = item.products.filter(product => product.name.toLowerCase().indexOf(search) > -1)
				if(product.length>0) {
					return product
				}
			})
			return new Array(temp)
		},
		//GET ALL PRODUCT BY STORE
		getProducts: function() {
			var vm = this
			return new Promise((resolve, reject) => {
				var vm = this 
				const data = {storeId: vm.store.id} 
				axios.post('/api/Dofuu/Checkout/GetProductByStore', data, {headers: getHeader()}).then(response => {
					if(response.status == 200) {
						vm.products = response.data.data
					}
					resolve(response)
				})
			})
		} 
	},
	computed: {
		...mapState({
			currentCity: state => state.cityStore.currentCity,
			offsetTop: state   => state.offsetTop,
			tabIndex: state    => state.storeStore.tabIndex,
			tabs: state        => state.storeStore.headers,
			isAuth: state      => state.authStore.isAuth,			
			store: state       => state.storeStore.store,
			loading: state     => state.storeStore.loading,
			rightDrawer: state => state.storeStore.rightDrawer,
			show: state        => state.cartStore.show,
			cart: state        => Object.assign({}, state.cartStore.cart),
			coupon: state      => state.cartStore.coupon
		}),
		options: function() {
			return {
				duration: this.duration,
				offset: this.offset,
				easing: this.easing
			}
		},
		//COUNT ITEM IN CART
		counts() {
			return this.$store.getters.counts
		},
		//SUBTOTAL PRICE ALL ITEM IN CART
		subTotal() {
			return this.$store.getters.subTotal
		},
		dealPrice: function() {
			if(this.coupon != null) {
				return -Math.floor(numeral(this.subTotal).value()*numeral(this.coupon.discountPercent).value()/100)
			}
			return 0
		},
		total: function() {
			return Math.floor(numeral(this.subTotal).value() + (this.dealPrice))
		},
		menu: function () {
			if(this.store.catalogues) {
				return this.getByKeyWords(this.store.catalogues, this.search)

			}
		}
	},
	watch: {
		'loading': function(val) {
			if(!val) {
				setTimeout(() => {
					this.offsetNavbarRight = this.$refs.target_navbar_right.offsetTop
				}, 300)
			}
		},
		'rightDrawer': function(val) {
			console.log(val) 
		}
	},	
	mounted: async function() {
		if(!this.loading) {
			
		}
		this.$store.dispatch('getToCart', this.store.id)	

		
	},
	beforeDestroy() {
		this.$store.commit('REMOVE_COUPON')
		this.$store.commit('CLOSE_CHECKOUT')
	}
}
</script>
<style scoped>
.card--sticky {
	position: fixed;
	right: 16px;
	top: 50px;
	bottom: auto;
	width: 318.578px
}
</style>