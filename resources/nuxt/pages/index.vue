<template>
	<v-container grey-lighten-4 lighten-2 fluid grid-list-lg v-scroll="onScroll">
		<v-layout grey-lighten-4 row wrap v-if="!loading">
			<v-flex xs12 sm6 md9>
				<v-layout column> 
					<v-flex xs12 sm12 md12  v-if="deal.stores.length>0">
						<!-- START DEAL STORES -->
						<v-card flat class="pb-2" >					
							<v-layout grey lighten-4 row wrap class="elevation-1" >

								<v-toolbar color="white" flat dense>
									<v-toolbar-title class="red--text text--accent-2">
										HOT DEALS 
									</v-toolbar-title>
									<v-icon color="red">whatshot</v-icon>
								</v-toolbar>					
								<v-flex xs12 sm6 md3 :class="{'d-flex': $vuetify.breakpoint.mdAndUp, 'order-xs2': $vuetify.breakpoint.smAndDown}" class="pa-0">
									<v-card flat tile>
										<v-tabs v-model="deal.tabs" grow slider-color="yellow accent-3">
											<v-tab @click="loadDistrict(0, 'deal')">
												Khu vực
											</v-tab>
											<v-tab @click="loadType(0, 'deal')">
												Danh mục
											</v-tab>
										</v-tabs>
										<v-list v-if="deal.tabs==0" class="pt-0 scroll-y" dense style="min-height:424px">
											<v-list-tile @click="loadDistrict(0, 'deal')" :class="{'red--text text--accent-2 bold' : deal.list_flag == 0}" >
												<v-list-tile-content>
													<v-list-tile-title>Tất cả</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{deal.count}} <v-icon color="red">whatshot</v-icon></v-subheader>
												</v-list-tile-action>
											</v-list-tile>
											<v-list-tile  v-for="(item, i) in deal.districts" :key="i" @click="loadDistrict(item.id, 'deal')" :class="{'red--text text--accent-2': item.id == deal.list_flag}" >
												<v-list-tile-content>
													<v-list-tile-title>{{item.district_name}}</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{item.stores_count}} <v-icon color="red">whatshot</v-icon></v-subheader>
												</v-list-tile-action>
											</v-list-tile>
										</v-list>
										<v-list v-if="deal.tabs==1" class="pt-0 scroll-y" dense style="min-height:424px">
											<v-list-tile @click="loadType(0, 'deal')" :class="{'red--text text--accent-2' : deal.list_flag == 0}" >
												<v-list-tile-content>
													<v-list-tile-title>Tất cả</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{deal.count}} <v-icon color="red">whatshot</v-icon></v-subheader>
												</v-list-tile-action>
											</v-list-tile>

											<v-list-tile  v-for="(item, i) in deal.types" :key="i" @click="loadType(item.id, 'deal')" :class="{'red--text text--accent-2': item.id == deal.list_flag}">
												<v-list-tile-content>
													<v-list-tile-title>{{ item.type_name }}</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{item.stores_count}} <v-icon color="red">whatshot</v-icon></v-subheader>
												</v-list-tile-action>
											</v-list-tile>
										</v-list>
									</v-card>	
								</v-flex>

								<v-flex xs12 sm6 md9>
									<v-content>
										<v-layout row wrap >
											<v-flex  xs12 md4 d-flex v-for="(item, i) in deal.stores " :key="i">
												<v-card nuxt :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}" width="200px" hover ripple >
													<v-card-media class="white--text" height="150px" :src="image(item.avatar)">
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

					<v-flex  xs12 sm12 md12 >
						<v-card flat ><!-- START ALL STORE -->					
							<v-layout grey lighten-4 fill-height row wrap class="elevation-1">
								<v-toolbar color="white" flat dense>
									<v-toolbar-title class="red--text text--accent-2">
										TẤT CẢ
									</v-toolbar-title>
								</v-toolbar>					
								<v-flex :class="{'d-flex': $vuetify.breakpoint.mdAndUp, 'order-xs2': $vuetify.breakpoint.smAndDown}" xs12 sm6 md3  class="pa-0">
									<v-card flat tile>
										<v-tabs v-model="all.tabs" grow slider-color="yellow accent-3">
											<v-tab @click="loadDistrict(0, 'all')">
												Khu vực
											</v-tab>
											<v-tab @click="loadType(0, 'all')">
												Danh mục
											</v-tab>
										</v-tabs>
										<v-list v-if="all.tabs==0" class="pt-0 scroll-y" dense style="min-height:424px">
											<v-list-tile @click="loadDistrict(0, 'all')" :class="{'red--text text--accent-2' : all.list_flag == 0}" >
												<v-list-tile-content>
													<v-list-tile-title>Tất cả</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{all.count}}</v-subheader>
												</v-list-tile-action>
											</v-list-tile>
											<v-list-tile  v-for="(item, i) in all.districts" :key="i" @click="loadDistrict(item.id, 'all')" :class="{'red--text text--accent-2': item.id == all.list_flag}" >
												<v-list-tile-content>
													<v-list-tile-title>{{item.district_name}}</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{item.stores_count}}</v-subheader>
												</v-list-tile-action>
											</v-list-tile>
										</v-list>
										<v-list v-if="all.tabs==1" class="pt-0 scroll-y" dense style="min-height:424px">
											<v-list-tile @click="loadType(0, 'all')" :class="{'red--text text--accent-2' : all.list_flag == 0}" >
												<v-list-tile-content>
													<v-list-tile-title>Tất cả</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{all.count}}</v-subheader>
												</v-list-tile-action>
											</v-list-tile>

											<v-list-tile  v-for="(item, i) in all.types" :key="i" @click="loadType(item.id, 'all')" :class="{'red--text text--accent-2': item.id == all.list_flag}">
												<v-list-tile-content>
													<v-list-tile-title>{{ item.type_name }}</v-list-tile-title>
												</v-list-tile-content>
												<v-list-tile-action>
													<v-subheader>{{item.stores_count}}</v-subheader>
												</v-list-tile-action>
											</v-list-tile>
										</v-list>
									</v-card>	
								</v-flex>

								<v-flex xs12 sm6 md9 order-xs1>
									<v-content>
										<v-layout row wrap >
											<v-flex  xs12 md4 d-flex v-for="(item, i) in all.stores " :key="i">
												<v-card nuxt :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}" width="200px" hover ripple >
													<v-card-media class="white--text" height="150px" :src="image(item.avatar)">
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
														<div class="subheading grey--text">
															{{item.type.name}}
														</div>		
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
										<div class="text-xs-center" v-if="all.pagination.last_page>1">
											<v-pagination :length="all.pagination.last_page" v-model="all.pagination.current_page" @input="changePage(all.pagination.current_page, 'all')" circle></v-pagination>
										</div>
									</v-content>
								</v-flex>
							</v-layout>
						</v-card><!-- END ALL STORE -->
					</v-flex>
				</v-layout>
			</v-flex> 

			<v-flex xs12 sm6 md3 :class="{'mt-4': $vuetify.breakpoint.xsOnly}">
				<v-card color="grey lighten-3" >
					<v-layout column wrap >
						<v-flex :d-flex="$vuetify.breakpoint.xsOnly" class="pt-0 pb-0">
							<v-card height="100%" flat tile>
								<v-toolbar color="red accent-2" flat dense>
									<v-toolbar-title class="white--text">
										Bán Chạy
									</v-toolbar-title>
								</v-toolbar>
								<v-divider></v-divider>
								<v-card-text>

								</v-card-text>
							</v-card>
						</v-flex>
					</v-layout>
				</v-card>
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
import {getCityHasDealURL, getStoreHasDealURL} from '@/config.js'
import { mapState } from "vuex";
import Cookies from "js-cookie";
export default {
	mixins: [index],
	middleware: ["home"],
	// Async Data
	async asyncData({ store }) {
		return {
			deal: {
				tabs: null,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 6,
				count:0,
			},
			all: {
				tabs: null,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 6,
				count: 0
			},
			new: {
				tabs: null,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 6,
				count: 0
			},
			loading: true,
			endPage: false
		}
	},
	methods: {
		//GET DISTRICT AND TYPE BY CITY ID
		getCity: function(id) {
			axios.get('/api/GetCityInformation'+'/'+id, {withCredentials:true}).then(response => {
				if(response.status == 200) {
					this.all.districts = response.data.districts
					this.all.types     = response.data.types
					let count          = 0
					this.all.districts.forEach(item => {
						count = count + parseFloat(item.stores_count)					
						return count
					})
					this.all.count = count
				}
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
					this.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
					this.$store.commit('UPDATE_CITY', response.data.city)
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
				if(this.all.list_flag != id) {
					this.all.list_flag = id
					const query = {did:id, tid:0, page:0}
					this.fetchStore(query)
				}
				break;

				case 'deal':
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
				if(this.all.list_flag != id) {
					this.all.list_flag = id
					const query = {did:0, tid:id, page:0}
					this.fetchStore(query)
				}
				break;

				case 'deal':
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
    	},
    },
    created: async function() {
    	if(Cookies.get('flag_c') != null || typeof Cookies.get('flag_c') != 'undefined') {
    		await setTimeout(() => {
    			this.getCityHasDeal(Cookies.get('flag_c'))	
    		}, 300)

    		await setTimeout(() => {
    			this.getCity(Cookies.get('flag_c'))
    		}, 300)

    	} else {
    		await setTimeout(() => {
    			this.getCityHasDeal(10001)
    		}, 300)

    		await setTimeout(() => {
    			this.getCity(10001)
    		})			
    	}
    	const query = {did:0, tid:0, page:0}
    	await setTimeout(() => {
    		this.fetchStoreWithDeal(query)
    	}, 300)

    	await setTimeout(() => {
    		this.fetchStore(query)
    	}, 300)	

    	this.loading = false
    }
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
