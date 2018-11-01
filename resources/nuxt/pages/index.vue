<template>
	<v-container transparent :grid-list-md="$vuetify.breakpoint.smAndDown" :grid-list-lg="$vuetify.breakpoint.mdAndUp" v-scroll="onScroll" :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-layout transparent row wrap v-if="!loading">
			<v-flex xs12>
				<v-layout column> 
					<v-flex xs12>
						<v-carousel :style="$vuetify.breakpoint.mdAndUp ? `height: 315px` : `height: 200px`" hide-delimiters :hide-controls="$vuetify.breakpoint.smAndDown" interval="3000">
							<v-carousel-item >
								<v-img  src="img/deal_banner.png" aspect-ratio="1.77778" alt="" :height="$vuetify.breakpoint.mdAndUp ? '315px' : '200px'" contain>
								</v-img>
							</v-carousel-item>
							<v-carousel-item >
								<v-img src="img/Banner1.png" aspect-ratio="1.77778" alt="" :height="$vuetify.breakpoint.mdAndUp ? '315px' : '200px'" contain>
								</v-img>
							</v-carousel-item>
							<v-carousel-item >
								<v-img src="img/Banner2.png" aspect-ratio="1.77778" alt="" :height="$vuetify.breakpoint.mdAndUp ? '315px' : '200px'" contain>
								</v-img>
							</v-carousel-item>
							<v-carousel-item >
								<v-img src="img/Banner3.png" aspect-ratio="1.77778" alt="" :height="$vuetify.breakpoint.mdAndUp ? '315px' : '200px'" contain>
								</v-img>
							</v-carousel-item>
						</v-carousel>
					</v-flex>

					<v-flex xs12 v-if="deal.stores.length>0" class="mb-3">
						<!-- START DEAL STORES -->
						<v-card flat color="grey lighten-4" class="card-radius">	
							<v-toolbar color="white" flat dense  extension-height="30px">
								<v-icon color="red">whatshot</v-icon>
								<v-toolbar-title class="red--text text--accent-3" id="deal-store">
									<h5 class="text-uppercase">{{deal.title}}</h5>
								</v-toolbar-title>									

								<v-toolbar-items slot="extension">
									<!-- START DISTRICT IN DEAL STORE -->
									<v-menu left bottom offset-y :open-on-hover="$vuetify.breakpoint.mdAndUp">
										<v-btn slot="activator" flat><h5>Khu vực</h5><v-icon right>arrow_drop_down</v-icon></v-btn>
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
										<v-btn slot="activator" flat><h5>Danh mục</h5><v-icon right>arrow_drop_down</v-icon></v-btn>
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
							<v-divider></v-divider>
							<v-progress-linear v-if="deal.loading" background-color="grey lighten-3"	color="white" :indeterminate="true" class="py-0 my-0"></v-progress-linear>

							<v-card-text v-else>
								<!-- STORE LIST -->										
								<vue-store-list v-if="!!currentCity" :stores.sync="deal.stores" :currentCity.sync="currentCity"></vue-store-list>	
								<!-- STORE GRID -->
								<vue-store-grid v-if="!!currentCity" :stores.sync="deal.stores" :currentCity.sync="currentCity"></vue-store-grid>
							</v-card-text>

							<div class="text-xs-center" v-if="deal.pagination.last_page>1">
								<v-pagination :length="deal.pagination.last_page" v-model="deal.pagination.current_page" @input="changePage(deal.pagination.current_page, 'deal')" circle prev-icon="chevron_left" next-icon="chevron_right">
								</v-pagination>
							</div>		
						</v-card><!-- END DEAL STORE -->

					</v-flex>

					<v-flex  xs12 md12 >

						<v-card flat color="grey lighten-4" class="card-radius" ><!-- START ALL STORE -->	
							<v-toolbar color="white" flat dense  extension-height="30px">
								<v-toolbar-title class="red--text text--accent-3" >
									<h5 id="all-store" class="text-uppercase">{{all.title}}</h5>
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
							<v-divider></v-divider>
							<v-progress-linear v-if="all.loading" background-color="grey lighten-3"	color="white" :indeterminate="true" class="py-0 my-0"></v-progress-linear>

							<v-card-text v-else>
								<!-- STORE LIST -->									
								<vue-store-list v-if="!!currentCity" :stores.sync="all.stores" :currentCity.sync="currentCity"></vue-store-list>				
								<!-- STORE GRID -->										
								<vue-store-grid v-if="!!currentCity" :stores.sync="all.stores" :currentCity.sync="currentCity"></vue-store-grid>
							</v-card-text>

							<div class="text-xs-center" v-if="all.pagination.last_page>1">
								<v-pagination :length="all.pagination.last_page" v-model="all.pagination.current_page" @input="changePage(all.pagination.current_page, 'all')" circle prev-icon="chevron_left" next-icon="chevron_right"></v-pagination>
							</div>

							<v-card-actions>
								<v-btn v-if="!!currentCity" color="grey lighten-2" block :to="{name: 'city-tat-ca-dia-diem', params: {city: currentCity.slug }}" round small >Xem thêm <v-icon right>arrow_forward</v-icon> </v-btn>
							</v-card-actions>

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
	import { distanceMatrixService } from '@/utils'
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
				title: 'Hot deals',
				tabs: 0,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 8,
				count:0,
				loading: false
			},
			all: {
				title: 'Tất cả',
				tabs: 0,
				list_flag: 0,
				stores: [],
				districts: [],
				types: [],
				pagination: {},
				pageSize: 8,
				count: 0,
				loading: false
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
			var vm     = this
			var params = {districtId: data.did, typeId: data.tid, size: vm.deal.pageSize, page: data.page}
			if(!vm.all.loading) {
				vm.all.loading = !vm.all.loading
				return new Promise((resolve, reject) => {
					setTimeout(() => {
						axios.get('/api/LoadStore', {params, withCredentials:true}).then(response => {
							if(response.status == 200) {
								vm.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
								vm.all.stores     = response.data.data
								vm.all.pagination = response.data.pagination
								resolve(true)
							}
						}).finally(() => {
							vm.all.loading = !vm.all.loading
						})
					}, 100)
				})
				
			}
			
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
			var vm     = this
			var params = {districtId: data.did, typeId: data.tid, size: vm.deal.pageSize, page: data.page}
			if(!vm.deal.loading) {
				vm.deal.loading = !vm.deal.loading
				return new Promise((resolve, reject) => {
					setTimeout(() => {
						axios.get('/api/FetchStoreHasDeal', {params, withCredentials:true}).then(response => {
							if(response.status == 200) {
								vm.$store.commit('CHANGE_CITY', parseInt(Cookies.get('flag_c')))
								vm.deal.stores     = response.data.data
								vm.deal.pagination = response.data.pagination
								resolve(true)
							}
						}).finally(() => {
							vm.deal.loading = !vm.deal.loading
						})
					}, 100)
				})
			}
			
		},
		//LOAD STORES IN DISTRICT
		loadDistrict: function(id, type) {
			var vm     = this
			const name = new String(type).toLowerCase();
			switch(name) {
				case 'all':
				vm.all.tabs = 0
				if(vm.all.list_flag != id) {
					vm.all.list_flag = id
					const query = {did:id, tid:0, page:0}
					vm.fetchStore(query)
				}
				break;

				case 'deal':
				vm.deal.tabs = 1
				if(vm.deal.list_flag != id) {
					vm.deal.list_flag = id
					const query = {did:id, tid:0, page:0}
					vm.fetchStoreWithDeal(query)
				}
				break;

				case 'new':

				break;
			}
		},
		//LOAD STORES IN TYPE
		loadType: function(id, type) {
			var vm     = this
			const name = new String(type).toLowerCase();
			switch(name) {
				case 'all':
				vm.all.tabs = 1
				if(vm.all.list_flag != id) {
					vm.all.list_flag = id
					const query = {did:0, tid:id, page:0}
					vm.fetchStore(query)
				}
				break;

				case 'deal':
				vm.deal.tabs = 1
				if(vm.deal.list_flag != id) {
					vm.deal.list_flag = id
					const query = {did:0, tid:id, page:0}
					vm.fetchStoreWithDeal(query)
				}
				break;

				case 'new':

				break;
			}
		},
		//CHANGE PAGE
		changePage: function(page, type) {
			var vm     = this
			const name = new String(type).toLowerCase();
			switch (name) {
				case 'all': 
				if (vm.all.tabs == 0) {

					const query = {did: vm.all.list_flag, tid: 0, page: page}	
					vm.fetchStore(query).then(results => {
						if(results) {
							vm.$vuetify.goTo('#all-store', {duration: 300,  easing: 'easeInOutCubic', offset: 0})
						}
					})

				} else if (vm.all.tabs == 1) {

					vm.$vuetify.goTo('#all-store', {duration: 300,  easing: 'easeInOutCubic', offset: 0})
					const query = {did: 0, tid: vm.all.list_flag, page: page}
					vm.fetchStore(query).then(results => {
						if(results) {
							vm.$vuetify.goTo('#all-store', {duration: 300,  easing: 'easeInOutCubic', offset: 0})
						}
					})

				}
				break;

				case 'deal':
				if (vm.deal.tabs == 0) {

					const query = {did: vm.deal.list_flag, tid: 0, page: page}	
					vm.fetchStoreWithDeal(query).then(result => {
						if(result) {
							vm.$vuetify.goTo('#deal-store', {duration: 300,  easing: 'easeInOutCubic', offset: 0})
						}
					})

				} else if (this.deal.tabs == 1) {	

					const query = {did: 0, tid: this.deal.list_flag, page: page}
					vm.fetchStoreWithDeal(query).then(result => {
						if(result) {
							vm.$vuetify.goTo('#deal-store', {duration: 300,  easing: 'easeInOutCubic', offset: 0})
						}
					})

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
    		currentCity: state => state.cityStore.currentCity,
    		myLocation: state  => state.myLocation
    	}),
    	city: function() {
    		return this.$store.getters.getCityByID(this.currentCity)
    	}
    },
    created: function() {
    	const query = { did:0, tid:0, page:0 }
    	if(Cookies.get('flag_c') != null || typeof Cookies.get('flag_c') != 'undefined') {
    		setTimeout(() => {
    			this.getCity(Cookies.get('flag_c')).then(response => {
    				this.fetchStoreWithDeal(query)
    				this.fetchStore(query)		
    			})
    			this.getCityHasDeal(Cookies.get('flag_c'))
    			
    		},100)

    		this.loading = false
    	} else {
    		setTimeout(() => {
    			this.getCity(10001).then(response => {
    				this.fetchStoreWithDeal(query)
    				this.fetchStore(query)
    			})
    			this.getCityHasDeal(10001)
    		}, 100)

    		this.loading = false
    	}
    	
    },
};
</script>

<style>

</style>
