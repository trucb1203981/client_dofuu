<template>
	<v-container grid-list-xs="$vuetify.breakpoint.smAndDown" grid-list-md="$vuetify.breakpoint.mdAndUp" v-scroll="onScroll" v-if="stores.length > 0">
		<v-layout row wrap>
			<v-flex xs12>
				<v-card flat >			
					<v-layout grey lighten-4 fill-height row wrap class="elevation-1">
						<v-toolbar color="white" flat dense>
							<v-toolbar-title>
								TẤT CẢ ĐỊA ĐIỂM
							</v-toolbar-title>
						</v-toolbar>		
						<v-flex xs12>
							<v-content>
								<!-- STORE LIST -->
								<vue-store-list v-if="currentCity != null && $vuetify.breakpoint.smAndDown" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-list>
								<!-- STORE GRID -->
								<vue-store-grid v-if="currentCity != null && $vuetify.breakpoint.mdAndUp" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-grid>
								<!-- INFINITE LOADING -->
								<v-card v-if="loading" color="transparent" dark >
									<v-card-text class="text-xs-center">
										<v-progress-circular
										indeterminate
										color="grey"
										></v-progress-circular>
									</v-card-text>
								</v-card>						

							</v-content>
						</v-flex>		
					</v-layout>						
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
			stores: [],
			districts: [],
			pagination: {},
			pageSize: 8,
			offset: 0,
			bottom: false,
			trigger:300,
			currentCity: null,
			end: false,
			loading: false
		}
	},
	methods: {
		onScroll: async function(e) {
			var vm = this
			if(window.innerHeight + window.scrollY >= (document.body.offsetHeight - vm.trigger) ) {
				if(!this.loading) {
					await vm.getStoreByType(vm.currentCity.id)
				}
			}
		},
		getStoreByType: async function(id) {
			const data = {typeId: 0, pageSize: this.pageSize, offset: this.offset}
			if(!this.end) {
				this.loading = true
				axios.post('/api/GetStoreByType/'+id, data, {withCredentials:true}).then(response => {
					if(response.status === 200) {

						if(response.data.data.length>0) {
							response.data.data.forEach(item => {
								this.stores.push(item)
							})
						} else {
							this.end = true
						}	
					}
				}).finally(() => {					
					this.loading = false
				})	
				this.offset = Math.floor(this.offset + this.pageSize)
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

