<template>
	<v-container grid-list-lg v-scroll="onScroll">
		<v-layout row wrap column>
			<v-card flat>				
				<v-toolbar color="white" flat dense>
					<v-toolbar-title>
						DANH SÁCH CỬA HÀNG
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
		</v-layout>
	</v-container>
</template>

<script>
import {mapState} from 'vuex'
import index from '@/mixins/index'
import axios from 'axios'
export default {
	mixins: [index],
	data() {
		return {
			stores: [],
			pageSize: 8,
			offset: 0,
			trigger:300,
			currentCity: null,
			end: false,
			loading: true
		}
	},
	methods: {
		onScroll: async function(e) {
			var vm = this
			if(window.innerHeight + window.scrollY >= (document.body.offsetHeight - vm.trigger) ) {
				if(!this.loading) {
					// await vm.searchStore(this.$route.query.q)
				}
			}
		},
		searchStore: async function(keywords) {
			const city = this.$store.getters.getCityBySlug(this.$route.params.city)
			const params = {keywords: keywords, citySlug: this.$route.params.city, pageSize: this.pageSize, offset: this.offset}
			if(!this.end) {
				this.loading = await true
				await axios.get('/api/Search/Types', {params, withCredentials:true}).then(response => {
					if(response.status === 200) {
						this.stores = response.data.data
					}
				})
				this.loading = false
				this.offset = Math.floor(this.offset + this.pageSize)
			}	
		}
	},
	watch: {
		'$route.query.q': function(val) {
			this.searchStore(val)
		}
	},
	mounted() {
		this.searchStore(this.$route.query.q)
	}
}
</script>