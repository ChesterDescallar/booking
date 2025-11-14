<template>
  <div class="bookings-list">
    <h2 class="text-2xl font-bold mb-4">Bookings</h2>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-8">
      <p>Loading bookings...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="isError" class="text-red-600 py-8">
      <p>Error loading bookings: {{ error?.message }}</p>
    </div>

    <!-- Data State -->
    <div v-else-if="bookingsData?.data">
      <div v-if="bookingsData.data.length === 0" class="text-gray-500 py-8">
        <p>No bookings found.</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="booking in bookingsData.data"
          :key="booking.id"
          class="border rounded-lg p-4 hover:shadow-md transition"
        >
          <div class="flex justify-between items-start">
            <div>
              <h3 class="font-semibold text-lg">{{ booking.title }}</h3>
              <p v-if="booking.description" class="text-gray-600 mt-1">
                {{ booking.description }}
              </p>
              <div class="mt-2 space-y-1 text-sm text-gray-500">
                <p>
                  <span class="font-medium">Start:</span>
                  {{ new Date(booking.start_time).toLocaleString() }}
                </p>
                <p>
                  <span class="font-medium">End:</span>
                  {{ new Date(booking.end_time).toLocaleString() }}
                </p>
                <p v-if="booking.client">
                  <span class="font-medium">Client:</span>
                  {{ booking.client.name }}
                </p>
              </div>
            </div>
            <button
              @click="handleDelete(booking.id)"
              :disabled="deleteBooking.isPending"
              class="px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded disabled:opacity-50"
            >
              {{ deleteBooking.isPending ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination Info -->
      <div class="mt-4 text-sm text-gray-600">
        Page {{ bookingsData.current_page }} of {{ bookingsData.last_page }} ({{
          bookingsData.total
        }}
        total)
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useBookings, useDeleteBooking } from '../hooks/useBookings';

const { data: bookingsData, isLoading, isError, error } = useBookings();
const deleteBooking = useDeleteBooking();

const handleDelete = (id: number) => {
  if (confirm('Are you sure you want to delete this booking?')) {
    deleteBooking.mutate(id, {
      onSuccess: () => {
        alert('Booking deleted successfully!');
      },
      onError: (error) => {
        alert(`Error deleting booking: ${error.message}`);
      },
    });
  }
};
</script>
