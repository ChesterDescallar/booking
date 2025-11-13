<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test customer
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        // Create some clients for this customer
        $client1 = Client::create([
            'customer_id' => $customer->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '5551234567',
        ]);

        $client2 = Client::create([
            'customer_id' => $customer->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '5559876543',
        ]);

        $client3 = Client::create([
            'customer_id' => $customer->id,
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'phone' => '5555555555',
        ]);

        // Create some bookings
        Booking::create([
            'customer_id' => $customer->id,
            'client_id' => $client1->id,
            'title' => 'Initial Consultation',
            'description' => 'First meeting with John to discuss his project requirements',
            'start_time' => now()->addDays(1)->setHour(10)->setMinute(0),
            'end_time' => now()->addDays(1)->setHour(11)->setMinute(30),
        ]);

        Booking::create([
            'customer_id' => $customer->id,
            'client_id' => $client2->id,
            'title' => 'Project Review',
            'description' => 'Review project progress with Jane',
            'start_time' => now()->addDays(2)->setHour(14)->setMinute(0),
            'end_time' => now()->addDays(2)->setHour(15)->setMinute(0),
        ]);

        Booking::create([
            'customer_id' => $customer->id,
            'client_id' => $client3->id,
            'title' => 'Strategy Meeting',
            'description' => null,
            'start_time' => now()->addDays(3)->setHour(9)->setMinute(0),
            'end_time' => now()->addDays(3)->setHour(10)->setMinute(0),
        ]);
    }
}
