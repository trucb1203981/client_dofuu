<template>
	<div ref="target_store" fluid class="mt-4" v-scroll="onScroll" v-show="!loading">
		<v-dialog v-model="loading" hide-overlay persistent width="300">
			<v-card	color="red darken-3"	dark>
				<v-card-text>
					Xin vui lòng chờ giây lát...
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
						<v-card flat tile color="transparent">
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
							<v-card-media :src="image(store.avatar)" height="200">
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
						</v-card-media>
						<v-toolbar dense flat color="red darken-3" dark class="elevation-0">
							<v-subheader class="text-xs-center"><h4>{{store.name}}</h4></v-subheader>
						</v-toolbar>
					</v-card>
					<v-list avatar dense three-line subheader>
						<v-list-tile>
							<v-list-tile-avatar>
								<v-avatar
								slot="activator"
								size="22"
								color="white"
								>
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
							<v-avatar
							slot="activator"
							size="22"
							color="white"
							>
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
		</v-container>	
	</v-flex>
	<v-flex xs12 md9 d-flex>
		<nuxt-child :key="$route.params.store" :store.sync="store"/>
	</v-flex>		
</v-layout>			
</v-content>
</div>
</template>

<script>
import moment from 'moment'
import Cookies from 'js-cookie'
import axios from 'axios'
import {getStoreURL, getHeader} from '@/config'
import index from '@/mixins/index'
import {mapState} from 'vuex'
export default {
	mixins: [index],
	asyncData({params}) {
		return {
			city: {},
			activeSearch: false,
			offsetTab: 0,
		}
	},
	methods: {
		getStore() {
			const params = {_CID: this.$route.params.city, _SID: this.$route.params.store}
			this.$store.dispatch('getStore', params).then(response => {
				if(response.status == 200) {
					this.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
					this.$store.commit('UPDATE_CITY', response.data.city)
				}
			})
		},
		onScroll (e) {
			this.$store.commit('ON_SCROLL', window.scrollY)
		}
	},
	computed: {
		...mapState({
			loading: state   => state.storeStore.loading,
			offsetTop: state => state.offsetTop,
			store: state     => state.storeStore.store
		}),
		//COUNT ITEM IN CART
		countItems() {
			return this.$store.getters.counts
		},
	},
	created() {
		this.getStore()
	},
	mounted() {
		this.offsetTab = this.$refs.target_store.offsetTop
	}
}
</script>

<style scoped>
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