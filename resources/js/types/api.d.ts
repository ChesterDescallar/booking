import type { User } from './index';

export interface Client {
  id: number;
  user_id: number;
  name: string;
  email: string;
  phone: string;
  created_at: string;
  updated_at: string;
}

export interface Booking {
  id: number;
  user_id: number;
  client_id: number;
  title: string;
  description: string | null;
  start_time: string;
  end_time: string;
  created_at: string;
  updated_at: string;
  user?: User;
  client?: Client;
}

export interface CreateBookingInput {
  client_id: number;
  title: string;
  description?: string;
  start_time: string;
  end_time: string;
}

export interface UpdateBookingInput {
  client_id?: number;
  title?: string;
  description?: string;
  start_time?: string;
  end_time?: string;
}

export interface CreateClientInput {
  name: string;
  email: string;
  phone: string;
}

export interface UpdateClientInput {
  name?: string;
  email?: string;
  phone?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface WeeklyBooking {
  date: string;
  bookings: Booking[];
}
