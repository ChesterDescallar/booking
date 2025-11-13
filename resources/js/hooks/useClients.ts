import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';
import { toValue } from 'vue';
import { apiClient } from '../lib/api';
import type {
  Client,
  CreateClientInput,
  PaginatedResponse,
  UpdateClientInput,
} from '../types/api';

// Query Keys
export const clientKeys = {
  all: ['clients'] as const,
  lists: () => [...clientKeys.all, 'list'] as const,
  list: (filters: Record<string, unknown>) => [...clientKeys.lists(), filters] as const,
  details: () => [...clientKeys.all, 'detail'] as const,
  detail: (id: number) => [...clientKeys.details(), id] as const,
};

// API Functions
export const clientsApi = {
  getAll: async () => {
    const { data } = await apiClient.get<PaginatedResponse<Client>>('/clients');
    return data;
  },

  getById: async (id: number) => {
    const { data } = await apiClient.get<Client>(`/clients/${id}`);
    return data;
  },

  create: async (input: CreateClientInput) => {
    const { data } = await apiClient.post<Client>('/clients', input);
    return data;
  },

  update: async (id: number, input: UpdateClientInput) => {
    const { data } = await apiClient.put<Client>(`/clients/${id}`, input);
    return data;
  },

  delete: async (id: number) => {
    await apiClient.delete(`/clients/${id}`);
  },
};

// Hooks
export function useClients() {
  return useQuery({
    queryKey: clientKeys.lists(),
    queryFn: clientsApi.getAll,
  });
}

export function useClient(id: MaybeRefOrGetter<number>) {
  return useQuery({
    queryKey: clientKeys.detail(toValue(id)),
    queryFn: () => clientsApi.getById(toValue(id)),
    enabled: () => !!toValue(id),
  });
}

export function useCreateClient() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: clientsApi.create,
    onSuccess: () => {
      // Invalidate and refetch clients
      queryClient.invalidateQueries({ queryKey: clientKeys.lists() });
    },
  });
}

export function useUpdateClient() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, input }: { id: number; input: UpdateClientInput }) =>
      clientsApi.update(id, input),
    onSuccess: (_, variables) => {
      // Invalidate the specific client and lists
      queryClient.invalidateQueries({ queryKey: clientKeys.detail(variables.id) });
      queryClient.invalidateQueries({ queryKey: clientKeys.lists() });
    },
  });
}

export function useDeleteClient() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: clientsApi.delete,
    onSuccess: () => {
      // Invalidate all clients lists
      queryClient.invalidateQueries({ queryKey: clientKeys.lists() });
    },
  });
}
