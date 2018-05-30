<template>
	<v-container grid-list-lg v-scroll="onScroll">
		<v-card flat >				
			<v-toolbar color="white" flat dense>
				<v-toolbar-title>
					TẤT CẢ ĐỊA ĐIỂM
				</v-toolbar-title>
			</v-toolbar>					
			<v-container>
				<v-layout row wrap>
					<v-flex v-if="stores.length>0" xs12 md3 d-flex v-for="(item, i) in stores " :key="i">
						<v-card nuxt :to="{name: 'city-store', params: {city: $route.params.city, store: item.slug}}" width="180px" hover ripple >
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
			pagination: {},
			pageSize: 8,
			offset: 0,
			bottom: false,
			trigger:300,
			currentCity: null,
			end: false,
			loading: true
		}
	},
	methods: {
		bottomVisible() {
			const scrollY      = window.scrollY
			const visible      = document.documentElement.clientHeight
			const pageHeight   = document.documentElement.scrollHeight
			const bottomOfPage = visible + scrollY >= pageHeight
			this.offset        = Math.floor(this.offset + this.pageSize)
			return bottomOfPage || pageHeight < visible
		},
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

