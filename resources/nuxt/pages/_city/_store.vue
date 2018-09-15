<template>
	<div ref="target_store" fluid class="mt-4" v-scroll="onScroll" v-show="!loading">
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
		<v-content v-show="!loading">
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
					<v-tab nuxt exact :to="{name: 'city-store-comment', params: {city: $route.params.city, store: $route.params.store}}">
						<v-badge color="blue">
							<span slot="badge" v-if="store.totalComment > 0">{{store.totalComment}}</span>
							<span>Bình luận</span>			 
						</v-badge>						
					</v-tab>
					<!-- <v-tab nuxt exact :to="{name: 'city-store-about', params: {city: $route.params.city, store: $route.params.store}}">
						Giới thiệu
					</v-tab> -->
				</v-tabs>
				<v-spacer></v-spacer>
				<v-btn icon @click.native="$store.commit('SHOW_CART')" v-if="$vuetify.breakpoint.smAndDown">
					<v-badge color="red" overlap>
						<span slot="badge" v-if="countItems>0">{{countItems}}</span>
						<v-icon color="blue">shopping_cart</v-icon>
					</v-badge>					
				</v-btn>
			</v-toolbar>
			<v-layout row wrap v-if="store != null">
				<v-flex xs12 md3>
					<v-container>
						<v-card color="white" tile class="card-radius">
							<v-system-bar status color="red darken-3">
								<div class="white--text">
									<v-icon color="white">alarm</v-icon>
									<span v-for="(item, i) in store.activities" v-if="i==0"> 
										<span v-for="(time, index) in item.times">
											{{time.from}} - {{time.to}} 
										</span>	
									</span>
								</div>
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
									<v-avatar slot="activator" size="22" color="white" >
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
									<v-avatar slot="activator" size="22" color="white" >
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
						<v-card-actions>
							<v-tooltip top>
								<v-btn slot="activator" @click.prevent="toggle('like')" flat :ripple="false">
									<v-icon left color="pink">{{like ? 'favorite' : 'favorite_border'}}</v-icon>
									{{store.likes}}
								</v-btn>
								<span>{{like ? 'Bỏ thích' : 'Thích'}}</span>
							</v-tooltip>
							
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

							<v-spacer></v-spacer>

							<v-tooltip top>
								<v-btn slot="activator" icon @click.prevent="toggle('favorite')" :ripple="false">
									<v-icon color="blue">{{favorite ? 'bookmark' : 'bookmark_border'}}</v-icon>
								</v-btn>
								<span>{{favorite ? 'Bỏ lưu' : 'Lưu'}}</span>
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

						</v-card-actions>
					</v-card>
				</v-container>	
			</v-flex>
			<v-flex xs12 md9 >
				<nuxt-child :key="$route.params.store" :store.sync="store"/>
			</v-flex>		
		</v-layout>			
		<v-snackbar v-model="snackbar" :timeout="3000" bottom >
			{{ snackbarMessage }}
			<v-btn
			color="pink"
			flat
			@click="snackbar = false"
			>
			Đóng
		</v-btn>
	</v-snackbar>
</v-content>
</div>
</template>

<script>
	import moment from 'moment'
	import Cookies from 'js-cookie'
	import axios from 'axios'
	import { getStoreURL, getHeader } from '@/config'
	import index from '@/mixins/index'
	import { mapState } from 'vuex'
	export default {
		mixins: [index],
		asyncData({params}) {
			return {
				city: {},
				activeSearch: false,
				offsetTab: 0,
				likePopup: false,
				favoritePopup: false,
				snackbar: false,
				snackbarMessage: null,
				favorite: false,
				processFavorite: false,
				like: false,
				processLike: false
			}
		},
		methods: {
			getStore() {
				var vm       = this
				const params = {cityId: this.$route.params.city, storeId: this.$route.params.store}
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
			likeStore() {
				var vm       = this
				var data     = {}
				const params = { name: 'likeEndpoint' }
				if(!vm.processLike) {
					vm.processLike = true

					setTimeout(() => {

						axios.post('/api/LikeStore/'+vm.store.id+'/Toggle', data, { params, withCredentials:true }).then(response => {
							if(response.status === 200) {			
								vm.like = ! vm.like
								vm.$store.commit('GET_STORE', response.data.store)
								if(response.data.type === 'success') {
									vm.snackbar        = true
									vm.snackbarMessage = 'Đã thích cửa hàng này.'
								}
							}
						}).finally(() => {
							vm.processLike = false
						})

					}, 300)
					
				}
				
			},	
			toggle(name) {

				var btnName = new String(name).toLowerCase()

				switch(btnName) {

					case 'like':
					if(this.isAuth && this.currentUser != null) {
						this.likePopup = false
						this.likeStore()
					} else {
						this.likePopup = true
					}
					return
					break

					case 'favorite':
					if(this.isAuth && this.currentUser != null) {
						this.favoritePopup = false
						this.favoriteStore()
					} else {
						this.favoritePopup = true
					}
					return
					break
				}
			},	
			//CHECK LIKE BY USER
			checkLike() {
				var vm = this
				var data = {}
				const params = {name: 'likeEndpoint'}

				return axios.post('/api/LikeStore/'+vm.store.id+'/Check', data, { params, withCredentials:true }).then(response => {
					if(response.data.type === 'success') {						
						vm.like = ! vm.like
					}
				})
			},
			//CLICK FAVORITE
			favoriteStore() {
				var vm   = this
				var data = {}
				const params = {name: 'favoriteEndpoint'}

				if(!vm.processFavorite) {

					vm.processFavorite = true

					setTimeout(() => {

						axios.post('/api/FavoriteStore/'+vm.store.id+'/Toggle', data, { params, withCredentials:true }).then(response => {
							if(response.status === 200) {
								vm.favorite = !vm.favorite
								if(response.data.type === 'success') {
									vm.snackbar        = true
									vm.snackbarMessage = response.data.message
								}
							}
						}).finally(() => {
							vm.processFavorite = false
						})

					}, 300)
					
				}
				
			},
			// CHECK FAVORITE BY USER
			checkFavorite() {
				var vm = this
				var data = {}
				const params = {name: 'favoriteEndpoint'}

				return axios.post('/api/FavoriteStore/'+vm.store.id+'/Check', data, { params, headers: getHeader(), withCredentials:true }).then(response => {
					if(response.data.type === 'success') {						
						vm.favorite = ! vm.favorite
					}
				})
			},
			onScroll (e) {
				this.$store.commit('ON_SCROLL', window.scrollY)
			}
		},
		computed: {
			...mapState({
				loading: state     => state.storeStore.loading,
				offsetTop: state   => state.offsetTop,
				store: state       => state.storeStore.store,
				currentUser: state => state.authStore.currentUser
			}),
		//COUNT ITEM IN CART
		countItems() {
			return this.$store.getters.counts
		},
		isAuth() {
			return this.$store.getters.isAuth
		}
	},
	created() {
		this.getStore().then(response => {
			if(response.status === 200) {
				if(this.isAuth) {
					axios.all([this.checkLike(), this.checkFavorite()])
				}				
				this.$store.dispatch('getToCart', response.data.store.id)	
			}
		})	

	},
	mounted() {
		this.offsetTab = this.$refs.target_store.offsetTop
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