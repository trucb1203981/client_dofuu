<template>
	<v-container grid-list-lg  :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-layout row wrap >
			<v-flex xs12>
				<v-card color="grey lighten-4" flat>	
					<v-tabs color="red" dark slider-color="yellow">
						<v-tab nuxt :to="{name: 'city-tim-kiem-tat-ca', query: {q: $route.query.q}, params: {city: $route.params.city}}">
							Tất cả
						</v-tab>
						<v-tab nuxt :to="{name: 'city-tim-kiem-dia-diem', query: {q: $route.query.q}, params: {city: $route.params.city}}">
							Địa điểm
						</v-tab>
						<v-tab :to="{name: 'city-tim-kiem-mon', query: {q: $route.query.q}, params: {city: $route.params.city}}">
							Món
						</v-tab>
						<v-tab :to="{name: 'city-tim-kiem-danh-muc', query: {q: $route.query.q}, params: {city: $route.params.city}}">
							Danh mục
						</v-tab>
					</v-tabs>			
					<v-toolbar color="white" flat dense>
						<v-toolbar-title>
							DANH SÁCH CỬA HÀNG
						</v-toolbar-title>
					</v-toolbar>					
					<v-container>
						<nuxt-child></nuxt-child>
					</v-container>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>
<script>
	export default {
		created() {

		}
	}
</script>