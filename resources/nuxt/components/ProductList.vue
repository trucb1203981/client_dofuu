<template>
	<v-layout row wrap>
		<!-- DESKTOP PRODUCT -->
		<v-flex md12 lg6  v-for="(item, i) in products" :key="i" v-if="$vuetify.breakpoint.mdAndUp">
			<v-hover>
				<v-card slot-scope="{hover}" hover ripple class="mb-2 card-radius" v-on:click.native="open(item)">	

					<v-system-bar status color="red darken-3" dark height="30">
						<h5 class="white--text text-uppercase text-truncate">{{item.name}}</h5>
						<v-spacer></v-spacer>
						<v-expand-transition>
							<div v-if="hover" class="transition-fast-in-fast-out white--text" >
								<v-icon color="success" size="20">add_shopping_cart</v-icon>
							</div>
						</v-expand-transition>	
					</v-system-bar>

					<v-layout row wrap class="px-2" align-center justify-center>
						<v-flex md3 lg4>
							<v-layout align-center justify-center>
								<v-flex xs9>
									<v-card style="border-radius: 50%" :max-width="imageSize" :max-height="imageSize" raised>	
										<v-avatar :size="imageSize"  color="grey lighten-3">
											<img :src="image(item.image)" alt="item.name">
										</v-avatar>											
									</v-card>	
								</v-flex>	
							</v-layout>					
						</v-flex>

						<v-flex md9 lg8 class="px-0">				
							<v-layout row wrap class="pr-2">
								<v-flex v-for="(size, i) in item.sizes" xs4 class="pa-0 text-xs-center" :key="i" v-if="size.price >0">
									<div class="caption">{{size.name}}:</div>
									<div><strong>{{size.price | formatPrice}}</strong></div>
								</v-flex>
								<v-flex xs12 v-if="item.description != null" class="text-truncate pa-0">{{ item.description }}</v-flex>	
							</v-layout>											
						</v-flex>

						<v-flex xs12 md12>

						</v-flex>
					</v-layout>
				</v-card>
			</v-hover>
		</v-flex>
		<!-- MOBILE PRODUCT -->
		<v-flex xs12 v-if="$vuetify.breakpoint.smAndDown" v-for="(item, i) in products" :key="i">
			<v-card hover ripple class="elevation-1 mb-2 card-radius" raised v-on:click.native="open(item)">
				<v-layout row wrap class="pa-2"  align-center justify-center>
					<v-flex xs3 class="text-xs-center">
						<v-layout  align-center justify-center>
							<v-flex xs9>
								<v-card style="border-radius: 50%" :max-width="imageSize" :max-height="imageSize" raised>	
									<v-avatar :size="imageSize" color="grey lighten-3">
										<img :src="image(item.image)" alt="item.name">
									</v-avatar>
								</v-card>
							</v-flex>
						</v-layout>									
					</v-flex>

					<v-flex xs9 class="px-0">
						<v-card-text class="py-0">
							<v-system-bar status color="transparent" class="px-0">
								<h4 class="text-truncate">{{item.name}}</h4>
								<v-spacer></v-spacer>
								<v-btn icon small class="ma-0"><v-icon color="success" size="20">add_shopping_cart</v-icon></v-btn>
							</v-system-bar>	

							<v-layout column>
								<v-flex v-for="(size, i) in item.sizes" xs4 class="body justify-center py-0" :key="i" v-if="size.price >0">
									<div class="caption"><span>{{size.name}}: <strong>{{size.price | formatPrice}}</strong> </span></div>
								</v-flex>	
							</v-layout>
						</v-card-text>
					</v-flex>
					<v-flex xs12  v-if="item.description != null" class="text-truncate">
						<span class="text-xs-center caption ">Mô tả: {{ item.description }}</span>
					</v-flex>
				</v-layout>
			</v-card>
		</v-flex>
	</v-layout>
</template>


<script>
	import index from "@/mixins/index.js";
	export default {
		mixins: [index],
		props: ['products'],
		computed: {
			imageSize () {
				switch (this.$vuetify.breakpoint.name) {
					case 'xs': return '50'
					case 'sm': return '50'
					case 'md': return '50'
					case 'lg': return '60'
					case 'xl': return '80'
				}
			}
		},
		methods: {
			open: function(product) {
				this.$emit('openCart', product)
			}
		}
	}
</script>

<style>
.v-card--reveal {
	align-items: center;
	bottom: 0;
	justify-content: center;
	opacity: .5;
	position: absolute;
	width: 100%;
}
</style>