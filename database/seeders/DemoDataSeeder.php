<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 demo users
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        $createdUsers = collect($users)->map(function ($userData) {
            return User::create($userData);
        });

        // Create 5 demo clients
        $clients = [
            [
                'name' => 'Acme Corporation',
                'email' => 'contact@acme.com',
                'phone' => '+1 (555) 123-4567',
            ],
            [
                'name' => 'Tech Solutions Inc',
                'email' => 'info@techsolutions.com',
                'phone' => '+1 (555) 234-5678',
            ],
            [
                'name' => 'Global Enterprises',
                'email' => 'hello@globalenterprises.com',
                'phone' => '+1 (555) 345-6789',
            ],
            [
                'name' => 'Innovate Labs',
                'email' => 'contact@innovatelabs.com',
                'phone' => '+1 (555) 456-7890',
            ],
            [
                'name' => 'Creative Studios',
                'email' => 'info@creativestudios.com',
                'phone' => '+1 (555) 567-8901',
            ],
        ];

        $createdClients = collect($clients)->map(function ($clientData) {
            return Client::create($clientData);
        });

        // Create some demo bookings
        $bookings = [
            [
                'user_id' => $createdUsers[0]->id,
                'client_id' => $createdClients[0]->id,
                'title' => 'Initial Consultation',
                'description' => 'Discuss project requirements and timeline',
                'start_time' => Carbon::now()->addDays(1)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(1)->setTime(10, 30),
            ],
            [
                'user_id' => $createdUsers[1]->id,
                'client_id' => $createdClients[1]->id,
                'title' => 'Project Review Meeting',
                'description' => 'Review progress and discuss next steps',
                'start_time' => Carbon::now()->addDays(2)->setTime(14, 0),
                'end_time' => Carbon::now()->addDays(2)->setTime(15, 0),
            ],
            [
                'user_id' => $createdUsers[2]->id,
                'client_id' => $createdClients[2]->id,
                'title' => 'Technical Workshop',
                'description' => 'Training session on new system features',
                'start_time' => Carbon::now()->addDays(3)->setTime(10, 0),
                'end_time' => Carbon::now()->addDays(3)->setTime(12, 0),
            ],
            [
                'user_id' => $createdUsers[0]->id,
                'client_id' => $createdClients[3]->id,
                'title' => 'Design Presentation',
                'description' => 'Present initial design concepts',
                'start_time' => Carbon::now()->addDays(4)->setTime(15, 0),
                'end_time' => Carbon::now()->addDays(4)->setTime(16, 30),
            ],
            [
                'user_id' => $createdUsers[3]->id,
                'client_id' => $createdClients[4]->id,
                'title' => 'Budget Discussion',
                'description' => 'Review project budget and resource allocation',
                'start_time' => Carbon::now()->addDays(5)->setTime(11, 0),
                'end_time' => Carbon::now()->addDays(5)->setTime(12, 0),
            ],
            [
                'user_id' => $createdUsers[4]->id,
                'client_id' => $createdClients[0]->id,
                'title' => 'Sprint Planning',
                'description' => 'Plan tasks for the upcoming sprint',
                'start_time' => Carbon::now()->addDays(7)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(7)->setTime(11, 0),
            ],
            [
                'user_id' => $createdUsers[1]->id,
                'client_id' => $createdClients[2]->id,
                'title' => 'Client Onboarding',
                'description' => 'Welcome new client and set expectations',
                'start_time' => Carbon::now()->addDays(8)->setTime(13, 0),
                'end_time' => Carbon::now()->addDays(8)->setTime(14, 30),
            ],
        ];

        foreach ($bookings as $bookingData) {
            Booking::create($bookingData);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Users created: 5 (password for all: "password")');
        $this->command->info('Clients created: 5');
        $this->command->info('Bookings created: 7');
    }
}
