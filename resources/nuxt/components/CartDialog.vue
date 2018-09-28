<template>
	<v-dialog v-model="show" persistent scrollable disabled max-width="1000">
		<v-card v-if="editedItem != null" class="grey lighten-4">
			<v-toolbar color="red accent-4" dense class="elevation-0" dark flat height="30">
				<v-toolbar-title class="body-1 px-0 text-uppercase"> {{editedItem.name}}</v-toolbar-title>
				<v-spacer></v-spacer>
				<div class="font-weight-bold">{{totalProduct(editedItem) | formatPrice}}</div>
			</v-toolbar>

			<v-card-text class="white" style="height: 500px">
				<v-container fluid grid-list-xs>
					<v-layout row wrap>

						<v-flex  xs12 md4>
							<v-card>
								<v-img :src="image(editedItem.image)" :height="$vuetify.breakpoint.mdAndUp ? '250' : '200'">								
								</v-img>
							</v-card>				
						</v-flex>

						<v-flex xs12 md8>
							<v-container class="py-0 my-0">
								<v-layout row wrap>
									<v-flex xs12>
										<v-card v-if="editedItem.description != null" class="card-radius" color="grey lighten-3 mt-2">
											<div class="pa-2">{{ editedItem.description }}</div>				
										</v-card>
										<v-radio-group mandatory v-model="editedItem.size" :row="$vuetify.breakpoint.mdAndUp">
											<v-radio color="green darken-3" :value="size" v-for="(size, i) in sizes" :key="i">
												<span slot="label" class="black--text body-1">{{size.name}} <strong>({{size.price | formatPrice}})</strong></span>
											</v-radio>
										</v-radio-group>

									</v-flex>

									<v-flex xs12>
										<v-select v-if="editedItem.haveTopping" v-model="editedItem.toppings" :items="store.toppings" hint="Chọn thêm topping"  label="Phần thêm" multiple persistent-hint small-chips box :menu-props="{ maxHeight: '250' }" dense return-object>	
											<template slot="selection" slot-scope="{index, item, parent, selected}">
												<v-chip v-if="index <= 4" :selected="selected" color="white" small>
													{{ item.name }}
													<v-icon small @click="parent.selectItem(item)" >close</v-icon>							
												</v-chip>
												<span
												v-if="index === 5"
												class="grey--text caption"
												>(+{{ editedItem.toppings.length - 5 }} Khác)</span>
											</template>

											<template slot="item" slot-scope="{item, parent, tile}">
												<v-list-tile-content>						
													{{item.name}}
												</v-list-tile-content>

												<v-list-tile-action>
													{{item.price |formatPrice}}
												</v-list-tile-action>
											</template>
										</v-select>
									</v-flex>

									<v-flex xs12>
										<div>Số lượng: 
											<span>
												<v-btn icon ripple @click.stop="editedItem.qty++" class="ma-0">
													<v-icon color="green darken-3">add_box</v-icon>
												</v-btn>
											</span>
											<span>												
												{{editedItem.qty}}
											</span>
											<span>
												<v-btn icon ripple @click.stop="editedItem.qty > 0 ? editedItem.qty-- :''"  class="ma-0">
													<v-icon color="grey" >indeterminate_check_box</v-icon>
												</v-btn>
											</span>				
										</div>

									</v-flex>

									<v-flex xs12>
										<v-text-field
										solo
										class="btn-custom"
										background-color="grey lighten-4"
										v-model="editedItem.memo"
										label="Ghi chú" 
										flat
										></v-text-field>
									</v-flex>

								</v-layout>		
							</v-container>
						</v-flex>
					</v-layout>
				</v-container>			
			</v-card-text>
			<v-divider></v-divider>
			<v-card-actions>
				<v-btn color="red darken-1" @click.native="closeCartDialog" class="mr-5 white--text" round small>Hủy bỏ</v-btn>
				<v-spacer></v-spacer>									
				<v-btn color="green darken-3" class="white--text" round @click.prevent="addToCart(editedItem)" :loading="processAddCart" :disabled="editedItem.qty == 0" small>{{totalProduct(editedItem) | formatPrice}} <v-icon right>add_shopping_cart</v-icon></v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
	export default {
		data() {
			return {
				show: false,
				resolve: null,
				reject: null,
				editedItem: {
					rowId: null, 
					size: null,
					memo: null,
					qty: 1,
					subTotal: 0,
					toppings: []
				},
				default: {
					rowId: null, 
					size: null,
					memo: null,
					qty: 1,
					subTotal: 0,
					toppings: []				
				},
			}
		}
	}
</script>