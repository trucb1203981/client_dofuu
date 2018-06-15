<template>
	<v-container grid-list-lg v-scroll="onScroll" v-if="stores.length>0">
		<v-card flat >				
			<v-toolbar color="white" flat dense>
				<v-toolbar-title>
					ĐỊA ĐIỂM {{currentType.name.toUpperCase()}}
				</v-toolbar-title>
			</v-toolbar>					
			<v-container>
				<v-layout row wrap>
					<v-flex v-if="stores.length>0" xs12 md3 d-flex v-for="(item, i) in stores " :key="i">
						<v-card nuxt :to="{name: 'city-store', params: {city: $route.params.city, store: item.slug}}" width="180px" hover ripple >
							<v-system-bar status color="red darken-4" dark>
								<v-icon left>access_time</v-icon>
								<span v-for="(item, i) in item.activities" v-if="i==0">		
									<span v-for="(time, i) in item.times">
										{{time.from}} - {{time.to}} 
									</span>	
								</span>
							</v-system-bar>
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
			</v-container>
		</v-card>
	</v-container>
</template>

<script>
import axios from 'axios'
import {getHeader} from '@/config'
import index from "@/mixins/index.js";

export default {
	mixins: [index],
	data() {
		return {
			stores: [],
			districts: [],
			pageSize: 8,
			offset: 0,
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
			const type = this.$store.getters.getTypeBySlug(this.$route.params.type)
			const data = {typeId: type.id, pageSize: this.pageSize, offset: this.offset}
			if(!this.end) {
				this.loading = await true
				await axios.post('/api/GetStoreByType/'+id, data, {withCredentials:true}).then(response => {
					if(response.status === 200) {

						if(response.data.data.length>0) {
							response.data.data.forEach(item => {
								this.stores.push(item)
							})
						} else {
							this.end = true
						}	
					}
				})	
				this.loading = false
				this.offset = Math.floor(this.offset + this.pageSize)
			}
		}
	},
	computed: {
		currentType: function() {
			return this.$store.getters.getTypeBySlug(this.$route.params.type)
		}
	},
	mounted() {

		this.$store.dispatch('fetchCity').then(response => {
			if(response.status === 200) {
				this.currentCity = this.$store.getters.getCityBySlug(this.$route.params.city)
				this.$store.dispatch('fetchType').then(response => {
					this.getStoreByType(this.currentCity.id)	
				})			
			}
		})		

	}
}
</script>

