<template>
	<v-card :ripple="true" flat to="/"  max-height="48">
		<v-card-actions class="text-xs-center pa-0">
			<v-avatar size="36" color="white" tile>
				<img src="/dofuu40x40.png" alt="dofuu-logo" style="border-radius: 8px">
			</v-avatar>
			<div class="pl-1 headline font-weight-bold red--text text--darken-1" v-if="$vuetify.breakpoint.smAndUp">Dofuu.com</div>
		</v-card-actions>
	</v-card>
</template>