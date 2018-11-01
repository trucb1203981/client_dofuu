<template>
	<v-flex xs12 md6>
		<v-card color="white" v-if="!!coupon" class="card-radius">
			<v-tooltip v-model="showTooltip" top>
				<div slot="activator"></div>
				<span>{{messageTooltip}}</span>
			</v-tooltip>		
			<v-system-bar status color="transparent">
				<v-icon color="red" size="22">card_giftcard</v-icon>
				<div class="body-1">Nhập mã: <a class="font-weight-bold red--text text--accent" @click.prevent="copyCode()" ><u>{{coupon.code}}</u></a></div>
				<v-spacer></v-spacer>
				<v-tooltip top>
					<v-btn slot="activator" small flat @click.native="copyCode()" class="body-1" color="red accent" icon>
						<v-icon size="20">content_copy</v-icon>
					</v-btn> 
					<span>Sao chép mã</span>
				</v-tooltip>
			</v-system-bar>				
			<v-divider></v-divider>
			<v-card-text class="pb-0 grey lighten-5">
				<div>
					<h4>{{coupon.title}}</h4>
					<input type="hidden" ref="couponCode" id="coupon-code" :value="coupon.code">
				</div>
				<div>Đơn hàng tối thiểu: <span class="font-weight-bold">{{coupon.totalAmount | formatPrice}}</span></div>
				<div>Giảm tối đa: <span v-if="coupon.maxPrice > 0" class="font-weight-bold">{{coupon.maxPrice | formatPrice}}</span> <span v-else class="font-weight-bold">Không giới hạn</span></div>
			</v-card-text>	
			<v-divider></v-divider>
			<v-system-bar color="transparent">
				<div class="caption">Khuyến mãi kết thúc sau <v-icon>alarm</v-icon> 
					<span class="body-2"><strong>{{time.dd}}</strong> Ngày <span class="font-weight-bold">{{time.hh}} : {{time.mm}} : {{time.ss}} </span></span>
				</div>	
			</v-system-bar>							
		</v-card>
	</v-flex>
</template>

<script>
	export default {
		props: ['coupon'],
		data() {
			return {
				messageTooltip: '',
				showTooltip: false,
				time: {
					dd:0,
					hh:0,
					mm:0,
					ss:0
				},
			}
		},
		methods: {
			//COPY DEAL CODE
			copyCode() {
				var vm = this
				let code = vm.$refs.couponCode
				code.setAttribute('type', 'text')
				code.select()
				
				try {
					var successful = document.execCommand('copy');
					this.messageTooltip = successful ? 'Sao chép mã khuyến mãi thành công' : 'Sao chép mã khuyến mãi thất bại'
				} catch (error) {
					this.messageTooltip = 'Không thể sao chép mã khuyến mãi'
				}
				
				code.setAttribute('type', 'hidden')
				window.getSelection().removeAllRanges()
				vm.showTooltip = !vm.showTooltip

				setTimeout(() => {
					vm.showTooltip = !vm.showTooltip
				}, 1000)

			},
			start: function (date) {

				if(!!this.coupon) {

					date = this.coupon.endedAt
					setTimeout(() => {
						let timeNow   = new Date().getTime()

						let endedTime = new Date(date.date).getTime()				

						let distance  = Math.floor(endedTime - timeNow)/1000

						let day       = Math.floor(distance / (60 * 60 * 24));

						let hour      = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));

						let minutes   = Math.floor((distance % (60 * 60)) / 60);

						var seconds   = Math.floor(distance % (60));

						this.time     = {dd: day, hh: hour, mm: minutes, ss: seconds}
					}, 1000)

				}
			},
		},
		mounted() {
			this.start()
		},
		beforeUpdate() {
			this.start()
		}
		
	}
</script>