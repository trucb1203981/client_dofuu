<template>
	<v-container grid-list-xs="$vuetify.breakpoint.smAndDown" grid-list-md="$vuetify.breakpoint.mdAndUp" v-scroll="onScroll" v-if="stores.length > 0" :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-layout row wrap>
			<v-flex xs12>
				<v-card flat class="card-radius">			

					<v-toolbar color="white" flat dense>
						<v-toolbar-title >
							<span class="text-capitalize">{{title}}</span>
						</v-toolbar-title>
					</v-toolbar>

					<v-divider></v-divider>

					<v-container fluid class="grey lighten-4">
						<!-- STORE LIST -->
						<vue-store-list v-if="!!currentCity" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-list>
						<!-- STORE GRID -->
						<vue-store-grid v-if="!!currentCity" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-grid>
						<!-- INFINITE LOADING -->
						<v-card v-if="loading" color="transparent" dark flat>
							<v-card-text class="text-xs-center">
								<v-progress-circular
								indeterminate
								color="grey"
								></v-progress-circular>
							</v-card-text>
						</v-card>			
					</v-container>

				</v-card>
			</v-flex>				
		</v-layout>
	</v-container>
</template>

<script>
	import axios from 'axios'
	import StoreList from '@/components/StoreList'
	import StoreGrid from '@/components/StoreGrid'
	import {getHeader} from '@/config'
	import index from "@/mixins/index.js";

	export default {
		mixins: [index],
		components: {
			'vue-store-list': StoreList,
			'vue-store-grid': StoreGrid
		},
		data() {
			return {
				title: 'Tất cả địa điểm',
				stores: [],
				districts: [],
				pagination: {},
				pageSize: 8,
				offset: 0,
				trigger:300,
				currentCity: null,
				end: false,
				loading: false
			}
		},
		methods: {
			onScroll: function(e) {
				var vm = this
				if(window.innerHeight + window.scrollY >= (document.body.offsetHeight - vm.trigger) ) {
					if(!vm.loading) {
						vm.getStoreByType(vm.currentCity.id)
					}
				}
			},
			getStoreByType: function(id) {
				var vm     = this
				var offset = vm.stores.length
				const data = {typeId: 0, offset: offset}

				if(!vm.loading && !vm.end) {
					vm.loading = true
					setTimeout(() => {

						axios.post('/api/GetStoreByType/'+id, data, {withCredentials:true}).then(response => {
							if(response.status === 200) {

								if(response.data.data.length==0) {
									vm.end = true
								} 

								response.data.data.forEach(item => {
									this.stores.push(item)
								})
							}
						}).finally(() => {					
							vm.loading = false
						})	

					}, 100)					
				}
			}
		},
		mounted() {
			this.$store.dispatch('fetchCity').then(response => {
				if(response.status === 200) {
					this.currentCity = this.$store.getters.getCityBySlug(this.$route.params.city)
					this.getStoreByType(this.currentCity.id)
				}
			})		
		}
	}
</script>

