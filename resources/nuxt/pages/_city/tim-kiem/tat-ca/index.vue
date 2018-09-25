<template>
	<div v-scroll="onScroll">				
		<!-- STORE LIST -->								
		<vue-store-list v-if="!!currentCity" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-list>				

		<!-- STORE GRID -->										
		<vue-store-grid v-if="!!currentCity" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-grid>

		<!-- LAZY LOADING START-->
		<v-card v-if="loading" color="transparent" dark flat>
			<v-card-text class="text-xs-center">
				<v-progress-circular
				indeterminate
				color="grey"
				></v-progress-circular>
			</v-card-text>
		</v-card>	
		<!-- LAZY LOADING END-->
	</div>
</template>

<script>

	import {mapState} from 'vuex'
	import index from '@/mixins/index'
	import axios from 'axios'
	import StoreList from '@/components/StoreList'
	import StoreGrid from '@/components/StoreGrid'

	export default {
		mixins: [index],
		components: {
			'vue-store-list': StoreList,
			'vue-store-grid': StoreGrid
		},
		data() {
			return {
				stores: [],
				trigger:300,
				end: false,
				loading: false
			}
		},
		methods: {
			//ONSCROLL LAZY LOAD
			onScroll: async function(e) {
				var vm = this
				if(window.innerHeight + window.scrollY >= (document.body.offsetHeight - vm.trigger) ) {
					if(!vm.loading) {
						vm.searchStore(this.$route.query.q)
					}
				}
			},
			// SEARCH STORE
			searchStore: function(keywords) {
				var vm       = this
				var offset   = vm.stores.length
				const city   = vm.$store.getters.getCityBySlug(vm.$route.params.city)
				const params = {keywords: keywords, citySlug: vm.$route.params.city, pageSize: this.pageSize, offset: offset}

				if(keywords.length > 0) {
					if(!vm.loading) {
						vm.loading = true
						setTimeout(() => {
							axios.get('/api/Search/All', {params, withCredentials:true}).then(response => {
								if(response.status === 200) {
									response.data.data.map(store => {
										vm.stores.push(store)
										return store
									})
								}
							}).finally(() => {
								vm.loading = false
							})
						}, 300)						
					}
				}
			}
		},
		computed: {
			...mapState({
				currentCity: state => state.cityStore.currentCity
			})
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