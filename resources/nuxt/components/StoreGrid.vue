<template>
	<v-layout row wrap>
		<v-flex v-if="$vuetify.breakpoint.mdAndUp" md3 d-flex v-for="(item, i) in stores" :key="i">

			<v-card nuxt :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}" hover ripple >
				<v-system-bar status color="red darken-4" dark>
					<v-icon left>access_time</v-icon>
					<span v-for="(item, i) in item.activities" v-if="i==0">		
						<span v-for="(time, i) in item.times">
							{{time.from}} - {{time.to}} 
						</span>	
					</span>
					<v-spacer></v-spacer>
					<span>
						{{item.type.name}}
					</span>
				</v-system-bar>
				<v-card-media class="white--text" :height="$vuetify.breakpoint.mdAndUp ? '150px' : '250px' " :src="image(item.avatar)">
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

				<v-system-bar v-if="item.coupon != null " status color="transparent">
					<h4 class="red--text"><i>{{item.coupon.title}}</i></h4>
				</v-system-bar>

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
</template>

<script>
import index from "@/mixins/index.js";
export default {
	mixins: [index],
	props: ['stores', 'currentCity'],
}
</script>