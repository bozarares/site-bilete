<template>
	<div :class="dropdownLabelClass">
		<div class="w-full py-0.5 text-sm font-semibold text-gray-700/50">
			<slot />
		</div>
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
	const dropdownLabelClass = computed(() => {
		return cva('pointer-events-none relative flex min-h-[1.25em] w-full select-none items-center border-b-0 px-4 transition-colors last:border-b-0', {
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
