<template>
  <div class="create-booking-form">
    <h2 class="text-2xl font-bold mb-4">Create New Booking</h2>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Client Select -->
      <div>
        <label for="client_id" class="block text-sm font-medium mb-1"> Client </label>
        <select
          id="client_id"
          v-model="form.client_id"
          required
          class="w-full px-3 py-2 border rounded-lg"
        >
          <option value="0" disabled>Select a client</option>
          <option
            v-for="client in clientsData?.data"
            :key="client.id"
            :value="client.id"
          >
            {{ client.name }}
          </option>
        </select>
      </div>

      <!-- Title -->
      <div>
        <label for="title" class="block text-sm font-medium mb-1"> Title </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          class="w-full px-3 py-2 border rounded-lg"
          placeholder="Meeting title"
        />
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-sm font-medium mb-1">
          Description (Optional)
        </label>
        <textarea
          id="description"
          v-model="form.description"
          rows="3"
          class="w-full px-3 py-2 border rounded-lg"
          placeholder="Meeting description"
        />
      </div>

      <!-- Start Time -->
      <div>
        <label for="start_time" class="block text-sm font-medium mb-1"> Start Time </label>
        <input
          id="start_time"
          v-model="form.start_time"
          type="datetime-local"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <!-- End Time -->
      <div>
        <label for="end_time" class="block text-sm font-medium mb-1"> End Time </label>
        <input
          id="end_time"
          v-model="form.end_time"
          type="datetime-local"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="createBooking.isPending"
        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ createBooking.isPending ? 'Creating...' : 'Create Booking' }}
      </button>

      <!-- Error Display -->
      <div v-if="createBooking.isError" class="text-red-600 text-sm">
        Error: {{ createBooking.error?.message }}
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useCreateBooking } from '../hooks/useBookings';
import { useClients } from '../hooks/useClients';
import type { CreateBookingInput } from '../types/api';

const createBooking = useCreateBooking();
const { data: clientsData } = useClients();

const form = ref<CreateBookingInput>({
  client_id: 0,
  title: '',
  description: '',
  start_time: '',
  end_time: '',
});

const handleSubmit = () => {
  createBooking.mutate(form.value, {
    onSuccess: () => {
      alert('Booking created successfully!');
      // Reset form
      form.value = {
        client_id: 0,
        title: '',
        description: '',
        start_time: '',
        end_time: '',
      };
    },
    onError: (error) => {
      alert(`Error creating booking: ${error.message}`);
    },
  });
};
</script>
