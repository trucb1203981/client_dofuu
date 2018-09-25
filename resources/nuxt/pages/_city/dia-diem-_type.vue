<template>
	<v-container grid-list-xs="$vuetify.breakpoint.smAndDown" grid-list-md="$vuetify.breakpoint.mdAndUp" v-scroll="onScroll" :class="{'px-0': $vuetify.breakpoint.xsOnly}"> 
		<v-layout row wrap>
			<v-flex xs12>
				<v-card flat >		
					<v-layout grey lighten-4 fill-height row wrap class="elevation-1">		
						<v-toolbar color="white" flat dense >
							<v-toolbar-title class="text-capitalize" v-if="!loading">
								{{title}}
							</v-toolbar-title>
						</v-toolbar>					
						<v-flex xs12>
							<v-container fluid>
								<!-- STORE LIST -->
								<vue-store-list v-if="!!currentCity" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-list>
								<!-- STORE GRID -->
								<vue-store-grid v-if="!!currentCity" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-grid>
								<!-- INFINITE LOADING -->
								<v-card v-if="processFetch" color="transparent" dark flat >
									<v-card-text class="text-xs-center">
										<v-progress-circular
										indeterminate
										color="grey"
										></v-progress-circular>
									</v-card-text>
								</v-card>		
							</v-container>
						</v-flex>			
					</v-layout>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
	import StoreList from '@/components/StoreList'
	import StoreGrid from '@/components/StoreGrid'
	import axios from 'axios'
	import { getHeader } from '@/config'
	import index from "@/mixins/index.js";
	import { mapState } from 'vuex'
	export default {
		mixins: [index],
		components: {
			'vue-store-list': StoreList,
			'vue-store-grid': StoreGrid
		},
		data() {
			return {
				stores: [],
				districts: [],
				trigger:300,
				currentCity: null,
				end: false,
				loading: true,
				processFetch: false
			}
		},
		methods: {
			onScroll: async function(e) {
				var vm = this
				if(window.innerHeight + window.scrollY >= (document.body.offsetHeight - vm.trigger) ) {
					if(!this.processFetch) {
						await vm.getStoreByType(vm.currentCity.id)
					}
				}
			},
			getStoreByType: function(id) {
				var vm     = this
				var offset = vm.stores.length
				const type = vm.$store.getters.getTypeBySlug(vm.$route.params.type)
				const data = { typeId: type.id, offset: offset }
				if(!vm.processFetch && !vm.end) {
					vm.processFetch = true
					return new Promise((resolve, reject) => {
						setTimeout(() => {							
							axios.post('/api/GetStoreByType/'+id, data, {withCredentials:true}).then(response => {
								if(response.status === 200) {
									if(response.data.data.length === 0) {
										vm.end = true
									}
									response.data.data.forEach(item => {
										vm.stores.push(item)
									})
									resolve(true)
								}
							}).finally(() => {					
								vm.processFetch = false
							})
						}, 100)
					}) 
				}
			}
		},
		computed: {
			currentType: function() {
				return this.$store.getters.getTypeBySlug(this.$route.params.type)
			},
			...mapState({
				cities: state => state.cityStore.cities,
				types: state => state.typeStore.types
			}),
			title: function() {
				return 'Địa điểm '+ this.currentType.name
			}
		},
		mounted() {
			this.$store.dispatch('fetchCity').then(response => {
				if(response.status === 200) {
					this.currentCity = this.$store.getters.getCityBySlug(this.$route.params.city)
					this.$store.dispatch('fetchType').then(response => {
						this.getStoreByType(this.currentCity.id).then((result) => {
							if(result) {
								this.loading = false
							}
						})
					})			
				}
			})		

		}
	}
</script>

