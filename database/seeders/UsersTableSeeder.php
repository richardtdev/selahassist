<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sermonassist.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        
        // Create admin team
        $adminTeam = Team::forceCreate([
            'user_id' => $admin->id,
            'name' => 'Admin Team',
            'personal_team' => true,
        ]);
        
        $admin->current_team_id = $adminTeam->id;
        $admin->save();
        
        // Create test pastor user
        $pastor = User::create([
            'name' => 'Test Pastor',
            'email' => 'pastor@sermonassist.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        
        // Create pastor team
        $pastorTeam = Team::forceCreate([
            'user_id' => $pastor->id,
            'name' => 'Pastors Team',
            'personal_team' => true,
        ]);
        
        $pastor->current_team_id = $pastorTeam->id;
        $pastor->save();
    }
}
