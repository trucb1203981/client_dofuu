<template>
	<v-layout row wrap>
		<v-flex xs12  v-for="(item, i) in stores" :key="i">
			<v-card hover :to="{name: 'city-store', params: {city: currentCity.slug, store: item.slug}}" ripple class="card-radius">	
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

				<v-layout row wrap class="pa-1" align-center justify-center>
					<v-flex xs4 sm2>
						<v-layout column align-center justify-center>
							<v-flex xs9>
								<v-card style="border-radius: 50%" :max-width="80" :max-height="80">	
									<v-avatar size="80" color="grey lighten-3">
										<img :src="image(item.avatar)" alt="alt">
									</v-avatar>		
								</v-card>
							</v-flex>	
						</v-layout>	
					</v-flex>

					<v-flex xs8 sm10 class="px-0">			
						<div class="font-weight-bold text-truncate">{{item.name}}</div>
						<v-tooltip top>									
							<div slot="activator" class="grey--text body-1 text-truncate">{{item.address}}</div>
							<span>{{item.address}}</span>
						</v-tooltip>									
					</v-flex>
				</v-layout>

				<v-system-bar status color="grey lighten-5">
					<span v-if="item.coupon != null">
						<h4 class="red--text"><i>{{item.coupon.title}}</i></h4>
					</span>
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

<style scoped>
.card-radius {
	border-radius: 15px;
}
</style>