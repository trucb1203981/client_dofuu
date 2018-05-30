<template>
	<v-text-field prepend-icon="place" placeholder="" :value="address" id="auto-complete"></v-text-field>
</template>


<script>
export default {
	props: ['address'],
	methods: {
		autoComplete() {
			var vm = this
			var input = document.getElementById('auto-complete')
			var autocomplete = new google.maps.places.Autocomplete(input)
			autocomplete.addListener('place_changed', function() {
				var place = autocomplete.getPlace();
				vm.$emit('place_changed', place)
			});
		}
	},
	mounted() {
		this.autoComplete()
	}
}
</script>

<style>

</style>