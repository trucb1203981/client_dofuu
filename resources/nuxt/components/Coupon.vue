<template>
	<v-card color="white" v-if="!!coupon" class="card-radius">
		<v-tooltip v-model="showTooltip" top>
			<div slot="activator"></div>
			<span>{{messageTooltip}}</span>
		</v-tooltip>		
		<v-system-bar status color="transparent">
			<v-spacer></v-spacer>
			<div class="caption">Khuyến mãi kết thúc sau <v-icon>alarm</v-icon> 
				<span class="body-2"><strong>{{time.dd}}</strong> Ngày <strong>{{time.hh}} : {{time.mm}} : {{time.ss}} </strong>
				</span>
			</div>
		</v-system-bar>				
		<v-alert outline color="red accent" icon="card_giftcard" :value="true" class="py-0">
			<v-card color="transparent" flat>		

				<v-card-text class="pb-0 pl-1">
					<div>
						<h4>{{coupon.title}}</h4>
						<div class="body-1">Nhập mã: <a class="font-weight-bold red--text text--accent" @click.prevent="copyCode()" ><u>{{coupon.code}}</u></a> để được khuyến mãi {{coupon.discount}}%</div>
						<input type="hidden" id="coupon-code" :value="coupon.code">
					</div>
				</v-card-text>							
				<v-card-actions  >

					<v-spacer></v-spacer>
					<v-btn small flat @click.native="copyCode()" class="body-1" outline color="red accent">
						<v-icon size="20">content_copy</v-icon>
						<h5>Sao chép mã</h5>
					</v-btn> 								
				</v-card-actions>							
			</v-card>						
		</v-alert>
	</v-card>
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
				let code = document.querySelector('#coupon-code')
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