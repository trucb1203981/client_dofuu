<template>
	<v-container :class="{'px-0': $vuetify.breakpoint.xsOnly}">
		<v-layout row wrap >
			<v-flex xs12>
				<v-card color="grey lighten-4" flat class="card-radius">
					<v-toolbar color="white" flat dense>
						<v-toolbar-title>
							<span>Kết quả tìm kiếm</span>
						</v-toolbar-title>
					</v-toolbar>		
					<v-tabs color="transparent" slider-color="red darken-3" active-class="red--text text--accent-4 font-weight-bold">
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

					<v-container grid-list-xs="$vuetify.breakpoint.smAndDown" grid-list-md="$vuetify.breakpoint.mdAndUp">
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