<template>
	<v-layout row wrap>
		<v-flex xs12 v-for="(item, i) in stores" :key="i">
			<v-card :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}" ripple>
				<v-system-bar status color="red darken-4" dark>
					<v-icon left>access_time</v-icon>
					<span v-for="(item, i) in item.activities" v-if="i==0">		
						<span v-for="(time, i) in item.times">
							{{time.from}} - {{time.to}} 
						</span>	
					</span>
					<v-spacer></v-spacer>
					<span>{{item.type.name}}</span>
				</v-system-bar>
				<v-layout row wrap>
					<v-flex xs3 class="text-xs-center">
						<v-avatar size="80" color="primary" tile>
							<img :src="image(item.avatar)" alt="alt">
						</v-avatar>
					</v-flex>
					<v-flex xs9>
						<div style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap"> <strong>{{item.name}}</strong></div>
						<v-tooltip top>									
							<div slot="activator" class="grey--text body-1" style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap">{{item.address}}</div>
							<span>{{item.address}}</span>
						</v-tooltip>
					</v-flex>
				</v-layout>	
				<v-system-bar status color="grey lighten-5">
					<v-spacer></v-spacer>
					<v-icon left>timer</v-icon>
					<span>		
						{{item.prepareTime}}p
					</span>
				</v-system-bar>										
			</v-card>
		</v-flex>	
	</v-layout>
</template>

<script>
import index from "@/mixins/index.js";
export default {
	mixins: [index],
	props: ['stores', 'currentCity'],
}
</script>