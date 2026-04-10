<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin OFPPT',
                'email'    => 'admin@ofppt.ma',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Gestionnaire OFPPT',
                'email'    => 'gestionnaire@ofppt.ma',
                'password' => Hash::make('gestionnaire123'),
                'role'     => 'gestionnaire',
            ],
            [
                'name'     => 'Formateur OFPPT',
                'email'    => 'formateur@ofppt.ma',
                'password' => Hash::make('formateur123'),
                'role'     => 'formateur',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Users Seeder terminé !');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['admin',        'admin@ofppt.ma',        'admin123'],
                ['gestionnaire', 'gestionnaire@ofppt.ma', 'gestionnaire123'],
                ['formateur',    'formateur@ofppt.ma',    'formateur123'],
            ]
        );
    }
}