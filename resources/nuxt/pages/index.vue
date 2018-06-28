<template>
	<v-container grey-lighten-4 lighten-2  grid-list-xs="$vuetify.breakpoint.smAndDown" grid-list-md="$vuetify.breakpoint.mdAndUp" v-scroll="onScroll">
		<v-layout grey-lighten-4 row wrap v-if="!loading">
			<v-flex xs12>
				<v-layout column> 
					
					<v-flex xs12>
						<v-flex xs12>
							<!-- <v-carousel hide-controls :style="$vuetify.breakpoint.xsAndDown ? `max-height:120px` : `max-height: 315px`">
								<v-carousel-item transition="fade" reverse-transition="fade" src="img/Banner1.png">
									<v-jumbotron dark>
										<v-container fill-height>
											<v-layout align-center>
												<v-flex>
													a
												</v-flex>
											</v-layout>
										</v-container>
									</v-jumbotron>
								</v-carousel-item>
								<v-carousel-item transition="fade" reverse-transition="fade">
									<v-card-media
									height="315px"
									src="img/Banner2.png"
									class="pa-0 ma-0">								
								</v-card-media>
							</v-carousel-item>
							<v-carousel-item transition="fade"	reverse-transition="fade">
								<v-card-media
								height="315px"
								src="img/Banner3.png"
								class="pa-0 ma-0">								
							</v-card-media>
						</v-carousel-item>
					</v-carousel>	 -->
				</v-flex>
			</v-flex>

			<v-flex xs12 v-if="deal.stores.length>0">
				<!-- START DEAL STORES -->
				<v-card flat class="pb-2" >					
					<v-layout grey lighten-4 row wrap class="elevation-1" >

						<v-toolbar color="white" flat dense  extension-height="30px">
							<v-icon color="red">whatshot</v-icon>
							<v-toolbar-title class="red--text text--accent-3">
								HOT DEALS 
							</v-toolbar-title>									
							
							<v-toolbar-items slot="extension">
								<!-- START DISTRICT IN DEAL STORE -->
								<v-menu left bottom offset-y :open-on-hover="$vuetify.breakpoint.mdAndUp">
									<v-btn slot="activator" flat>Khu vực<v-icon right>arrow_drop_down</v-icon></v-btn>
									<v-list dense>
										<v-list-tile @click="loadDistrict(0, 'deal')" :class="{'red--text text--accent-2 bold' : deal.list_flag == 0}" >
											<v-list-tile-action>
												<v-icon color="red">whatshot</v-icon>
											</v-list-tile-action>
											<v-list-tile-content>
												<v-list-tile-title>Tất cả</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{deal.count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>

										<v-list-tile  v-for="(item, i) in deal.districts" :key="i" @click="loadDistrict(item.id, 'deal')" :class="{'red--text text--accent-2': item.id == deal.list_flag}" >
											<v-list-tile-action>
												<v-icon color="red">whatshot</v-icon>
											</v-list-tile-action>
											<v-list-tile-content>
												<v-list-tile-title>{{item.district_name}}</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{item.stores_count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>

									</v-list>
								</v-menu> <!-- END DISTRICT IN ALL STORE -->
								<!-- START TYPE IN ALL STORE -->
								<v-menu left bottom offset-y :open-on-hover="$vuetify.breakpoint.mdAndUp">
									<v-btn slot="activator" flat>Danh mục<v-icon right>arrow_drop_down</v-icon></v-btn>
									<v-list dense>
										<v-list-tile @click="loadType(0, 'deal')" :class="{'red--text text--accent-2' : deal.list_flag == 0}" >
											<v-list-tile-action>
												<v-icon color="red">whatshot</v-icon>
											</v-list-tile-action>
											<v-list-tile-content>
												<v-list-tile-title>Tất cả</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{deal.count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>

										<v-list-tile  v-for="(item, i) in deal.types" :key="i" @click="loadType(item.id, 'deal')" :class="{'red--text text--accent-2': item.id == deal.list_flag}">
											<v-list-tile-action>
												<v-icon color="red">whatshot</v-icon>
											</v-list-tile-action>
											<v-list-tile-content>
												<v-list-tile-title>{{ item.type_name }}</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{item.stores_count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>
									</v-list>
								</v-menu><!-- END TYPE IN ALL STORE -->
							</v-toolbar-items>
							
						</v-toolbar>					

						<v-flex xs12>
							<v-content>
								<v-layout row wrap >
									<v-flex  xs12 md3 d-flex v-for="(item, i) in deal.stores " :key="i">

										<v-card nuxt :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}"  hover ripple >
											<v-system-bar status color="yellow accent-4">
												<v-icon left>access_time</v-icon>
												<span v-for="(item, i) in item.activities" v-if="i==0">		
													<span v-for="(time, i) in item.times">
														{{time.from}} - {{time.to}} 
													</span>	
												</span>
											</v-system-bar>
											<v-card-media class="white--text" :height="$vuetify.breakpoint.mdAndUp ? '150px' : '250px' " :src="image(item.avatar)">
												<v-container fill-height fluid>
													<v-layout fill-height >
														<v-flex xs12>
															<v-tooltip top>
																<v-icon slot="activator" :color="item.color" v-if="status(item.status) == 1">
																	sentiment_very_satisfied
																</v-icon>
																<span>{{item.status}}</span>
															</v-tooltip>
															<v-tooltip top>
																<v-icon slot="activator" :color="item.color" v-if="status(item.status) == 2">
																	sentiment_neutral
																</v-icon>
																<span>{{item.status}}</span>
															</v-tooltip>
														</v-flex>
													</v-layout>
												</v-container>
											</v-card-media>

											<v-card-actions>
												<v-icon color="red">whatshot</v-icon>
												<div class="red--text"><strong><i>{{item.coupon.title}}</i></strong></div>
											</v-card-actions>
											<v-divider light></v-divider>
											<v-card-text >
												<v-tooltip top>												
													<div slot="activator" style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap"><strong >{{item.name}}</strong>
													</div>
													<span>{{item.name}}</span>
												</v-tooltip>
												<v-tooltip top>												
													<div slot="activator" style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap">{{item.address}}</div>
													<span>{{item.address}}</span>
												</v-tooltip>
											</v-card-text>
										</v-card>
									</v-flex>
								</v-layout>
								<!-- PAGINATION -->
								<div class="text-xs-center" v-if="deal.pagination.last_page>1">
									<v-pagination :length="deal.pagination.last_page" v-model="deal.pagination.current_page" @input="changePage(deal.pagination.current_page, 'deal')" circle></v-pagination>
								</div>
							</v-content>
						</v-flex>
					</v-layout> 
				</v-card><!-- END DEAL STORE -->

			</v-flex>

			<v-flex  xs12 md12 >
				<v-card flat ><!-- START ALL STORE -->					
					<v-layout grey lighten-4 fill-height row wrap class="elevation-1">
						<v-toolbar color="white" flat dense  extension-height="30px" class="mb-1">
							<v-toolbar-title class="red--text text--accent-3" id="all-store">
								<h5>TẤT CẢ</h5>
							</v-toolbar-title>
							<!-- START DISTRICT IN ALL STORE -->
							<v-toolbar-items slot="extension">
								<v-menu left bottom offset-y :open-on-hover="$vuetify.breakpoint.mdAndUp">
									<v-btn slot="activator" flat> <h5>Khu vực</h5> <v-icon right>arrow_drop_down</v-icon></v-btn>
									<v-list dense>
										<v-list-tile @click="loadDistrict(0, 'all')" :class="{'red--text text--accent-2' : all.list_flag == 0}">
											<v-list-tile-content>
												<v-list-tile-title>Tất cả</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{all.count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>
										<v-list-tile  v-for="(item, i) in all.districts" :key="i" @click="loadDistrict(item.id, 'all')" :class="{'red--text text--accent-2': item.id == all.list_flag}" >
											<v-list-tile-content>
												<v-list-tile-title>{{item.district_name}}</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{item.stores_count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>
									</v-list>
								</v-menu> <!-- END DISTRICT IN ALL STORE -->
								<!-- START TYPE IN ALL STORE -->
								<v-menu left bottom offset-y :open-on-hover="$vuetify.breakpoint.mdAndUp">
									<v-btn slot="activator" flat><h5>Danh mục</h5><v-icon right>arrow_drop_down</v-icon></v-btn>
									<v-list dense>
										<v-list-tile @click="loadType(0, 'all')" :class="{'red--text text--accent-2' : all.list_flag == 0}" >
											<v-list-tile-content>
												<v-list-tile-title>Tất cả</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{all.count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>

										<v-list-tile  v-for="(item, i) in all.types" :key="i" @click="loadType(item.id, 'all')" :class="{'red--text text--accent-2': item.id == all.list_flag}">
											<v-list-tile-content>
												<v-list-tile-title>{{ item.type_name }}</v-list-tile-title>
											</v-list-tile-content>
											<v-list-tile-action>
												<v-avatar color="red accent-3" size="20">
													<span class="white--text">{{item.stores_count}}</span>
												</v-avatar>
											</v-list-tile-action>
										</v-list-tile>
									</v-list>
								</v-menu><!-- END TYPE IN ALL STORE -->
							</v-toolbar-items>							
						</v-toolbar>					

						<v-flex xs12 order-xs1>
							<v-content class="pb-0">
								<!-- STORE LIST -->
								<vue-store-list v-if="currentCity != null && $vuetify.breakpoint.smAndDown" :stores.sync="all.stores" :currentCity.sync="currentCity"></vue-store-list>
								<!-- STORE GRID -->
								<vue-store-grid v-if="currentCity != null && $vuetify.breakpoint.mdAndUp" :stores.sync="all.stores" :currentCity.sync="currentCity"></vue-store-grid>
								
								<div class="text-xs-center" v-if="all.pagination.last_page>1">
									<v-pagination :length="all.pagination.last_page" v-model="all.pagination.current_page" @input="changePage(all.pagination.current_page, 'all')" circle></v-pagination>
								</div>
								
								<v-card-actions>
									<v-btn v-if="currentCity != null && all.stores.length > 0" color="grey lighten-2" block :to="{name: 'city-tat-ca-dia-diem', params: {city: currentCity.slug }}" round small >Xem thêm <v-icon right>arrow_forward</v-icon> </v-btn>
								</v-card-actions>
							</v-content>
						</v-flex>								
					</v-layout>
				</v-card><!-- END ALL STORE -->

			</v-flex>
		</v-layout>
	</v-flex> 

</v-layout>

<v-btn v-if="!endPage" class="hidden-md-and-up" transition="scale-transition" right bottom fixed fab dark small color="primary"  @click="scrollDown">
	<v-icon dark>expand_more</v-icon>
</v-btn>
<v-btn v-if="endPage" class="hidden-md-and-up" transition="scale-transition" right bottom fixed fab dark small color="primary"  @click="scrollTop">
	<v-icon dark>expand_less</v-icon>
</v-btn>
</v-container>
</template>

<script>
import axios from 'axios'
import index from "@/mixins/index.js";
import StoreList from '@/components/StoreList'
import StoreGrid from '@/components/StoreGrid'
import {getCityHasDealURL, getStoreHasDealURL} from '@/config.js'
import { mapState } from "vuex";
import Cookies from "js-cookie";
export default {
	mixins: [index],
	middleware: ["home"],
	components: {
		'vue-store-list': StoreList,
		'vue-store-grid': StoreGrid
	},
	// Async Data
	async asyncData({ store }) {
		return {
			slide: 0,
			sliding: null,
			deal: {
				tabs: 0,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 8,
				count:0,
			},
			all: {
				tabs: 0,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 8,
				count: 0
			},
			new: {
				tabs: 0,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 8,
				count: 0
			},
			loading: true,
			endPage: false
		}
	},
	methods: {
		onSlideStart (slide) {
			this.sliding = true
		},
		onSlideEnd (slide) {
			this.sliding = false
		},
		//GET DISTRICT AND TYPE BY CITY ID
		getCity: function(id) {
			var vm = this
			return new Promise((resolve, reject) => {
				axios.get('/api/GetCityInformation'+'/'+id, {withCredentials:true}).then(response => {
					if(response.status == 200) {
						vm.all.districts = response.data.districts
						vm.all.types     = response.data.types
						let count          = 0
						vm.all.districts.forEach(item => {
							count = count + parseFloat(item.stores_count)					
							return count
						})
						vm.all.count = count
					}
					resolve(response)
				})
			})			
		},
		//FETCH STORE
		fetchStore: function(data) {
			var params = {_DID: data.did, _TID: data.tid, _s: this.deal.pageSize, page: data.page}

			axios.get('/api/LoadStore', {params, withCredentials:true}).then(response => {
				if(response.status == 200) {
					this.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
					this.all.stores     = response.data.data
					this.all.pagination = response.data.pagination
				}
			})
		},
		//FETCH DISTRICT AND TYPE HAS DEAL BY CITY ID
		getCityHasDeal: function(id) {
			axios.get('/api/GetCityInformationHasDeal'+'/'+id, {withCredentials: true}).then(response => {
				if(response.status == 200) {
					// this.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
					// this.$store.commit('UPDATE_CITY', response.data.city)
					this.deal.districts = response.data.districts
					this.deal.types     = response.data.types
					let count = 0
					this.deal.districts.forEach(item => {
						count = count + parseFloat(item.stores_count)					
						return count
					})
					this.deal.count = count
				}
			})
		},
		//FETCH STORE HAS DEAL BY DISTRICT OR TYPE
		fetchStoreWithDeal: function(data) {
			var params = {_DID: data.did, _TID: data.tid, _s: this.deal.pageSize, page: data.page}
			axios.get('/api/LoadStoreHasDeal', {params, withCredentials:true}).then(response => {
				if(response.status == 200) {
					this.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
					this.deal.stores     = response.data.data
					this.deal.pagination = response.data.pagination
				}
			})
		},
		//LOAD STORES IN DISTRICT
		loadDistrict: function(id, type) {
			const _t = new String(type).toLowerCase();
			switch(_t) {
				case 'all':
				this.all.tabs = 0
				if(this.all.list_flag != id) {
					this.all.list_flag = id
					const query = {did:id, tid:0, page:0}
					this.fetchStore(query)
				}
				break;

				case 'deal':
				this.deal.tabs = 1
				if(this.deal.list_flag != id) {
					this.deal.list_flag = id
					const query = {did:id, tid:0, page:0}
					this.fetchStoreWithDeal(query)
				}
				break;

				case 'new':

				break;
			}
		},
		//LOAD STORES IN TYPE
		loadType: function(id, type) {
			const _t = new String(type).toLowerCase();
			switch(_t) {
				case 'all':
				this.all.tabs = 1
				if(this.all.list_flag != id) {
					this.all.list_flag = id
					const query = {did:0, tid:id, page:0}
					this.fetchStore(query)
				}
				break;

				case 'deal':
				this.deal.tabs = 1
				if(this.deal.list_flag != id) {
					this.deal.list_flag = id
					const query = {did:0, tid:id, page:0}
					this.fetchStoreWithDeal(query)
				}
				break;

				case 'new':

				break;
			}
		},
		//CHANGE PAGE
		changePage: function(page, type) {
			const _t = new String(type).toLowerCase();
			switch (_t) {
				case 'all': 
				if (this.all.tabs == 0) {
					this.$vuetify.goTo('#all-store', {duration: 300,  easing: 'easeInOutCubic', offset: 0})
					const query = {did: this.all.list_flag, tid: 0, page: page}	
					this.fetchStore(query)
				} else if (this.all.tabs == 1) {
					const query = {did: 0, tid: this.all.list_flag, page: page}
					this.fetchStore(query)
				}
				break;

				case 'deal':
				if (this.deal.tabs == 0) {
					const query = {did: this.deal.list_flag, tid: 0, page: page}	
					this.fetchStoreWithDeal(query)
				} else if (this.deal.tabs == 1) {
					const query = {did: 0, tid: this.deal.list_flag, page: page}
					this.fetchStoreWithDeal(query)
				}
				break;

				case 'new':

				break;
			}
			
		},
		scrollDown: function() {
			window.scrollBy(0, 100);
			this.height       = document.body.scrollHeight
			this.scrollY      = window.scrollY
			this.clientHeight = document.documentElement.clientHeight
			if(this.scrollY == this.height - this.clientHeight) {
				this.endPage = true
			}
		},
		scrollTop: function() {
			document.body.scrollTop = 0; // For Safari
    		document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    		this.endPage = false
    	},
    	onScroll: function(e) {
    		if(window.scrollY == document.body.scrollHeight - document.documentElement.clientHeight) {
    			this.endPage = true
    		} else {
    			this.endPage = false
    		}
    	}
    },
    computed: {
    	...mapState({
    		currentCity: state => state.cityStore.currentCity
    	}),
    	city: function() {
    		return this.$store.getters.getCityByID(this.currentCity)
    	}
    },
    created: async function() {
    	const query = {did:0, tid:0, page:0}
    	if(Cookies.get('flag_c') != null || typeof Cookies.get('flag_c') != 'undefined') {
    		setTimeout(async () => {
    			await this.getCity(Cookies.get('flag_c')).then(response => {
    				this.fetchStoreWithDeal(query)
    				this.fetchStore(query)		
    			})
    			await this.getCityHasDeal(Cookies.get('flag_c'))
    			
    		},100)

    		this.loading = false
    	} else {
    		setTimeout(async () => {
    			await this.getCity(10001).then(response => {
    				this.fetchStoreWithDeal(query)
    				this.fetchStore(query)
    			})
    			await this.getCityHasDeal(10001)
    		}, 100)

    		this.loading = false
    	}
    	
    },
};
</script>

<style>

#section04 a {
	padding-top: 60px;
}
#section04 a span {
	position: absolute;
	top: 0;
	left: 50%;
	width: 18px;
	height: 18px;
	margin-left: -9px;
	border-left: 1px solid #fff;
	border-bottom: 1px solid #fff;
	-webkit-transform: rotate(-45deg);
	transform: rotate(-45deg);
	-webkit-animation: sdb04 2s infinite;
	animation: sdb04 2s infinite;
	box-sizing: border-box;
}
@-webkit-keyframes sdb04 {
	0% {
		-webkit-transform: rotate(-45deg) translate(0, 0);
	}
	20% {
		-webkit-transform: rotate(-45deg) translate(-10px, 10px);
	}
	40% {
		-webkit-transform: rotate(-45deg) translate(0, 0);
	}
}
@keyframes sdb04 {
	0% {
		transform: rotate(-45deg) translate(0, 0);
	}
	20% {
		transform: rotate(-45deg) translate(-10px, 10px);
	}
	40% {
		transform: rotate(-45deg) translate(0, 0);
	}
}
</style>
