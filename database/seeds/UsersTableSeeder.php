<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'id' => 1,
            'user_id' => 0,
            'domain' => 'www.dollarexch.com',
            'name' => 'Admin',
            'user_name' => "admin",
            'email' => "admin@gmail.com",
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'mobile' => '1234567890',
            'partnership' => 100.00,
            'city' => 'Ahmedabad',
            'expose' => null,
            'limit' => 1000000000.00,
            'used_limit' => 0.00,
            'expense' => 0.00,
            'extra_delay' => null,
            'min_bet' => null,
            'max_bet' => null,
            'expose_limit' => null,
            'is_admin' => 'Yes',
            'is_betting_now' => 'No',
            'level' => 1,
            'upper_level_expense' => 0
        ];

        $userObj = User::findOrNew($user['id']);
        $userObj->id = $user['id'];
        $userObj->user_id = $user['user_id'];
        $userObj->domain = $user['domain'];
        $userObj->name = $user['name'];
        $userObj->user_name = $user['user_name'];
        $userObj->email = $user['email'];
        $userObj->password = $user['password'];
        $userObj->mobile = $user['mobile'];
        $userObj->partnership = $user['partnership'];
        $userObj->city = $user['city'];
        $userObj->expose = $user['expose'];
        $userObj->limit = $user['limit'];
        $userObj->used_limit = $user['used_limit'];
        $userObj->expense = $user['expense'];
        $userObj->extra_delay = $user['extra_delay'];
        $userObj->min_bet = $user['min_bet'];
        $userObj->max_bet = $user['max_bet'];
        $userObj->expose_limit = $user['expose_limit'];
        $userObj->is_admin = $user['is_admin'];
        $userObj->is_betting_now = $user['is_betting_now'];
        $userObj->level = $user['level'];
        $userObj->upper_level_expense = $user['upper_level_expense'];
        $userObj->save();
    }
}
