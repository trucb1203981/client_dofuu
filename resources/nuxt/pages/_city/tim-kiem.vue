<template>
	<v-content>
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
		<v-container grid-list-lg>
			<v-layout row wrap column>
				<nuxt-child></nuxt-child>
			</v-layout>
		</v-container>
	</v-content>
</template>
<script>
export default {
	created() {

	}
}
</script>