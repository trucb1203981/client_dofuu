<template>
	<v-container :grid-list-lg="$vuetify.breakpoint.mdAndUp" :grid-list-xs="$vuetify.breakpoint.smAndDown">
		<v-layout child-flex v-if="!loading" justify-end>
			<v-flex xs12 md7 lg8>
				<v-autocomplete prepend-inner-icon="search" :items="products" :loading="isLoading" :search-input.sync="search" cache-items class="mx-3 btn-custom" solo label="Tìm món ăn, thức uống" height="10" item-text="name" clearable :persistent-hint="search != null" hint="Vui lòng xóa từ khóa tìm kiếm để hiện đầy đủ menu"  @focus="focusSearchInput = true" @blur="focusSearchInput = false" :color="focusSearchInput ? 'blue' : 'black'" :background-color="focusSearchInput ? 'white' : 'grey lighten-3'"  :flat="!focusSearchInput" return-object @input="openCartDialog" :menu-props="{maxHeight: '200px'}">
					<template slot="no-data">
						<v-list-tile>
							<v-list-tile-title>
								Không tìm thấy món <span class="font-weight-bold">{{search}}</span>
							</v-list-tile-title>
						</v-list-tile>
					</template>
					<template slot="item" slot-scope="data" >
						<v-list-tile-avatar>
							<img :src="image(data.item.image)">
						</v-list-tile-avatar>
						<v-list-tile-content>
							<v-list-tile-title v-html="data.item.name"></v-list-tile-title>				
						</v-list-tile-content>				
					</template>
				</v-autocomplete>

				<!-- COUPON START -->
				<vue-coupon :coupon="store.coupon"></vue-coupon>
				<!-- COUPON END -->
				<!-- PRODUCT LIST START -->
				<v-content v-if="menu.length>0" v-for="(data, index) in menu" :key="index" class="pb-0">
					<v-subheader :id="'item_'+data.id" v-show="data.products.length>0" class="text-uppercase">{{data.name}}</v-subheader>
					<vue-product :products="data.products" @openCart="openCartDialog" />
				</v-content>
				<!-- PRODUCT LIST END -->
			</v-flex>

			<!-- RIGHT NAVBAR DESKTOP -->
			<v-flex xs12 md5 lg4  ref="target_navbar_right" v-if="$vuetify.breakpoint.mdAndUp" class="hidden-sm-and-down row px-0 text-xs-right">
				<v-card :class="{'card--sticky' : offsetTop>offsetNavbarRight-50 && $vuetify.breakpoint.mdAndUp}" style="z-index:4" :width="$vuetify.breakpoint.mdOnly ? '280' : '310'" class="card-radius elevation-1">

					<v-tabs grow   :value="`item-${tabIndex}`" dense>
						<v-tabs-slider color="yellow"></v-tabs-slider>
						<template v-for="(tab, index) in tabs">
							<v-tab :key="index" @click="changeTab(index)":href="`#item-${index}`" >
								<v-icon left v-if="index === 1" size="20">shopping_cart</v-icon>
								<v-icon left v-if="index === 0" size="20">assignment</v-icon>
								<v-badge color="red">
									<span slot="badge" v-if="counts>0 && index == 1">{{counts}}</span>
									<h5>{{tab.title}}</h5>
								</v-badge>
								
							</v-tab>
						</template>
					</v-tabs>

					
					<v-card  v-if="tabIndex==0" flat>
						<v-card-text>
							<a v-for="item in store.catalogues" @click="goTo('#item_'+item.id)">
								<v-card ripple  v-if="item.products.length>0" @click="goTo('#item_'+item.id)" :key="item.name" max-height="35" class="mb-2 text-xs-center card-radius" color="grey lighten-3">					
									<div class="body-2 text-uppercase">{{item.name}}</div>				
								</v-card>
							</a>
						</v-card-text>
					</v-card>
					

					<v-card v-else-if="tabIndex==1" class="transparent" >
						<v-card-text>
							<v-data-table v-if="cart && cart.items.length>0" :headers="headers" :items="cart.items" class="elevation-1 scroll-y" hide-actions hide-headers style="max-height:280px; overflow-x:hidden">
								<template slot="items" slot-scope="props" >
									<tr :key="props.item.rowId">
										<td class="text-xs-center">
											<div>{{props.item.name}}</div>
											<div><v-btn  outline color="primary" icon @click="props.expanded = ! props.expanded" small><v-icon>edit</v-icon></v-btn></div>
										</td>
										<td class="text-xs-center">
											<v-layout column>
												<v-flex xs4 class="py-0">
													<v-btn icon ripple @click.stop="addToCart(props.item)" class="ma-0">
														<v-icon color="green">add_box</v-icon>
													</v-btn>
												</v-flex>

												<v-flex xs4 class="py-0">
													{{props.item.qty}}
												</v-flex>

												<v-flex xs4 class="py-0">
													<v-btn icon ripple @click.stop="minusToCart(props.item)"  class="ma-0">
														<v-icon color="grey" >indeterminate_check_box</v-icon>
													</v-btn>
												</v-flex>
											</v-layout>									
										</td>
										<td>{{totalProduct(props.item) | formatPrice}}</td>	
									</tr>
								</template>
								<template slot="expand" slot-scope="props">
									<v-content class="grey lighten-3 pb-0">			
										<v-flex d-flex xs12>
											<v-text-field v-model="props.item.memo" :label="`Ghi chú ${props.item.name}`" ></v-text-field>
										</v-flex>
									</v-content>
								</template>
							</v-data-table>
						</v-card-text>

						<v-divider></v-divider>

						<!-- FIELD COUPON START -->
						<v-toolbar color="grey lighten-3" flat dense>
							<v-layout row justify-center align-center>

								<v-flex v-if="coupon == null">
									<v-text-field flat solo color="red accent-3" v-model="code" label="Nhập mã khuyến mãi"  class="btn-custom" background-color="grey lighten-4" @input="forceUppercase" @focus="focusCouponInput = true" @blur="focusCouponInput = false" :color="focusCouponInput ? 'blue' : 'black'" :background-color="focusCouponInput ? 'white' : 'grey lighten-3'"  :flat="!focusCouponInput"  @keyup.enter.exact="checkCoupon">
										<v-btn slot="append" class="ma-0" style="right: -10px" icon small :color="focusCouponInput ? 'blue' : 'grey lighten-3'" :outline="focusCouponInput" @click.prevent="checkCoupon" :loading="loadingCoupon">
											<v-icon :color="focusCouponInput ? 'blue' : 'grey'">send</v-icon>
										</v-btn>	
									</v-text-field>		
								</v-flex>
								
								<v-flex v-else class="pa-0">
									<v-chip close :value="coupon != null" color="green darken-3" text-color="white" @input="removeCoupon">
										<v-icon left>redeem</v-icon>
										{{coupon.coupon}}											
									</v-chip>
									<span class="text-xs-end red--text text--accent-3 font-weight-bold font-italic">Giảm {{coupon.discountPercent}}%</span>	
								</v-flex>

								<span v-if="alert.show" class="red--text text-lg-right">{{alert.message}}</span>

							</v-layout>			
						</v-toolbar>
						<!-- FIELD COUPON END -->

						<v-divider></v-divider>

						<v-layout row wrap>
							<v-flex	xs12>
								<v-list dense > 

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
											<span slot="activator">Phí vận chuyển: <v-icon size="20" color="primary" @click="showInfoDelivery = true">help</v-icon></span>
										</v-list-tile-content>
										<v-list-tile-content class="align-end" v-if="currentCity != null">
											<h3>{{currentCity.deliveries[1].price | formatPrice}}/km</h3>
										</v-list-tile-content>
									</v-list-tile>
								</v-list>
							</v-flex>
						</v-layout>
						<v-divider></v-divider>
						<div class="text-xs-center font-weight-bold" v-if="currentCity != null && subTotal < currentCity.service.minAmount">Đơn đặt hàng tối thiểu là: {{currentCity.service.minAmount | formatPrice}}</div>
						<v-card-actions>
							<v-btn block :disabled="processCheckout" color="red darken-1 white--text" dense @click.native="checkOut" round>
								Gửi đơn hàng
							</v-btn>
						</v-card-actions>
					</v-card>
				</v-card>			
			</v-flex>
		</v-layout>

		<!-- RIGHT NAVBAR MOBILE START -->
		<v-navigation-drawer fixed :clipped="$vuetify.breakpoint.mdAndUp" v-model="drawer" right class="hidden-lg-only hidden-md-only" >
			<v-tabs icons-and-text grow :value="`item-${tabIndex}`" centered>

				<v-tabs-slider color="yellow"></v-tabs-slider>

				<v-tab v-for="(tab, index) in tabs" :key="index" @click="changeTab(index)":href="`#item-${index}`" >
					<h5>{{tab.title}}</h5>
					<v-badge color="red" v-if="index == 1" overlap>
						<span slot="badge" v-if="counts>0">{{counts}}</span>
						<v-icon left size="20">shopping_cart</v-icon>						 
					</v-badge>

					<v-icon left v-if="index == 0" size="20">assignment</v-icon>

				</v-tab>
			</v-tabs>

			<v-card v-if="tabIndex==0" flat >
				<v-card-text >
					<a v-for="item in store.catalogues" @click="goTo('#item_'+item.id)">
						<v-card ripple  v-if="item.products.length>0" @click="goTo('#item_'+item.id)" :key="item.name" max-height="35" class="mb-2 text-xs-center card-radius" color="grey lighten-3">					
							<div class="body-2 text-uppercase">{{item.name}}</div>				
						</v-card>
					</a>
				</v-card-text>
			</v-card>


			<v-card v-else-if="tabIndex==1" class="transparent">
				<v-card-text>

					<v-data-table v-if="cart && cart.items.length>0" :headers="headers" :items="cart.items" class="elevation-1 scroll-y" hide-actions hide-headers style="max-height:280px; overflow-x:hidden">
						<template slot="items" slot-scope="props">
							<tr>
								<td class="text-xs-center">
									<div>{{props.item.name}}</div>
									<div><v-btn  outline color="primary" icon @click="props.expanded = ! props.expanded" small><v-icon>edit</v-icon></v-btn></div>
								</td>
								<td class="text-xs-center">
									<v-layout column>
										<v-flex xs4 class="py-0">
											<v-btn icon ripple @click.stop="addToCart(props.item)" class="ma-0">
												<v-icon color="green">add_box</v-icon>
											</v-btn>
										</v-flex>

										<v-flex xs4 class="py-0">
											{{props.item.qty}}
										</v-flex>

										<v-flex xs4 class="py-0">
											<v-btn icon ripple @click.stop="minusToCart(props.item)"  class="ma-0">
												<v-icon color="grey" >indeterminate_check_box</v-icon>
											</v-btn>
										</v-flex>
									</v-layout>									
								</td>
								<td>{{totalProduct(props.item) | formatPrice}}</td>	
							</tr>
						</template>
						<template slot="expand" slot-scope="props">
							<v-container fluid grid-list-md class="grey lighten-3">			
								<v-flex d-flex xs12>
									<v-text-field
									v-model="props.item.memo"
									:label="`Ghi chú ${props.item.name}`"
									></v-text-field>
								</v-flex>
							</v-container>
						</template>
					</v-data-table>

				</v-card-text>

				<v-divider></v-divider>

				<!-- FIELD COUPON START -->
				<v-toolbar color="grey lighten-3" flat dense>
					<v-layout row justify-center align-center>

						<v-flex v-if="coupon == null">
							<v-text-field flat solo color="red accent-3" v-model="code" label="Nhập mã khuyến mãi"  class="btn-custom" background-color="grey lighten-4" @input="forceUppercase" @focus="focusCouponInput = true" @blur="focusCouponInput = false" :color="focusCouponInput ? 'blue' : 'black'" :background-color="focusCouponInput ? 'white' : 'grey lighten-3'"  :flat="!focusCouponInput" @keyup.enter.exact="checkCoupon">
								<v-btn slot="append" class="ma-0" style="right: -10px" icon small :color="focusCouponInput ? 'blue' : 'grey lighten-3'" :outline="focusCouponInput" @click.prevent="checkCoupon" :loading="loadingCoupon">
									<v-icon :color="focusCouponInput ? 'blue' : 'grey'">send</v-icon>
								</v-btn>	
							</v-text-field>		
						</v-flex>

						<v-flex v-else class="pa-0">
							<v-chip close :value="coupon != null" color="green darken-3" text-color="white" @input="removeCoupon">
								<v-icon left>redeem</v-icon>
								{{coupon.coupon}}											
							</v-chip>
							<span class="text-xs-end red--text text--accent-3 font-weight-bold font-italic">Giảm {{coupon.discountPercent}}%</span>	
						</v-flex>

						<span v-if="alert.show" class="red--text text-lg-right">{{alert.message}}</span>

					</v-layout>			
				</v-toolbar>
				<!-- FIELD COUPON END -->

				<v-divider></v-divider>

				<v-layout row wrap>
					<v-flex	xs12>

						<v-list dense> 
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
									<span>Phí vận chuyển: <v-icon size="20" color="primary" @click="showInfoDelivery = true">help</v-icon></span>
								</v-list-tile-content>
								<v-list-tile-content class="align-end" v-if="currentCity != null">
									<h3>{{currentCity.deliveries[1].price | formatPrice}}/km</h3>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>						
					</v-flex>
				</v-layout>

				<v-divider></v-divider>

				<h4 class="text-xs-center" v-if="currentCity != null && subTotal < currentCity.service.minAmount">Đơn đặt hàng tối thiểu là: {{currentCity.service.minAmount | formatPrice}}</h4>
				<v-card-actions>
					<v-btn block :disabled="processCheckout" color="red darken-1 white--text" dense @click.native="checkOut" round>
						Gửi đơn hàng
					</v-btn>
				</v-card-actions>
			</v-card>

		</v-navigation-drawer>
		<!-- RIGHT NAVBAR MOBILE END -->

		<v-dialog v-model="showInfoDelivery" max-width="500px" v-if="!!currentCity" :fullscreen="$vuetify.breakpoint.xsOnly">
			<v-card>
				<v-toolbar dense flat>
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
							<v-list-tile-content><span>Phí vận chuyển tối thiểu: <strong>{{currentCity.deliveries[0].price | formatPrice}}</strong> </span></v-list-tile-content>
						</v-list-tile>
						<v-list-tile>
							<v-list-tile-content><span>Phí vận chuyển sẽ được tính theo khoảng cách: <strong>{{currentCity.deliveries[1].price | formatPrice}}/km</strong> </span></v-list-tile-content>
						</v-list-tile>
						<v-list-tile>
							<v-list-tile-content class="font-weight-bold red--text font-italic">Phí vận chuyển có thể thay đổi tùy theo thời điểm</v-list-tile-content>
						</v-list-tile>
					</v-list>
				</v-card-text>
				<v-card-actions>
					<v-btn block color="grey lighten-4" round small @click.native="showInfoDelivery = false">Đóng</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<v-dialog v-model="cartDialog" persistent scrollable disabled max-width="1000" >
			<v-card v-if="editedItem != null" class="grey lighten-4" >
				<v-toolbar color="red accent-4" dense class="elevation-0" dark flat height="30">
					<v-toolbar-title class="body-1 px-0 text-uppercase"> {{editedItem.name}}</v-toolbar-title>
					<v-spacer></v-spacer>
					<div class="font-weight-bold">{{totalProduct(editedItem) | formatPrice}}</div>
				</v-toolbar>

				<v-card-text class="white" style="height: 350px" :class="{'px-0': $vuetify.breakpoint.xsOnly}">
					<v-container fluid grid-list-xs>
						<v-layout row wrap>

							<v-flex  xs12 md4>
								<v-card>
									<v-img :src="image(editedItem.image)" :height="$vuetify.breakpoint.mdAndUp ? '250' : '200'">			
									</v-img>
								</v-card>				
							</v-flex>

							<v-flex xs12 md8>
								<v-container class="py-0 my-0">
									<v-layout row wrap>
										<v-flex xs12>
											<v-card flat v-if="editedItem.description != null" class="card-radius" color="grey lighten-4 mt-2">
												<div class="pa-2">{{ editedItem.description }}</div>				
											</v-card>
											<v-radio-group mandatory v-model="editedItem.size" :row="$vuetify.breakpoint.mdAndUp" hide-details class="py-0">
												<v-radio color="green darken-3" :value="size" v-for="(size, i) in sizes" :key="i">
													<span slot="label" class="black--text body-1">{{size.name}} <strong>({{size.price | formatPrice}})</strong></span>
												</v-radio>
											</v-radio-group>

										</v-flex>

										<v-flex xs12>
											<v-select v-if="editedItem.haveTopping" v-model="editedItem.toppings" :items="store.toppings" hint="Chọn thêm topping"  label="Phần thêm" multiple persistent-hint small-chips box :menu-props="{ maxHeight: '250' }" dense return-object clearable>	
												<template slot="selection" slot-scope="{index, item, parent, selected}">
													<v-chip v-if="index <= 4" :selected="selected" color="white" small>
														{{ item.name }}
														<v-icon small @click="parent.selectItem(item)" >close</v-icon>							
													</v-chip>
													<span
													v-if="index === 5"
													class="grey--text caption"
													>(+{{ editedItem.toppings.length - 5 }} Khác)</span>
												</template>

												<template slot="item" slot-scope="{item, parent, tile}">
													<v-list-tile-content>						
														{{item.name}}
													</v-list-tile-content>

													<v-list-tile-action>
														{{item.price |formatPrice}}
													</v-list-tile-action>
												</template>
											</v-select>
										</v-flex>

										<v-flex xs12>
											<div>Số lượng: 
												<span>
													<v-btn icon ripple @click.stop="editedItem.qty++" class="ma-0">
														<v-icon color="green darken-3">add_box</v-icon>
													</v-btn>
												</span>
												<span>												
													{{editedItem.qty}}
												</span>
												<span>
													<v-btn icon ripple @click.stop="editedItem.qty > 0 ? editedItem.qty-- :''"  class="ma-0">
														<v-icon color="grey" >indeterminate_check_box</v-icon>
													</v-btn>
												</span>				
											</div>

										</v-flex>

										<v-flex xs12>
											<v-text-field
											solo
											class="btn-custom"
											background-color="grey lighten-4 elevation-1"
											v-model="editedItem.memo"
											label="Ghi chú" 
											flat
											></v-text-field>
										</v-flex>

									</v-layout>		
								</v-container>
							</v-flex>
						</v-layout>
					</v-container>			
				</v-card-text>
				<v-divider></v-divider>
				<v-card-actions style="max-height: 44px">
					<v-btn color="red darken-1" @click.native="closeCartDialog" class="mr-5 white--text" round small>Hủy bỏ</v-btn>
					<v-spacer></v-spacer>									
					<v-btn color="green darken-3" class="white--text" round @click.prevent="addToCart(editedItem)" :loading="processAddCart" :disabled="editedItem.qty == 0" small>{{totalProduct(editedItem) | formatPrice}} <v-icon right>add_shopping_cart</v-icon></v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<!-- DIALOG ALERT START-->
		<v-dialog v-model="dialog" max-width="400">
			<v-card>
				<v-toolbar dense color="grey lighten-4" class="elevation-0">
					<v-avatar size="24px" tile>
						<img src="~/static/dofuu24x24.png">
					</v-avatar>
					<v-toolbar-title>
						Thông báo
					</v-toolbar-title>
				</v-toolbar>
				<v-divider></v-divider>
				<v-card-text class="text-xs-center" v-if="checkOutSuccess">
					<div class="green--text">Quý khách đã đặt hàng thành công</div>
					<v-btn color="success" :to="{name:'history'}" @click="$store.commit('CLOSE_CHECKOUT_SUCCESS')" >Lịch sử đặt món</v-btn>
				</v-card-text>
				<v-card-text v-if="dayOff">
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
				<v-card-actions v-if="dayOff">
					<v-btn block color="grey lighten-4" round small @click.native="dialog = false">Đóng</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<!-- DIALOG ALERT END-->
		<!-- CHECKOUT DIALOG START-->
		<vue-checkout :store.sync="store" v-if="store != null"></vue-checkout>
		<!-- CHECKOUT DIALOG END-->
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
	import CheckoutDialog from '@/components/CheckoutDialog'
	import ProductList from '@/components/ProductList'
	import Coupon from '@/components/Coupon'
	import * as easings from 'vuetify/es5/util/easing-patterns'
	// const CheckoutDialog = () => ({
	//   // The component to load (should be a Promise)
	//   component: import('@/components/CheckoutDialog'),
	//   // Delay before showing the loading component. Default: 200ms.
	//   delay: 200,
	//   // The error component will be displayed if a timeout is
	//   // provided and exceeded. Default: Infinity.
	//   timeout: 3000
	// })
	export default {
		scrollToTop: true,
		mixins: [index],
		components: {
			'vue-coupon': Coupon,
			'vue-checkout' : CheckoutDialog,
			'vue-product' : ProductList
		},
		asyncData() {
			return {
				duration: 50,
				offset: -50,
				easing: 'easeInOutQuint',
				offsetNavbarRight: 184,
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
				storeInfo: null,
				search: null,
				isLoading: false,
				drawer: false,
				dialog: false,
				message:'',
				products: [],
				showInfoDelivery: false,
				loadingCoupon: false,
				code: null,
				focusCouponInput: false,
				couponChip: true,
				alert: {
					show: false,
					message: '',
					type: 'error'
				},
				cartDialog:false,
				editedItem: {
					rowId: null, 
					size: null,
					memo: null,
					qty: 1,
					subTotal: 0,
					toppings: []
				},
				default: {
					rowId: null, 
					size: null,
					memo: null,
					qty: 1,
					subTotal: 0,
					toppings: []				
				},
				sizes: [],
				processAddCart: false,
				dayOff:false,
				focusSearchInput: false
			}
		},
		methods: {
			checkCoupon: async function() {
				var vm = this
				if(vm.code == null) {
					vm.alert = Object.assign({}, {show: true, message: 'Vui lòng nhập mã giảm giá', type: 'error'})
				} else {

					if(!vm.loadingCoupon) {
						vm.loadingCoupon = await !vm.loadingCoupon
						const data = Object.assign({}, {storeID: this.store.id, coupon: this.code, subTotal: this.subTotal, districtID: this.store.districtId, cityID: this.currentCity.id})
						setTimeout(function() {						

							axios.post('/api/Dofuu/CheckCouponCode', data, {withCredentials:true}).then(response => {

								if(response.data.type === 'success') {
									vm.$store.commit('ADD_COUPON', response.data)
								}

								if(response.data.type === 'error') {
									vm.alert = Object.assign({}, {show: true, message: response.data.message, type: 'error'})	
								}

							}).finally(() => {
								vm.loadingCoupon = !vm.loadingCoupon
							})

						},300)
					}
				}
				setTimeout(() => {
					vm.alert = Object.assign({}, {show: false, message: '', type: 'error'})
				},3000)		

			},
			removeCoupon: function() {
				this.$store.dispatch('removeCoupon')
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
								this.dayOff = false
							} else {
								if(moment(moment(), 'HH:mm:ss').format('HH:mm') < moment(time.from, 'HH:mm:ss').format('HH:mm')) {
									this.dayOff  = true
									this.message = 'Quán hiện tại chưa mở cửa'
								} else if(moment(moment(), 'HH:mm:ss').format('HH:mm') >= moment(time.to, 'HH:mm:ss').format('HH:mm')) {
									this.dayOff  = true
									this.message = 'Quán hiện tại đã đóng cửa'
								}
							}
						})
					} else {
						this.dayOff  = true
						this.message = 'Quán hôm nay nghỉ'
					}			
				})			
			},
		// SCROLLING TO CATALOGUE
		goTo: function (target) {
			this.drawer = false
			this.$vuetify.goTo(target, this.options)
		},
		openCartDialog: async function(item) {
			if(!this.dayOff) {
				this.editedItem =  Object.assign({}, this.default)
				this.sizes      =  []		
				var uuid        = require("uuid");
				var rowId       = uuid.v4();
				if(item.sizes.length >0) {
					await item.sizes.forEach(size => {
						if(size.price > 0) {					
							this.sizes.push(size)
							if(this.editedItem.size === null) {
								this.editedItem.size = size
							}
						}
					})
				}
				this.editedItem       = Object.assign(this.editedItem, item)
				this.editedItem.rowId = rowId
				this.cartDialog       = true
			} else {				
				this.dialog  = true
			}
		},
		closeCartDialog: async function() {
			this.cartDialog = false
			this.editedItem = Object.assign({}, this.default)
			this.sizes      =  []				
		},
		// ADD ITEM TO CART
		addToCart: async function (product) {
			var vm              = this
			if(!vm.processAddCart) {
				vm.processAddCart = true
				setTimeout(() => {
					var addToCart = new Promise(async (resolve, reject) => {
						const productIndex  = await vm.cart.items.findIndex(item => {
							return item.rowId === product.rowId
						})
						if (productIndex > -1) {
							vm.cart.items[productIndex].qty++
						} else {
							vm.cart.items.push(product)
						}
						vm.$store.commit('FETCH_CART', vm.cart)
						window.localStorage.setItem('cart', JSON.stringify(vm.cart))
					})
					vm.processAddCart = false
					vm.closeCartDialog()
					this.changeTab(1)
				}, 300)				
			}	
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
		//CHECK OUT CART
		checkOut: async function() {
			var vm    = this
			if(this.dayOff) {
				this.dialog  = true
				return
			} else {
				const city = this.$store.getters.getCityBySlug(this.$route.params.city)
				vm.$store.dispatch('getUser').then((response) => {
					if(response.status == 200) {
						vm.getProducts().then(async (response) => {
							if(response.status == 200) {
								var array = []
								await vm.products.find(product => {
									vm.cart.items.forEach(item => {
										if(product.id == item.id) {
											array.push(item)
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
		},
		//SEARCH PRODUCT BY KEYWORD
		getByKeyWords: function(list, value) {
			var search   = value
			var products = []
			var data     = this.store.catalogues.slice(0)

			if(search == null || typeof search === 'undefined') {

				return data
			}

			let temp   = data.filter(item => {
				return item.products.some((product) => product.name.toLowerCase().indexOf(search.toLowerCase()) > -1)
			})
			return temp
		},
		//GET ALL PRODUCT BY STORE
		getProducts: function() {
			return new Promise((resolve, reject) => {
				var vm = this 
				const data = {storeId: vm.store.id} 
				axios.get('/api/GetStore/'+vm.store.id+'/Product').then(response => {
					if(response.status == 200) {
						vm.products = response.data.data
					}
					resolve(response)
				})
			})
		},
		totalProduct: function(product) {
			if(product != null) {
				var total        = 0
				var totalTopping = 0
				if(product.toppings.length > 0) {
					product.toppings.forEach(topping => {
						totalTopping = totalTopping + parseInt(topping.price)
					})
				}				
				if(product.size != null) {					
					total = parseInt(product.size.price) + totalTopping
				}
			}
			product.subTotal = total*product.qty
			return total*product.qty
		},
		forceUppercase(e) {
			this.code = e.toUpperCase()
		}
	},
	computed: {
		...mapState({
			currentCity: state    => state.cityStore.currentCity,
			offsetTop: state      => state.offsetTop,
			tabIndex: state       => state.storeStore.tabIndex,
			tabs: state           => state.storeStore.headers,
			isAuth: state         => state.authStore.isAuth,			
			store: state          => state.storeStore.store,
			loading: state        => state.storeStore.loading,
			show: state           => state.cartStore.show,
			cart: state           => Object.assign({}, state.cartStore.cart),
			coupon: state         => state.cartStore.coupon,
			cartDrawer:state      => state.cartStore.cartDrawer,
			checkOutSuccess:state => state.cartStore.checkOutSuccess
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
			if(this.store.catalogues.length>0) { 
				if(this.store.catalogues) {
					return this.getByKeyWords(this.store.catalogues.slice(0), this.search)
				}
			}
		},
		processCheckout: function() {
			if(this.currentCity != null) {
				if(this.subTotal >= this.currentCity.service.minAmount) {
					return false
				}
			}
			return true
		}
	},
	watch: {
		'cartDrawer': function(val) {
			if(val) {
				this.drawer = true
			}
		},
		'drawer': function(val) {
			if(!val) {
				this.$store.commit('CLOSE_CART')
			}
		},
		'checkOutSuccess': function(val) {
			if(val) {
				this.dialog = true
			}
		},
		'dialog': function(val) {
			if(!val) {
				this.$store.commit('CLOSE_CHECKOUT_SUCCESS')
			}
		},
		search: function(val) {
			if(this.products.length>0) return
				this.isLoading = true
			this.getProducts().finally(() => {
				this.isLoading = false
			})
		}
	},	
	mounted() {
		this.checkDayOff()
	},
	beforeDestroy() {
		this.$store.dispatch('removeCoupon')
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
}
</style>