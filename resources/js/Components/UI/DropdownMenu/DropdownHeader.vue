<template>
	<div :class="dropdownHeaderClass">
		<slot />
	</div>
</template>

<script setup>
	import { cva } from 'class-variance-authority';
	import { computed } from 'vue';

	const props = defineProps({
		align: {
			type: String,
			default: 'center',
			validator: (val) => ['left', 'center', 'right'].includes(val),
		},
	});
	const dropdownHeaderClass = computed(() => {
		return cva('pointer-events-none w-full select-none px-4 py-2 text-xl font-semibold', {
			variants: {
				align: {
					left: 'text-left',
					center: 'text-center',
					right: 'text-right',
				},
			},
		})({ align: props.align });
	});
</script>
