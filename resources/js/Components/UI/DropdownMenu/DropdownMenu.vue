<script setup>
	import { cva } from 'class-variance-authority';
	import { computed, ref } from 'vue';

	const props = defineProps({
		label: String,
		align: {
			type: String,
			default: 'left',
			validator: (val) => ['left', 'right'].includes(val),
		},
	});
	const menuRef = ref(null);
	const buttonRef = ref(null);
	const menuIsVisible = ref(false);
	const toggleMenu = () => {
		if (!menuIsVisible.value) {
			menuIsVisible.value = true;
			document.addEventListener('click', handleOutsideClick);
		} else {
			menuIsVisible.value = false;
			document.removeEventListener('click', handleOutsideClick);
		}
	};

	const closeMenu = () => {
		if (menuIsVisible.value) {
			menuIsVisible.value = false;
			document.removeEventListener('click', handleOutsideClick);
		}
	};

	const menuClass = computed(() => {
		return cva('absolute z-10 mt-2 flex w-[15em] flex-col items-center rounded-lg border-2 border-gray-700/20 bg-white shadow-sm', {
			variants: {
				align: {
					left: 'left-0 origin-top-left',
					right: 'right-0 origin-top-right',
				},
			},
		})({ align: props.align });
	});

	const handleOutsideClick = (event) => {
		const dropdownElement = menuRef.value;
		const buttonElement = buttonRef.value;
		if (!dropdownElement.contains(event.target) && !buttonElement.contains(event.target)) {
			toggleMenu();
		}
	};
</script>

<template>
	<div
		@keydown.escape="closeMenu"
		class="relative"
	>
		<div
			@click="toggleMenu"
			ref="buttonRef"
		>
			<slot name="dropdownMenuButton" />
		</div>
		<Transition
			enter-from-class="scale-90 opacity-0"
			enter-active-class="transition-all duration-100"
			enter-to-class="scale-100 opacity-100"
			leave-from-class="scale-100 opacity-100"
			leave-active-class="transition-all duration-100"
			leave-to-class="scale-90 opacity-0"
			mode="out-in"
		>
			<div
				ref="menuRef"
				v-if="menuIsVisible"
				:class="menuClass"
			>
				<slot />
			</div>
		</Transition>
	</div>
</template>
