import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';
import { toValue } from 'vue';
import { apiClient } from '../lib/api';
import type {
  Booking,
  CreateBookingInput,
  PaginatedResponse,
  UpdateBookingInput,
  WeeklyBooking,
} from '../types/api';

// Query Keys
export const bookingKeys = {
  all: ['bookings'] as const,
  lists: () => [...bookingKeys.all, 'list'] as const,
  list: (filters: Record<string, unknown>) => [...bookingKeys.lists(), filters] as const,
  details: () => [...bookingKeys.all, 'detail'] as const,
  detail: (id: number) => [...bookingKeys.details(), id] as const,
  weekly: () => [...bookingKeys.all, 'weekly'] as const,
};

// API Functions
export const bookingsApi = {
  getAll: async () => {
    const { data } = await apiClient.get<PaginatedResponse<Booking>>('/bookings');
    return data;
  },

  getById: async (id: number) => {
    const { data } = await apiClient.get<Booking>(`/bookings/${id}`);
    return data;
  },

  getWeekly: async () => {
    const { data } = await apiClient.get<WeeklyBooking[]>('/bookings/weekly');
    return data;
  },

  create: async (input: CreateBookingInput) => {
    const { data } = await apiClient.post<Booking>('/bookings', input);
    return data;
  },

  update: async (id: number, input: UpdateBookingInput) => {
    const { data } = await apiClient.put<Booking>(`/bookings/${id}`, input);
    return data;
  },

  delete: async (id: number) => {
    await apiClient.delete(`/bookings/${id}`);
  },
};

// Hooks
export function useBookings() {
  return useQuery({
    queryKey: bookingKeys.lists(),
    queryFn: bookingsApi.getAll,
  });
}

export function useBooking(id: MaybeRefOrGetter<number>) {
  return useQuery({
    queryKey: bookingKeys.detail(toValue(id)),
    queryFn: () => bookingsApi.getById(toValue(id)),
    enabled: () => !!toValue(id),
  });
}

export function useWeeklyBookings() {
  return useQuery({
    queryKey: bookingKeys.weekly(),
    queryFn: bookingsApi.getWeekly,
  });
}

export function useCreateBooking() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: bookingsApi.create,
    onSuccess: () => {
      // Invalidate and refetch bookings
      queryClient.invalidateQueries({ queryKey: bookingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: bookingKeys.weekly() });
    },
  });
}

export function useUpdateBooking() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, input }: { id: number; input: UpdateBookingInput }) =>
      bookingsApi.update(id, input),
    onSuccess: (_, variables) => {
      // Invalidate the specific booking and lists
      queryClient.invalidateQueries({ queryKey: bookingKeys.detail(variables.id) });
      queryClient.invalidateQueries({ queryKey: bookingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: bookingKeys.weekly() });
    },
  });
}

export function useDeleteBooking() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: bookingsApi.delete,
    onSuccess: () => {
      // Invalidate all bookings lists
      queryClient.invalidateQueries({ queryKey: bookingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: bookingKeys.weekly() });
    },
  });
}
