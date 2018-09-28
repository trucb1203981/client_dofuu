<template>
	<div ref="target_store" fluid class="mt-4" v-scroll="onScroll">
		
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

		<v-content v-if="!loading">

			<v-toolbar :fixed="offsetTop>offsetTab" color="white" class="elevation-0 scroll-y"  flat dense style="max-height: 400px" tabs  v-if="store != null"> 

				<v-btn flat :to="{path: '/'}" icon>
					<v-icon>arrow_back</v-icon>
				</v-btn>

				<v-toolbar-title class="red--text text-accent-3 hidden-sm-and-down" style="width: 300px">
					{{store.name}}
				</v-toolbar-title>

				<v-tabs show-arrows>
					<v-tabs-slider color="yellow"></v-tabs-slider>
					<v-tab nuxt exact :to="{name: 'city-store', params: {city: $route.params.city, store: $route.params.store}}">
						Thực Đơn
					</v-tab>
					<v-divider vertical inset></v-divider>
					<v-tab nuxt exact :to="{name: 'city-store-comments', params: {city: $route.params.city, store: $route.params.store}}">
						<v-badge color="blue">
							<span slot="badge" v-if="store.totalComment > 0" class="caption">{{store.totalComment}}</span>
							<span>Bình luận</span>			 
						</v-badge>						
					</v-tab>
					<!-- <v-tab nuxt exact :to="{name: 'city-store-about', params: {city: $route.params.city, store: $route.params.store}}">
						Giới thiệu
					</v-tab> -->
				</v-tabs>
				<v-spacer></v-spacer>
				<v-btn icon @click.native="$store.commit('SHOW_CART')" v-if="$vuetify.breakpoint.smAndDown && $route.name === 'city-store' ">
					<v-badge color="red" overlap>
						<span slot="badge" v-if="countItems>0">{{countItems}}</span>
						<v-icon color="blue">shopping_cart</v-icon>
					</v-badge>					
				</v-btn>
			</v-toolbar>

			<v-layout row wrap v-if="!!store">
				<v-flex xs12 sm12 md4 lg3 v-if="$route.name === 'city-store' || $vuetify.breakpoint.mdAndUp">
					<v-container :class="{'px-0': $vuetify.breakpoint.xsOnly}">
						<v-card color="white" tile class="card-radius" >
							<v-system-bar status color="red darken-3" dark>
								<v-icon>alarm</v-icon>
								<span>{{activityTime(store.activities)}}</span>
								<v-spacer></v-spacer>
								<span>{{store.type.name}}</span>
							</v-system-bar>
							<v-img :src="image(store.avatar)" height="200" :aspect-ratio="16/9" >
								<v-layout column class="media">
									<v-card-title>
										<v-spacer></v-spacer>
										<v-tooltip top v-if="store.verified">
											<v-avatar
											slot="activator"
											size="22">
											<v-icon color="green darken-3">verified_user</v-icon>
										</v-avatar>	
										<span>Chứng nhận hợp tác cùng Dofuu</span>
									</v-tooltip>									
								</v-card-title>
							</v-layout>			
						</v-img>
						<v-toolbar dense flat color="red darken-3" dark class="elevation-0">
							<v-subheader class="text-xs-center"><h4>{{store.name}}</h4></v-subheader>
						</v-toolbar>
						<v-list avatar dense three-line subheader>
							<v-list-tile>
								<v-list-tile-avatar>
									<v-avatar size="22" color="white" >
										<v-icon color="primary">place</v-icon>
									</v-avatar>	
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title><h4>Địa chỉ</h4></v-list-tile-title>
									<v-list-tile-sub-title>
										<h4>{{store.address}}</h4>
									</v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
						<v-divider></v-divider>
						<v-list  avatar dense>
							<v-list-tile>
								<v-list-tile-avatar>
									<v-avatar size="22" color="white" >
										<v-icon color="green darken-3">access_time</v-icon>
									</v-avatar>	
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title>
										<h4>Chuẩn bị</h4>
									</v-list-tile-title>
									<v-list-tile-sub-title>
										<h4>{{store.prepareTime}} phút</h4>
									</v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>

						</v-list>
						<v-divider></v-divider>
						<v-card flat>
							<v-list avatar dense>
								<v-list-tile avatar v-for="(rating, index) in ratings" :key="index">
									<v-list-tile-avatar>
										<v-avatar size="22" color="white" >
											<v-icon color="orange lighten-3">star</v-icon>
										</v-avatar>	
									</v-list-tile-avatar>
									<v-list-tile-content>
										{{rating.name}}
									</v-list-tile-content>
									<v-list-tile-action>
										<v-avatar size="28" color="green" >
											<span class="body-1 font-weight-bold white--text">{{rating.avg}}</span>
										</v-avatar>	
									</v-list-tile-action>
								</v-list-tile>
							</v-list>
							<v-card-actions>
								<v-btn v-if="store.ratedStore" small block flat color="red" @click.prevent="removeRating">Bỏ đánh giá</v-btn>
								<v-btn v-else small block flat color="green" @click.prevent="toggle('rating')">Đánh giá</v-btn>
							</v-card-actions>
						</v-card>
						<v-divider></v-divider>
						<v-system-bar status color="grey lighten-5" height="35" class="text-xs-center">
							<v-flex>
								<v-tooltip top>	
									<span  slot="activator">
										<v-icon size="20">visibility</v-icon> 	
										{{store.views}}
									</span>			
									<span>{{store.views}} lượt xem</span>
								</v-tooltip>
							</v-flex>
							<v-divider vertical inset class="mx-2"></v-divider>
							<v-flex >
								<v-tooltip top>	
									<span  slot="activator">
										<v-icon size="20">comment</v-icon> 	
										{{store.totalComment}}
									</span>	
									<span>{{store.totalComment}} lượt bình luận</span>
								</v-tooltip>
							</v-flex>							
							<v-divider vertical inset class="mx-2"></v-divider>

							<v-flex>
								<v-tooltip top>
									<v-icon slot="activator" color="pink" @click.prevent="toggle('like')" size="20">{{store.checkedLike ? 'favorite' : 'favorite_border'}}</v-icon>
									<span>{{store.checkedLike ? 'Bỏ thích' : 'Thích'}}</span>
								</v-tooltip>
								<span class="grey--text text--darken-1">{{store.likes}}</span>
							</v-flex>
							<v-divider vertical inset class="mx-2"></v-divider>
							<v-menu v-model="likePopup" offset-y >
								<div slot="activator"></div>
								<v-card>
									<v-card-text>
										<h4>Bạn thích cửa hàng này?</h4>
										<div>Đăng nhập để thể hiện ý kiến của bạn.</div>
									</v-card-text>
									<v-divider></v-divider>
									<v-card-actions>
										<v-btn v-if="!isAuth" nuxt :to="{path: '/login', query: {redirect: $route.path}}" color="blue" small dark round>
											<v-icon left>person</v-icon> ĐĂNG NHẬP
										</v-btn>
									</v-card-actions>
								</v-card>
							</v-menu>	

							<v-flex >
								<v-tooltip top>
									<v-icon slot="activator"  @click.prevent="toggle('favorite')" color="blue" size="20">{{store.checkedFavorite ? 'bookmark' : 'bookmark_border'}}</v-icon>
									<span>{{store.checkedFavorite ? 'Bỏ lưu' : 'Lưu'}}</span>
								</v-tooltip>	
								<v-menu v-model="favoritePopup" offset-y >
									<div slot="activator"></div>
									<v-card>
										<v-card-text>
											<h4>Bạn muốn thêm vào bộ sưu tập cửa hàng này?</h4>
											<div>Đăng nhập để thêm cửa hàng này vào bộ sưu tập.</div>
										</v-card-text>
										<v-divider></v-divider>
										<v-card-actions>
											<v-btn v-if="!isAuth" nuxt :to="{path: '/login', query: {redirect: $route.path}}" color="blue" small dark round>
												<v-icon left>person</v-icon> ĐĂNG NHẬP
											</v-btn>
										</v-card-actions>
									</v-card>
								</v-menu>		
							</v-flex>
						</v-system-bar>
					</v-card>
				</v-container>	
			</v-flex>
			<v-flex xs12 sm12 md8 lg9 >
				<nuxt-child :key="$route.params.store" :store.sync="store"/>
			</v-flex>		
		</v-layout>			
		<vue-snackbar ref="snackbar"/>
	</v-content>
	<v-dialog v-model="showRating" persistent scrollable max-width="500px" :hide-overlay="$vuetify.breakpoint.smAndDown"  :fullscreen="$vuetify.breakpoint.smAndDown">
		<v-card>
			<v-toolbar color="white" dense flat>
				<v-toolbar-title>Đánh giá cửa hàng</v-toolbar-title>
			</v-toolbar>
			<v-divider></v-divider>
			<v-card-actions v-for="(rating, i) in ratings" :key="i">
				<v-flex xs3 sm4 class="caption text-xs-right font-weight-bold">
					{{rating.name}}
				</v-flex>
				<v-flex xs9 sm8>
					<v-rating
					v-model="rating.value"
					hover
					background-color="orange lighten-3"
					color="orange"
					></v-rating>
				</v-flex>
			</v-card-actions>
			<v-divider></v-divider>
			<v-card-actions>
				<v-btn color="red darken-1" outline flat round small @click.native="showRating = false">Hủy</v-btn>
				<v-spacer></v-spacer>
				<v-btn color="blue" class="white--text" small :loading="processRating" @click.native="addRating" round>Gửi</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</div>
</template>

<script>
	import moment from 'moment'
	import Cookies from 'js-cookie'
	import axios from 'axios'
	import Snackbar from '@/components/commons/snackbar'
	import index from '@/mixins/index'
	import { mapState } from 'vuex'
	const endpoint = 'ratingEndpoint'
	export default {
		mixins: [index],
		components: {
			'vue-snackbar': Snackbar
		},
		asyncData({params}) {
			return {
				city: {},
				ratings: [],
				activeSearch: false,
				offsetTab: 160,
				likePopup: false,
				favoritePopup: false,
				processLike: false,
				processRating: false,
				showRating: false,
				editedRating: null
			}
		},
		methods: {
			getStore() {
				var vm          = this
				const citySlug  = vm.$route.params.city
				const storeSlug = vm.$route.params.store
				const params    = {citySlug: citySlug, storeSlug: storeSlug}

				return new Promise((resolve, reject) => {
					vm.$store.dispatch('getStore', params).then(response => {
						if(response.status == 200) {							
							vm.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
							vm.$store.commit('UPDATE_CITY', response.data.city)
						}	
						resolve(response)
					})
				})

			},
			toggle(name) {

				var btnName = new String(name).toLowerCase()

				switch(btnName) {

					case 'like':
					if(this.isAuth) {
						this.likePopup = false
						this.likeStore()
					} else {
						this.likePopup = true
					}
					return
					break

					case 'favorite':
					if(this.isAuth) {
						this.favoritePopup = false
						this.favoriteStore()
					} else {
						this.favoritePopup = true
					}
					return
					break

					case 'rating':
					if(this.isAuth) {						
						this.rating()
					} else {
						this.$router.push({name: 'login', query: {redirect: this.$route.path}})
					}
					return
					break
				}
			},	
			likeStore() {
				var vm        = this
				var data      = {}
				const storeId = vm.store.id
				const params  = { name: 'likeEndpoint' }
				if(!vm.processLike) {
					vm.processLike       = true
					vm.store.checkedLike = !vm.store.checkedLike
					setTimeout(() => {

						axios.post('/api/LikeStore/'+storeId+'/Toggle', data, { params, withCredentials:true }).then(response => {
							if(response.status === 200) {			
								vm.store.likes = response.data.likes					
								if(response.data.type === 'success') {
									vm.$refs.snackbar.open('Đã thích cửa hàng này')
								}
							}
						}).finally(() => {
							vm.processLike = false
						})

					}, 100)					
				}				
			},
			//CLICK FAVORITE
			favoriteStore() {
				var vm        = this
				const storeId = vm.store.id
				var data      = {}
				const params  = {name: 'favoriteEndpoint'}

				if(!vm.processFavorite) {
					vm.store.checkedFavorite = !vm.store.checkedFavorite
					vm.$store.dispatch('toggleFavoriteStore', storeId).then(response => {
						if(response.status === 200) {								
							if(response.data.type === 'success') {
								vm.$refs.snackbar.open('Đã lưu cửa hàng này')
							}
						}
					})					
				}
			},
			onScroll (e) {
				this.$store.commit('ON_SCROLL', window.scrollY)
			},
			rating() {
				this.showRating = true
				this.ratings.map(item => {
					item.value  = 4
					return item
				})
			},
			addRating() {
				var vm        = this
				var data      = {ratings: vm.ratings} 
				const storeId = vm.store.id
				const params  = { endpoint: endpoint}
				if(!vm.processRating) {
					vm.processRating = !vm.processRating
					setTimeout(() => {
						axios.post('/api/RatingStore/'+storeId+'/NewRating', data, {params, withCredentials: true }).then(response => {
							if(response.status === 200) {
								vm.store.ratedStore = true
								vm.ratings          = JSON.parse(JSON.stringify(response.data.ratings))
								vm.showRating       = false
							}
						}).finally(() => {
							vm.processRating = !vm.processRating
						})
					})
				}

			},
			fetchRating() {
				var vm        = this
				var data      = {}
				const storeId = vm.store.id
				const params  = { endpoint: endpoint }
				return axios.post('/api/RatingStore/'+storeId+'/FetchRating', data, { params, withCredentials: true}).then(response => {
					if(response.status === 200) {
						this.ratings = JSON.parse(JSON.stringify(response.data.ratings))
					}
				})
			},
			removeRating() {
				var vm        = this
				var data      = { ratedStore: vm.store.ratedStore }
				const storeId = vm.store.id
				const params  = { endpoint: endpoint }
				if(!vm.processRating) {
					vm.processRating = !vm.processRating
					setTimeout(() => {
						return axios.post('/api/RatingStore/'+storeId+'/RemoveRating', data, { params, withCredentials: true}).then(response => {
							if(response.status === 200) {
								vm.store.ratedStore = false
								vm.ratings          = JSON.parse(JSON.stringify(response.data.ratings))
							}
						}).finally(() => {
							vm.processRating = !vm.processRating
						})
					}, 100)
					
				}

			}
		},
		computed: {
			...mapState({
				loading: state         => state.storeStore.loading,
				offsetTop: state       => state.offsetTop,
				store: state           => state.storeStore.store,
				currentUser: state     => state.authStore.currentUser,
				myLocation: state      => state.myLocation,
				processFavorite: state => state.storeStore.processFavorite
			}),
		//COUNT ITEM IN CART
		countItems() {
			return this.$store.getters.counts
		},
		//CHECK AUTH
		isAuth() {
			return this.$store.getters.isAuth
		}
	},
	created() {
		this.getStore().then(response => {
			if(response.status === 200) {				
				this.fetchRating()
				this.$store.dispatch('getToCart', response.data.store.id)	
			}
		})	
	}
}
</script>

<style scoped>
.card-radius {
	/*border-radius: 15px 15px 0 0;*/
	border-radius: 15px;
}
#overlay {
	position: fixed; /* Sit on top of the page content */
	display: block; /* Hidden by default */
	width: 100%; /* Full width (cover the whole page) */
	height: 100%; /* Full height (cover the whole page) */
	top: 0; 
	left: 0;
	right: 0;
	bottom: 0;
	background-color: rgba(0,0,0,0.5); /* Black background with opacity */
	z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
}
</style>