<template>
	<v-container :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-card color="grey lighten-4">
			<v-toolbar color="white" flat dense>
				<v-toolbar-title>
					{{title}}
				</v-toolbar-title>
			</v-toolbar>
			<v-divider></v-divider>
			<v-card-title>
				<v-layout row wrap align-center justify-center>

					<v-flex xs12 md3>
						<v-select
						prepend-icon="lens"
						:items="status"
						v-model="editedItem.statusId"
						item-value="id"
						item-text="name"
						label="Trạng thái"
						></v-select>
					</v-flex>

					<v-flex xs12 md3>						
						<v-menu
						ref="fromDialog"
						:close-on-content-click="false"
						v-model="fromModal"
						:nudge-right="40"
						lazy
						transition="scale-transition"
						offset-y
						full-width
						max-width="290px"
						min-width="290px">
						<v-text-field
						slot="activator"
						label="Từ ngày"
						v-model="fromDateString"
						prepend-icon="event"
						readonly></v-text-field>
						<v-date-picker locale="vn-vi" :show-current="false" v-model="editedItem.fromDate" no-title @input="$refs.fromDialog.save(editedItem.fromDate)"></v-date-picker></v-menu>
					</v-flex>

					<v-flex xs12 md3>
						<v-menu
						ref="toDialog"
						:close-on-content-click="false"
						v-model="toModal"
						:nudge-right="40"
						lazy
						transition="scale-transition"
						offset-y
						full-width
						max-width="290px"
						min-width="290px"
						>

						<v-text-field
						slot="activator"
						label="Đến ngày"
						v-model="toDateString"
						prepend-icon="event"
						readonly
						></v-text-field>
						<v-date-picker locale="vn-vi" :show-current="false" v-model="editedItem.toDate" no-title @input="$refs.toDialog.save(editedItem.toDate)"></v-date-picker></v-menu>
					</v-flex>

					<v-flex xs12 md1>
						<v-btn :block="$vuetify.breakpoint.smAndDown" round color="info" @click="getOrders" :loading="loading" small>
							<v-icon>search</v-icon>
						</v-btn>		
					</v-flex>

				</v-layout>
			</v-card-title>
			<v-container grid-list-md>
				<v-layout row wrap align-center justify-center>
					<v-data-iterator
					:items="orders"
					rows-per-page-text="Số hàng"
					content-tag="v-layout"
					row
					wrap
					no-data-text="Không tìm thấy đơn đặt hàng"
					:hide-actions="orders.length===0"
					>
					<v-flex	slot="item"	slot-scope="props" xs12 sm6 md6	lg6>
						<v-card class="card-radius">
							<v-toolbar color="grey lighten-5" dense flat>
								<v-avatar color="primary" size="36">
									<img :src="image(props.item.store.avatar)" alt="alt">
								</v-avatar>
								<v-toolbar-title>
									{{props.item.store.name}}
								</v-toolbar-title>
								<v-spacer></v-spacer>
								<div class="font-weight-bold">{{ props.item.id }}</div>
							</v-toolbar>
							<v-divider></v-divider>
							<v-card-text>
								<v-layout row wrap class="caption">
									<v-flex xs4>
										Nơi đặt:
									</v-flex>
									<v-flex xs8 class="text-xs-right font-weight-bold">
										<nuxt-link :to="{name: 'city-store', params: {city: props.item.store.district.city.slug, store: props.item.store.slug}}">{{ props.item.store.name }}</nuxt-link>
									</v-flex>

									<v-flex xs12 class="text-xs-right">
										{{ props.item.store.address }}
									</v-flex>

									<v-flex xs4>
										Ngày đặt:
									</v-flex>
									<v-flex xs8 class="text-xs-right">
										{{ props.item.bookingDate }}
									</v-flex>

									<v-flex xs4>
										Ngày giao:
									</v-flex>
									<v-flex xs8 class="text-xs-right font-weight-bold">
										{{ props.item.receiveDate | formatDate }} {{ props.item.receiveTime }}
									</v-flex>

									<v-flex xs4>
										CSKH:
									</v-flex>
									<v-flex xs8 class="text-xs-right">
										<div v-if="props.item.employee != null">{{ props.item.employee.name }}</div>	
									</v-flex>

									<v-flex xs4>
										Giao hàng:
									</v-flex>
									<v-flex xs8 class="text-xs-right">
										<div v-if="props.item.shipper != null">{{ props.item.shipper.name }}</div>
									</v-flex>

									<v-flex xs4>
										Tổng tiền:
									</v-flex>
									<v-flex xs8 class="text-xs-right">
										<div><strong>{{ props.item.total | formatPrice}}</strong></div>
										<div class="primary--text"><strong>{{ props.item.payment.paymentMethod}}</strong></div>
									</v-flex>

									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="text-xs-right">
										<div class="success--text" v-if="props.item.statusId == orderStatus('Thành công')"><strong>{{props.item.statusName}}</strong></div>
										<h4 v-if="props.item.statusId != orderStatus('Thành công')"><span v-if="disableCancelOrder(props.item.statusName)"><v-btn small color="red accent-3" :disabled="orderStatus('hủy') == props.item.statusId" @click.prevent="showCancelDialog(props.item)" class="white--text" round>Hủy<v-icon small right>block</v-icon>
										</v-btn></span>{{props.item.statusName}}</h4>
									</v-flex>
								</v-layout>
								
							</v-card-text>
							
							<v-divider></v-divider>
							<v-card-actions>								
								<v-btn small block @click.prevent="getDetails(props.item)" round>Xem chi tiết</v-btn>
							</v-card-actions>
						</v-card>
					</v-flex>
				</v-data-iterator>
			</v-flex>
		</v-layout>
	</v-container>	

	<v-dialog scrollable max-width="800px" v-model="dialog" :value="dialog" v-if="dialog" :fullscreen="$vuetify.breakpoint.xsOnly">
		<v-card v-if="order != null">
			<v-card-title> 
				<v-tabs
				:value="`item-${tab}`"
				slider-color="primary"
				centered
				>
				<v-tab :href="`#item-0`" :value="0" @click.prevent="changeTab(0)">
					Chi tiết món
				</v-tab>
				<v-tab :href="`#item-1`" :value="1" @click.prevent="changeTab(1)">
					Bản đồ
				</v-tab>
				<v-tab :href="`#item-2`" :value="2" @click.prevent="changeTab(2)">
					Trạng thái
				</v-tab>
				<v-tab :href="`#item-3`" :value="3" @click.prevent="changeTab(3)">
					Thông tin						
				</v-tab>
			</v-tabs>
		</v-card-title>
		<v-divider></v-divider>
		<v-card-text style="height: 400px;">
			<div v-show="tab == 0">
				<v-data-table
				:headers="headerDetails"
				:items="order.items"
				hide-actions
				class="elevation-1">
				<template slot="headerCell" slot-scope="props">
					<span class="red--text text--accent-3">
						{{ props.header.text }}
					</span>
				</template>
				<template slot="items" slot-scope="props">
					<td>
						{{props.item.name}}
						<h4 v-if="props.item.pivot.toppings.length > 0">Phần thêm: <span v-for="topping in props.item.pivot.toppings">{{topping.name}} ({{topping.price | formatPrice}}). </span></h4>
						<div v-if="props.item.pivot.memo != null">Ghi chú: {{props.item.pivot.memo}}</div>
					</td>
					<td class="text-xs-center">
						{{props.item.pivot.quantity}}
					</td>
					<td>
						{{props.item.pivot.price | formatPrice}}
					</td>
					<td>
						{{props.item.pivot.total | formatPrice}}
					</td>
				</template>
				<template slot="footer">
					<tr>
						<td colspan="3">Tạm tính</td>
						<td colspan="3"><strong>{{order.subTotal | formatPrice}}</strong></td>
					</tr>
					<tr>
						<td colspan="3">Phí vận chuyển:</td>	
						<td><strong>{{order.deliveryPrice | formatPrice}}</strong></td>
					</tr>
					<tr>
						<td colspan="3">Giảm giá:</td>
						<td><strong>{{-order.discountTotal | formatPrice}}</strong></td>
					</tr>	
					<tr>
						<td colspan="3">Tổng:</td>
						<td class="red--text"><strong>{{order.total | formatPrice}}</strong></td>
					</tr>			
				</template></v-data-table>
			</div>
			<div v-show="tab == 1">
				<v-layout row wrap v-if="matrix.show">
					<v-flex xs3>Từ:</v-flex>
					<v-flex xs9>{{matrix.start}}</v-flex>
					<v-flex xs3>
						Đến:
					</v-flex>
					<v-flex xs9>
						{{matrix.end}}
					</v-flex>
					<v-flex xs3>
						Khoảng cách:
					</v-flex>
					<v-flex xs9>
						<span class="primary--text">
							<strong>
								{{matrix.distance}}
							</strong>
						</span>
					</v-flex>
					<v-flex xs3>
						Phí vận chuyển:
					</v-flex>
					<v-flex xs9>
						<span class="red--text"><strong>
							{{order.deliveryPrice | formatPrice}}
						</strong></span>
					</v-flex>
				</v-layout>
				<div id="map"></div>
			</div>
			<div v-show="tab == 2">
				<v-layout row wrap v-if="order.statusId == orderStatus('Hủy')"> 
					<v-flex xs5 class="text-xs-right">
						Trạng thái:
					</v-flex>
					<v-flex xs5 offset-xs1 class="red--text">
						<strong>
							{{order.statusName}}
						</strong>
					</v-flex>

					<v-flex xs5 class="text-xs-right">
						Thông điệp:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<div>Rất tiếc đơn hàng của quý khách đã bị hủy.</div>
						<div><strong>Cám ơn bạn đã sử dụng hệ thống.</strong></div>
					</v-flex>

					<v-flex xs5 class="text-xs-right">
						Lý do:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<div v-for="(text, index) in order.orderNotes" :key="index">
							{{text}}
						</div>
					</v-flex>

				</v-layout>
				<v-card>						
					<v-stepper v-model="order.statusId" :value="order.statusId" v-if="order.statusId != orderStatus('Hủy')">
						<v-stepper-header>
							<v-stepper-step step="1" :complete="order.statusId > orderStatus('Chờ xử lý') && order.statusId != orderStatus('Hủy')">Chờ xử lý</v-stepper-step>		
							<v-divider></v-divider>
							<v-stepper-step step="2" :complete="order.statusId > orderStatus('Đang xử lý') && order.statusId != orderStatus('Hủy')">Đang xử lý</v-stepper-step>				
							<v-divider></v-divider >
							<v-stepper-step step="3" :complete="order.statusId > orderStatus('Xác nhận') && order.statusId != orderStatus('Hủy')">Xác nhận</v-stepper-step>
							<v-divider></v-divider>
							<v-stepper-step step="4" :complete="order.statusId > orderStatus('Đang giao') && order.statusId != orderStatus('Hủy')">Đang giao</v-stepper-step>
							<v-divider></v-divider>
							<v-stepper-step step="5" :complete="order.statusId == orderStatus('Thành công') && order.statusId != orderStatus('Hủy')">Thành công</v-stepper-step>
						</v-stepper-header>
						<v-stepper-items>
							<v-stepper-content step="1">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="yellow--text text--accent-4">
										<strong>
											{{order.statusName}}
										</strong>
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>Đơn đặt hàng của quý khách đang chờ nhân viên tiếp nhận.</div>
										<div><strong>Xin quý khách vui lòng chờ trong giây lát.</strong></div>
										<div><strong>Cám ơn.</strong></div>
									</v-flex>
								</v-layout>
							</v-stepper-content>
							<v-stepper-content step="2">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8>
										{{order.statusName}}
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>
											Đơn hàng của quý khách đã được nhân viên<span v-if="!!order.employee"><strong> {{order.employee.name}} </strong></span>tiếp nhận và xử lý.
										</div>
										<div>
											<strong>Xin quý khách vui lòng chờ trong giây lát.
											</strong>
										</div>
										<div><strong>Cám ơn.</strong></div>
									</v-flex>
								</v-layout>
							</v-stepper-content>
							<v-stepper-content step="3">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8>
										{{order.statusName}}
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>
											Đơn hàng của quý khách đã được xác nhận thành công.
										</div>
										<div class="font-weight-bold">Xin quý khách vui lòng chờ ít phút để cửa hàng chuẩn bị.</div>
										<div class="font-weight-bold">Cám ơn.</div>
									</v-flex>
								</v-layout>
							</v-stepper-content>
							<v-stepper-content step="4">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8>
										{{order.statusName}}
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>
											Đơn hàng của quý khách đã được nhân viên <span class="font-weight-bold"  v-if="!!order.shipper">{{order.shipper.name}}</span> giao đến.
										</div>
										<div>
											<strong>
												Xin quý khách vui lòng chờ đợi ít phút.
											</strong>
										</div>
										<div>
											<strong>
												Cám ơn.
											</strong>
										</div>											
									</v-flex>
								</v-layout>
							</v-stepper-content>
							<v-stepper-content step="5">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="success--text">
										<strong>{{order.statusName}}</strong>
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>Đơn hàng của quý khách đã được giao thành công.</div>			
										<div class="font-weight-bold">Chúc qúy khách dùng ngon miệng.</div>
										<div class="font-weight-bold">Cám ơn quý khách đã đặt hàng từ hệ thống.</div>
									</v-flex>
								</v-layout>
							</v-stepper-content>
						</v-stepper-items>
					</v-stepper>
				</v-card>
			</div>
			<div v-show="tab == 3">
				<v-layout row wrap class="grid-list-md">
					<v-flex xs5 class="text-xs-right">
						Nơi đặt:
					</v-flex>

					<v-flex xs5 offset-xs1>
						<div class="primary--text">
							<strong>
								<nuxt-link :to="{name: 'city-store', params: {city: order.store.district.city.slug, store: order.store.slug}}">
									{{order.store.name}}
								</nuxt-link>
							</strong>
						</div>
						<div>
							{{order.store.address}}
						</div>
					</v-flex>

					<v-flex xs5  class="text-xs-right">
						Người nhận:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<strong>
							{{order.name}}							
						</strong>
					</v-flex>

					<v-flex xs5  class="text-xs-right">
						Địa chỉ nhận:
					</v-flex>
					<v-flex xs5 offset-xs1>
						{{order.address}}
					</v-flex>

					<v-flex xs5  class="text-xs-right">
						Số điện thoại:
					</v-flex>
					<v-flex xs5 offset-xs1>
						{{order.phone}}
					</v-flex>

					<v-flex xs5  class="text-xs-right">
						Ngày đặt:
					</v-flex>
					<v-flex xs5 offset-xs1>
						{{order.bookingDate}}
					</v-flex>

					<v-flex xs5  class="text-xs-right"> 
						Ngày nhận:
					</v-flex>
					<v-flex xs5 offset-xs1>
						{{order.receiveDate | formatDate}} {{order.receiveTime}}
					</v-flex>						

					<v-flex xs5  class="text-xs-right">
						Phí vận chuyển:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<strong>{{order.deliveryPrice | formatPrice}}</strong>
					</v-flex>

					<v-flex xs5  class="text-xs-right">
						Tổng tiền:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<strong>{{order.total | formatPrice}}</strong>
					</v-flex>

					<v-flex xs5  class="text-xs-right">
						Ghi chú:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<strong>{{order.memo}}</strong>
					</v-flex>
				</v-layout>
			</div>
		</v-card-text>
		<v-divider></v-divider>
		<v-card-actions class="justify-center">
			<span class="red--text">
				<strong>
					Tổng: {{order.total | formatPrice}}
				</strong>
			</span>
			<v-spacer></v-spacer>
			<v-btn color="blue darken-1" dark @click.native="dialog = false" small round>Đóng</v-btn>
		</v-card-actions>
	</v-card>
</v-dialog>
<v-dialog v-model="cancelDialog" :value="cancelDialog" max-width="500">
	<v-card>
		<v-toolbar color="transparent" dense flat>
			<v-toolbar-title>Hủy đơn đặt hàng?</v-toolbar-title>
		</v-toolbar>
		<v-divider></v-divider>
		<v-card-text>
			<v-checkbox v-for="(item, index) in reasons" :label="item.note" v-model="notes" :value="item.note" :key="index"></v-checkbox>
		</v-card-text>
		<v-divider></v-divider>
		<v-card-actions>
			<v-spacer></v-spacer>
			<v-btn @click.native="cancelDialog = false" flat color="red darken-1" outline small round>Hủy</v-btn>
			<v-btn color="blue" dark @click.native="cancelOrder" small round>Chấp nhận</v-btn>
		</v-card-actions>
	</v-card>
</v-dialog>
</v-card>
</v-container>	
</template>

<script>
	import axios from 'axios'
	import moment from 'moment'
	import { getHeader } from '@/config'
	import index from '@/mixins'
	function formatDate(str) {
		if(str != null) {
			return str.substring(8, 10)+'/'+str.substring(5, 7)+'/'+str.substring(0, 4)
		}
		return null
	}

	export default {
		middleware: 'notAuthenticated',
		mixins: [index],
		data() {
			return {
				title: 'Lịch sử đặt món',
				headers: [
				{
					text: 'STT',
					align: 'left',
					sortable: false,
					value: 'name'
				},
				{ text: 'Mã đặt hàng', sortable: false, },
				{ text: 'Nơi đặt', sortable: false },
				{ text: 'Ngày đặt', sortable: false },
				{ text: 'CSKH/ Giao hàng', sortable: false },
				{ text: 'Tổng tiền', sortable: false },
				{ text: 'Trạng thái', sortable: false },
				{ text: 'Chi tiết', sortable:false }
				],
				editedItem: {
					fromDate: new Date(moment().subtract(7, 'days')).toISOString().substr(0, 10),
					toDate: new Date().toISOString().substr(0, 10),
					statusId: 0
				},
				status: [
				{ id: 0, name: 'Tất cả'},
				{ id: 5, name: 'Thành công'},
				{ id: 6, name: 'Hủy'}
				],
				orders: [],
				order: null,
				fromModal:false,
				fromDateString: formatDate(new Date(moment().subtract(7, 'days')).toISOString().substr(0, 10)),
				toModal:false,
				toDateString: formatDate(new Date().toISOString().substr(0, 10)),
				loading:false,
				dialog: false,
				matrix: {
					show:false,
					start: '',
					end: '',
					duration: '0 phút',
					distance: '0 Km'
				},
				tab: 0,
				headerDetails: [
				{ text: 'Món', sortable: false, },
				{ text: 'Số lượng', sortable: false, },
				{ text: 'Giá', sortable: false, },
				{ text: 'Tổng', sortable:false}
				],
				cancelDialog: false,
				reasons: [
				{note: 'Nhập sai số điện thoại'},
				{note: 'Nhập sai thông tin liên hệ'},
				{note: 'Đơn hàng chậm trễ'},
				],
				cancelData: {}, 
				notes: []
			}
		},
		methods: {
		//GET ORDER LIST BY FILTER
		getOrders: async function() {
			var data     = this.editedItem
			this.loading = await !this.loading
			await axios.post('/api/Dofuu/OrderByFilter', data, {headers: getHeader(), withCredentials:true}).then(response => {
				if(response.status === 200) {
					this.orders = response.data.data
				}
			})
			setTimeout(() => {
				this.loading = !this.loading
			}, 500)

			setTimeout(() => {
				this.getOrders()
			}, 300000)
		},
		//GET ORDER DETAILS
		getDetails: async function(item) {
			const data = {orderId: item.id}
			this.dialog = await !this.dialog
			await axios.post('/api/Dofuu/Order/GetOrderDetail', data, {headers: getHeader(), withCredentials:true}).then(response => {
				if(response.status === 200) {
					this.order = response.data.data
				}
			})
		},
		//SHOW CANCEL DIALOG
		showCancelDialog: async function(item) {
			this.cancelDialog= true
			this.cancelData  =  item
		},
		//ACCEPT CANCEL
		cancelOrder: async function() {
			const data = Object.assign({}, this.cancelData)
			Object.assign(data, {orderNotes: this.notes})
			axios.post('/api/Dofuu/Order/CancelByCustomer', data, {headers: getHeader(), withCredentials:true}).then(response => {
				if(response.status == 200) {
					const res   = response.data.data
					const index = this.orders.findIndex((item) => {
						return item.id === res.id
					})
					Object.assign(this.orders[index], res)
					this.cancelDialog = false
				}
			})
		},
		disableCancelOrder: function(status) {
			const _s = new String(status).toLowerCase();

			switch(_s) {
				case 'chờ xử lý' : 
				return true
				break;
				case 'đang xử lý':
				return true
				break;
				case 'xác nhận'  :
				return false
				break;
				case 'đang giao' :
				return false
				break;
				case 'thành công':
				return false
				break;
				case 'hủy'       : 
				return false
				break;
			}
		},
		//CHANGE TAB DETAILS
		changeTab(index) {
			this.tab = index
		},
		//DIRECTION GOOGLE MAP
		calculateRoute() {
			var str = ''
			var vm  = this
			var map = new google.maps.Map(document.getElementById('map'), {
				zoom  : 17,
				center: {lat:vm.order.store.lat, lng:vm.order.store.lng}
			});
			var directionsService = new google.maps.DirectionsService;
			var directionsDisplay = new google.maps.DirectionsRenderer;

			directionsDisplay.setMap(map)

			var start      = {lat: vm.order.store.lat, lng: vm.order.store.lng}
			var end        = {lat: vm.order.lat, lng: vm.order.lng}

			var request    = {
				origin     : start,
				destination: end,
				travelMode : 'DRIVING'
			}
			directionsService.route(request, function(response, status) {
				if(status === 'OK') {
					var response = response
					directionsDisplay.setDirections(response);
					var service  = new google.maps.DistanceMatrixService()
					service.getDistanceMatrix(
					{
						origins: [response.request.origin.location],
						destinations: [response.request.destination.location],
						travelMode: 'DRIVING'
					}, function(res, status) {
						if (status === 'OK') {
							var originList     = res.originAddresses
							var destinationList= res.destinationAddresses
							var distance       = '0 Km'
							var duration       = '0 phút'
							var leg            = response.routes[ 0 ].legs[ 0 ];
							directionsDisplay.setDirections(response);
							for (var i = 0; i < originList.length; i++) {
								var results = res.rows[i].elements
								for (var j = 0; j < results.length; j++) {
									var element = results[j]
									distance    = element.distance.text
									duration    = element.duration.text
								}

								vm.matrix.distance = distance
								vm.matrix.duration = duration
								vm.matrix.start    = leg.start_address
								vm.matrix.end      = leg.end_address
								vm.matrix.show     = true
								directionsDisplay.setDirections(response);
							}
						}					
					})
				}				
			})
		},
		//CHOOSE STATUS ORDER
		orderStatus: function(status) {
			const _s = new String(status).toLowerCase();
			switch(_s) {
				case 'chờ xử lý': 
				return 1
				break;
				case 'đang xử lý':
				return 2
				break;
				case 'xác nhận':
				return 3 
				break;
				case 'đang giao':
				return 4
				break;
				case 'thành công':
				return 5
				break;
				case 'hủy': 
				return 6
				break;
			}
		},
	},
	watch: {
		'editedItem.fromDate': function(val, oldVal) {
			this.fromDateString = formatDate(val)
		},
		'editedItem.toDate': function(val, oldVal) {
			this.toDateString = formatDate(val)
		},
		'dialog': function(val, oldVal) {
			var vm = this
			if(!val) {				
				this.tab = 0
				vm.matrix = {
					show: false,
					start: '',
					end: '',
					duration: '0 phút',
					distance: '0 Km'
				}
			}
		},
		'tab': async function(val, oldVal) {
			if(val == 1) {
				if(!this.matrix.show) {
					setTimeout(async () => {					
						await this.calculateRoute()
					}, 500)
				}				
			}
		},
		'cancelDialog': function(val, oldVal) {
			if(!val) {
				this.notes = []
				this.cancelData = {}
			}
		}
	},
	mounted() {
		this.getOrders()
	}
}	

</script>

<style scoped>
#map {
	width: 100%;
	height: 250px;
}
</style>
