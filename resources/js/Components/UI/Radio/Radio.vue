<script setup>
	import { cva } from 'class-variance-authority';
	import { computed, onMounted, onUnmounted, ref, watch, nextTick } from 'vue';

	const props = defineProps({
		label: String,
		description: String,
		value: String,
		name: {
			type: String,
			required: true,
		},
		class: String,
		color: {
			type: String,
			validator: (val) => ['green', 'blue', 'red', 'gray'].includes(val),
			default: 'gray',
		},
		checked: Boolean,
		disabled: Boolean,
		modelValue: {
			type: String,
		},
	});
	const emits = defineEmits(['update:modelValue']);

	const id = ref(null);

	onMounted(() => {
		id.value = uniqueInputId();
	});

	const radioOuterClass = computed(() => {
		return cva(
			'mr-2 flex h-6 w-6 items-center justify-center rounded-full border-2 border-black bg-white transition-colors duration-150 ease-in-out hover:cursor-pointer hover:transition  peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2 peer-focus-visible:transition',
			{
				variants: {
					color: {
						green: 'border-green-700 peer-checked:bg-green-500 peer-hover:border-green-600',
						blue: 'border-blue-700 peer-checked:bg-blue-500 peer-hover:border-blue-600',
						red: 'border-red-700 peer-checked:bg-red-500 peer-hover:border-red-600',
						gray: 'border-black/50 peer-checked:bg-gray-700 peer-hover:border-gray-700',
					},
					disabled: {
						true: '!hover:ring-0 !cursor-not-allowed !border-gray-300 !bg-gray-100 !text-gray-400 hover:ring-gray-700/0 hover:ring-offset-0 focus:ring-black focus:ring-offset-0',
					},
				},
			}
		)({ color: props.color, disabled: props.disabled });
	});

	const uniqueInputId = () => crypto.randomUUID().split('-')[0];
</script>

<template>
	<div
		class="p-2"
		:class="props.class"
	>
		<div class="flex items-center">
			<input
				class="peer absolute h-8 w-8 opacity-0"
				type="radio"
				:name="props.name"
				:id="id"
				:checked="props.modelValue === props.value ? true : false"
				@change="
					(event) => {
						emits('update:modelValue', props.value);
					}
				"
			/>
			<div :class="radioOuterClass"></div>
			<div class="flex flex-col">
				<label :for="id">{{ props.label }}</label>
				<p class="text-sm text-black/60">{{ props.description }}</p>
			</div>
		</div>
	</div>
</template>
