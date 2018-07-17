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
					
					<!-- STORE LIST -->								
					<vue-store-list v-if="currentCity != null && $vuetify.breakpoint.smAndDown" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-list>				
					
					<!-- STORE GRID -->										
					<vue-store-grid v-if="currentCity != null && $vuetify.breakpoint.mdAndUp" :stores.sync="stores" :currentCity.sync="currentCity"></vue-store-grid>

				</v-container>
			</v-card>
		</v-layout>
	</v-container>
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
			pageSize: 8,
			offset: 0,
			bottom: false,
			trigger:300,
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
				await axios.get('/api/Search/All', {params, withCredentials:true}).then(response => {
					if(response.status === 200) {
						this.stores = response.data.data
					}
				})
				this.loading = false
				this.offset = Math.floor(this.offset + this.pageSize)
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
			console.log(val)
			this.searchStore(val)
		}
	},
	mounted() {
		this.searchStore(this.$route.query.q)
	}
}
</script>