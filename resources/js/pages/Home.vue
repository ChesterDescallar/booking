<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { DateTimePicker } from '@/components/ui/date-picker'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

// API base URL
const API_BASE = '/dashboard/projects/bookingapplication/booking/public/api'

interface User {
  id: number
  name: string
  email: string
}

interface Client {
  id: number
  name: string
  email: string | null
  phone: string | null
  bookings_count?: number
}

interface Booking {
  id: number
  user_id: number
  title: string
  description: string | null
  start_time: string
  end_time: string
  client_id: number
  user?: User
  client?: Client
}

// Helper function to get CSRF token
const getCsrfToken = (): string => {
  const token = document.head.querySelector('meta[name="csrf-token"]')
  return token ? (token as HTMLMetaElement).content : ''
}

// Helper function to make authenticated requests
const fetchWithCsrf = async (url: string, options: RequestInit = {}) => {
  const headers = {
    'Accept': 'application/json',
    'X-CSRF-TOKEN': getCsrfToken(),
    ...options.headers,
  }
  return fetch(url, { ...options, headers })
}

const bookings = ref<Booking[]>([])
const clients = ref<Client[]>([])
const users = ref<User[]>([])
const isLoadingBookings = ref(false)
const isLoadingClients = ref(false)
const activeTab = ref<'bookings' | 'clients'>('bookings')
const error = ref<string | null>(null)
const success = ref<string | null>(null)

// Weekly filter
const selectedWeekDate = ref<Date | null>(null)
const selectedWeekDateString = ref<string>('')
const weekInfo = ref<{ week_start: string; week_end: string } | null>(null)

// Booking form
const showBookingDialog = ref(false)
const bookingFormError = ref<string | null>(null)
const bookingForm = ref({
  id: null as number | null,
  user_id: '',
  title: '',
  description: '',
  client_id: '',
  start_time: '',
  end_time: '',
})

// Client form
const showClientDialog = ref(false)
const clientForm = ref({
  id: null as number | null,
  name: '',
  email: '',
  phone: '',
})

// Delete confirmation
const showDeleteDialog = ref(false)
const deleteItem = ref<{ type: 'booking' | 'client'; id: number } | null>(null)

const fetchBookings = async () => {
  isLoadingBookings.value = true
  error.value = null
  try {
    let url = `${API_BASE}/bookings`

    // If a week is selected, add it as a query parameter
    if (selectedWeekDate.value) {
      const weekDate = selectedWeekDate.value.toISOString().split('T')[0]
      url += `?week=${weekDate}`
    }

    const response = await fetchWithCsrf(url)
    if (!response.ok) throw new Error('Failed to fetch bookings')

    const data = await response.json()

    // Handle both formats (direct array or object with bookings property)
    if (Array.isArray(data)) {
      bookings.value = data
      weekInfo.value = null
    } else {
      bookings.value = data.bookings || []
      weekInfo.value = {
        week_start: data.week_start,
        week_end: data.week_end,
      }
    }
  } catch (e: any) {
    error.value = e.message || 'Failed to load bookings'
  } finally {
    isLoadingBookings.value = false
  }
}

const fetchClients = async () => {
  isLoadingClients.value = true
  error.value = null
  try {
    const response = await fetchWithCsrf(`${API_BASE}/clients`)
    if (!response.ok) throw new Error('Failed to fetch clients')
    clients.value = await response.json()
  } catch (e: any) {
    error.value = e.message || 'Failed to load clients'
  } finally {
    isLoadingClients.value = false
  }
}

const fetchUsers = async () => {
  try {
    const response = await fetchWithCsrf(`${API_BASE}/users`)
    if (!response.ok) throw new Error('Failed to fetch users')
    users.value = await response.json()
  } catch (e: any) {
    console.error('Failed to load users:', e.message)
  }
}

const saveBooking = async () => {
  bookingFormError.value = null
  try {
    const url = bookingForm.value.id
      ? `${API_BASE}/bookings/${bookingForm.value.id}`
      : `${API_BASE}/bookings`
    const method = bookingForm.value.id ? 'PUT' : 'POST'

    const response = await fetchWithCsrf(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        user_id: parseInt(bookingForm.value.user_id),
        title: bookingForm.value.title,
        description: bookingForm.value.description || null,
        client_id: parseInt(bookingForm.value.client_id),
        start_time: bookingForm.value.start_time,
        end_time: bookingForm.value.end_time,
      }),
    })

    if (!response.ok) {
      const data = await response.json()
      // Extract validation errors from Laravel response
      if (data.errors) {
        // Get the first error message from the errors object
        const firstError = Object.values(data.errors)[0]
        throw new Error(Array.isArray(firstError) ? firstError[0] : firstError)
      }
      throw new Error(data.message || 'Failed to save booking')
    }

    success.value = bookingForm.value.id
      ? 'Booking updated successfully!'
      : 'Booking created successfully!'
    showBookingDialog.value = false
    resetBookingForm()
    await fetchBookings()
  } catch (e: any) {
    bookingFormError.value = e.message || 'Failed to save booking'
  }
}

const saveClient = async () => {
  error.value = null
  success.value = null
  try {
    const url = clientForm.value.id
      ? `${API_BASE}/clients/${clientForm.value.id}`
      : `${API_BASE}/clients`
    const method = clientForm.value.id ? 'PUT' : 'POST'

    const response = await fetchWithCsrf(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name: clientForm.value.name,
        email: clientForm.value.email || null,
        phone: clientForm.value.phone || null,
      }),
    })

    if (!response.ok) {
      const data = await response.json()
      throw new Error(data.message || 'Failed to save client')
    }

    success.value = clientForm.value.id
      ? 'Client updated successfully!'
      : 'Client created successfully!'
    showClientDialog.value = false
    resetClientForm()
    await fetchClients()
  } catch (e: any) {
    error.value = e.message || 'Failed to save client'
  }
}

const confirmDelete = async () => {
  if (!deleteItem.value) return

  error.value = null
  success.value = null
  const itemType = deleteItem.value.type
  try {
    const url =
      deleteItem.value.type === 'booking'
        ? `${API_BASE}/bookings/${deleteItem.value.id}`
        : `${API_BASE}/clients/${deleteItem.value.id}`

    const response = await fetchWithCsrf(url, {
      method: 'DELETE',
    })

    if (!response.ok) throw new Error(`Failed to delete ${deleteItem.value.type}`)

    success.value = `${deleteItem.value.type === 'booking' ? 'Booking' : 'Client'} deleted successfully!`
    showDeleteDialog.value = false
    deleteItem.value = null

    if (itemType === 'booking') {
      await fetchBookings()
    } else {
      await fetchClients()
    }
  } catch (e: any) {
    error.value = e.message || 'Failed to delete item'
  }
}

const editBooking = (booking: Booking) => {
  bookingForm.value = {
    id: booking.id,
    user_id: booking.user_id.toString(),
    title: booking.title,
    description: booking.description || '',
    client_id: booking.client_id.toString(),
    start_time: booking.start_time.slice(0, 16),
    end_time: booking.end_time.slice(0, 16),
  }
  showBookingDialog.value = true
}

const editClient = (client: Client) => {
  clientForm.value = {
    id: client.id,
    name: client.name,
    email: client.email || '',
    phone: client.phone || '',
  }
  showClientDialog.value = true
}

const openDeleteDialog = (type: 'booking' | 'client', id: number) => {
  deleteItem.value = { type, id }
  showDeleteDialog.value = true
}

const resetBookingForm = () => {
  bookingFormError.value = null
  bookingForm.value = {
    id: null,
    user_id: '',
    title: '',
    description: '',
    client_id: '',
    start_time: '',
    end_time: '',
  }
}

const resetClientForm = () => {
  clientForm.value = {
    id: null,
    name: '',
    email: '',
    phone: '',
  }
}

const formatDateTime = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const sortedBookings = computed(() => {
  return [...bookings.value].sort((a, b) => {
    return new Date(b.start_time).getTime() - new Date(a.start_time).getTime()
  })
})

const formatWeekRange = computed(() => {
  if (!weekInfo.value) return null

  const start = new Date(weekInfo.value.week_start)
  const end = new Date(weekInfo.value.week_end)

  const formatOptions: Intl.DateTimeFormatOptions = {
    month: 'short',
    day: 'numeric',
  }

  return `${start.toLocaleDateString('en-US', formatOptions)} - ${end.toLocaleDateString('en-US', formatOptions)}, ${end.getFullYear()}`
})

const clearWeekFilter = () => {
  selectedWeekDate.value = null
  selectedWeekDateString.value = ''
}

const handleWeekDateChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  selectedWeekDateString.value = input.value
  if (input.value) {
    selectedWeekDate.value = new Date(input.value + 'T00:00:00')
  } else {
    selectedWeekDate.value = null
  }
}

// Watch for changes in selected week date
watch(selectedWeekDate, () => {
  fetchBookings()
})

onMounted(() => {
  fetchBookings()
  fetchClients()
  fetchUsers()
})
</script>

<template>
  <Head title="Booking System" />

  <div class="min-h-screen bg-gray-50 p-6">
    <div class="mx-auto max-w-7xl">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Booking System</h1>
        <p class="mt-2 text-sm text-gray-600">
          Manage your bookings and clients efficiently
        </p>
      </div>

      <!-- Notifications -->
      <div v-if="error" class="mb-4 rounded-md bg-red-50 border border-red-200 p-4">
        <p class="text-sm text-red-800">{{ error }}</p>
      </div>

      <div v-if="success" class="mb-4 rounded-md bg-green-50 border border-green-200 p-4">
        <p class="text-sm text-green-800">{{ success }}</p>
      </div>

      <!-- Tabs -->
      <div class="mb-6 flex gap-2 border-b border-gray-200">
        <button
          @click="activeTab = 'bookings'"
          :class="[
            'px-4 py-2 text-sm font-medium transition-colors',
            activeTab === 'bookings'
              ? 'border-b-2 border-blue-500 text-blue-600'
              : 'text-gray-600 hover:text-gray-900',
          ]"
        >
          Bookings
        </button>
        <button
          @click="activeTab = 'clients'"
          :class="[
            'px-4 py-2 text-sm font-medium transition-colors',
            activeTab === 'clients'
              ? 'border-b-2 border-blue-500 text-blue-600'
              : 'text-gray-600 hover:text-gray-900',
          ]"
        >
          Clients
        </button>
      </div>

      <!-- Bookings Tab -->
      <div v-if="activeTab === 'bookings'">
        <!-- Week Filter -->
        <div class="mb-4 flex items-center gap-4">
          <div class="flex-1">
            <Label for="week-filter" class="mb-2 block text-sm font-medium">Filter by Week</Label>
            <div class="flex items-center gap-2">
              <div class="w-64">
                <Input
                  id="week-filter"
                  type="date"
                  v-model="selectedWeekDateString"
                  @change="handleWeekDateChange"
                  placeholder="Select a week date"
                />
              </div>
              <Button
                v-if="selectedWeekDate"
                variant="outline"
                @click="clearWeekFilter"
              >
                Clear Filter
              </Button>
            </div>
          </div>
          <div v-if="formatWeekRange" class="text-sm text-gray-600">
            Showing: <span class="font-medium">{{ formatWeekRange }}</span>
          </div>
        </div>

        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>Bookings</CardTitle>
                <CardDescription>Manage your scheduled appointments</CardDescription>
              </div>
              <Dialog v-model:open="showBookingDialog">
                <DialogTrigger as-child>
                  <Button @click="resetBookingForm">Add Booking</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-md">
                  <DialogHeader>
                    <DialogTitle>{{ bookingForm.id ? 'Edit' : 'Create' }} Booking</DialogTitle>
                    <DialogDescription>
                      Fill in the booking details below
                    </DialogDescription>
                  </DialogHeader>
                  <div class="space-y-4 py-4">
                    <div class="space-y-2">
                      <Label for="user">User</Label>
                      <Select v-model="bookingForm.user_id">
                        <SelectTrigger>
                          <SelectValue placeholder="Select a user" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectGroup>
                            <SelectItem
                              v-for="user in users"
                              :key="user.id"
                              :value="user.id.toString()"
                            >
                              {{ user.name }}
                            </SelectItem>
                          </SelectGroup>
                        </SelectContent>
                      </Select>
                    </div>
                    <div class="space-y-2">
                      <Label for="title">Title</Label>
                      <Input id="title" v-model="bookingForm.title" placeholder="Meeting title" />
                    </div>
                    <div class="space-y-2">
                      <Label for="client">Client</Label>
                      <Select v-model="bookingForm.client_id">
                        <SelectTrigger>
                          <SelectValue placeholder="Select a client" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectGroup>
                            <SelectItem
                              v-for="client in clients"
                              :key="client.id"
                              :value="client.id.toString()"
                            >
                              {{ client.name }}
                            </SelectItem>
                          </SelectGroup>
                        </SelectContent>
                      </Select>
                    </div>
                    <DateTimePicker
                      id="start_time"
                      v-model="bookingForm.start_time"
                      label="Start Time"
                    />
                    <DateTimePicker
                      id="end_time"
                      v-model="bookingForm.end_time"
                      label="End Time"
                    />
                    <div class="space-y-2">
                      <Label for="description">Description (Optional)</Label>
                      <Textarea
                        id="description"
                        v-model="bookingForm.description"
                        placeholder="Booking details"
                      />
                    </div>
                  </div>
                  <div v-if="bookingFormError" class="rounded-md bg-red-50 border border-red-200 p-3 mb-4">
                    <p class="text-sm text-red-800">{{ bookingFormError }}</p>
                  </div>
                  <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showBookingDialog = false">Cancel</Button>
                    <Button @click="saveBooking">Save</Button>
                  </div>
                </DialogContent>
              </Dialog>
            </div>
          </CardHeader>
          <CardContent>
            <div v-if="isLoadingBookings" class="py-8 text-center text-sm text-gray-500">
              Loading bookings...
            </div>
            <div v-else-if="bookings.length === 0" class="py-8 text-center text-sm text-gray-500">
              No bookings found. Create your first booking!
            </div>
            <Table v-else>
              <TableHeader>
                <TableRow>
                  <TableHead>Title</TableHead>
                  <TableHead>User</TableHead>
                  <TableHead>Client</TableHead>
                  <TableHead>Start Time</TableHead>
                  <TableHead>End Time</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="booking in sortedBookings" :key="booking.id">
                  <TableCell class="font-medium">{{ booking.title }}</TableCell>
                  <TableCell>{{ booking.user?.name || 'N/A' }}</TableCell>
                  <TableCell>{{ booking.client?.name || 'N/A' }}</TableCell>
                  <TableCell>{{ formatDateTime(booking.start_time) }}</TableCell>
                  <TableCell>{{ formatDateTime(booking.end_time) }}</TableCell>
                  <TableCell class="text-right">
                    <div class="flex justify-end gap-2">
                      <Button variant="outline" size="sm" @click="editBooking(booking)">
                        Edit
                      </Button>
                      <Button
                        variant="destructive"
                        size="sm"
                        @click="openDeleteDialog('booking', booking.id)"
                      >
                        Delete
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>

      <!-- Clients Tab -->
      <div v-if="activeTab === 'clients'">
        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>Clients</CardTitle>
                <CardDescription>Manage your client contacts</CardDescription>
              </div>
              <Dialog v-model:open="showClientDialog">
                <DialogTrigger as-child>
                  <Button @click="resetClientForm">Add Client</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-md">
                  <DialogHeader>
                    <DialogTitle>{{ clientForm.id ? 'Edit' : 'Create' }} Client</DialogTitle>
                    <DialogDescription>
                      Fill in the client details below
                    </DialogDescription>
                  </DialogHeader>
                  <div class="space-y-4 py-4">
                    <div class="space-y-2">
                      <Label for="name">Client Name</Label>
                      <Input id="name" v-model="clientForm.name" placeholder="ABC Corporation" />
                    </div>
                    <div class="space-y-2">
                      <Label for="email">Email (Optional)</Label>
                      <Input
                        id="email"
                        v-model="clientForm.email"
                        type="email"
                        placeholder="client@example.com"
                      />
                    </div>
                    <div class="space-y-2">
                      <Label for="phone">Phone (Optional)</Label>
                      <Input id="phone" v-model="clientForm.phone" placeholder="555-1234" />
                    </div>
                  </div>
                  <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showClientDialog = false">Cancel</Button>
                    <Button @click="saveClient">Save</Button>
                  </div>
                </DialogContent>
              </Dialog>
            </div>
          </CardHeader>
          <CardContent>
            <div v-if="isLoadingClients" class="py-8 text-center text-sm text-gray-500">
              Loading clients...
            </div>
            <div v-else-if="clients.length === 0" class="py-8 text-center text-sm text-gray-500">
              No clients found. Add your first client!
            </div>
            <Table v-else>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Phone</TableHead>
                  <TableHead>Bookings</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="client in clients" :key="client.id">
                  <TableCell class="font-medium">{{ client.name }}</TableCell>
                  <TableCell>{{ client.email || 'N/A' }}</TableCell>
                  <TableCell>{{ client.phone || 'N/A' }}</TableCell>
                  <TableCell>{{ client.bookings_count || 0 }}</TableCell>
                  <TableCell class="text-right">
                    <div class="flex justify-end gap-2">
                      <Button variant="outline" size="sm" @click="editClient(client)">
                        Edit
                      </Button>
                      <Button
                        variant="destructive"
                        size="sm"
                        @click="openDeleteDialog('client', client.id)"
                      >
                        Delete
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>

      <!-- Delete Confirmation Dialog -->
      <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Confirm Delete</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete this {{ deleteItem?.type }}? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <div class="flex justify-end gap-2 pt-4">
            <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
            <Button variant="destructive" @click="confirmDelete">Delete</Button>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </div>
</template>
