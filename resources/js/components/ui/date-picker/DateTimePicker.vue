<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

interface Props {
  modelValue?: string
  label?: string
  id?: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const internalValue = ref(props.modelValue || '')

watch(
  () => props.modelValue,
  (newValue) => {
    internalValue.value = newValue || ''
  }
)

const handleInput = (event: Event) => {
  const value = (event.target as HTMLInputElement).value
  internalValue.value = value
  emit('update:modelValue', value)
}

// Format datetime string for datetime-local input
const formattedValue = computed({
  get: () => {
    if (!internalValue.value) return ''
    // datetime-local expects format: YYYY-MM-DDTHH:mm
    return internalValue.value.slice(0, 16)
  },
  set: (value) => {
    internalValue.value = value
    emit('update:modelValue', value)
  },
})
</script>

<template>
  <div class="space-y-2">
    <Label v-if="label" :for="id">{{ label }}</Label>
    <Input
      :id="id"
      type="datetime-local"
      :value="formattedValue"
      @input="handleInput"
    />
  </div>
</template>
