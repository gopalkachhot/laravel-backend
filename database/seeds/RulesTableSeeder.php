<?php

use Illuminate\Database\Seeder;

class RulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sportList = [
            [ 'description'=>'If there is a technical issue due to which software is not working properly at that time we are not responsible for any losses.'],
            [ 'description'=>'In case of cheating and betting in unfair rates we will cancel the bet even after settling.'],
            [ 'description'=>'Local fancy based on Haar - Jeet.'],
            [ 'description'=>'Incomplete session will be cancelled but complete session will be settled. advance fancy e.g. player run , advance session , partnership etc. completed with full available over will be settled. If there is deduction in overs than all advance fancy & session will be cancelled . Player run in advance valid if batsman come in crease till 10 overs.'],
            [ 'description'=>'In case of match abandoned, cancelled, no result etc. completed sessions will be settled.'],
            [ 'description'=>'In case of a Tie match, FANCY BET WILL BE SETTLED.'],
            [ 'description'=>'Decision made on any bet by the administration will be final and the administration will not accept any claim for the same.'],
            [ 'description'=>'Please read the agreement carefully before betting. If you do not accept this agreement, do not place any bet'],
            [ 'description'=>'IMPORTANT FOR ALL USERS : If there is any change in rules than all users have to accept that without any claim .'],
            [ 'description'=>'Any player Retaired-Hurt and one ball is completed after that than all the betting on him will be valid.'],
            [ 'description'=>'This Is Not Betting Site.This Is Just For Fun.'],
            [ 'description'=>'In any match with LOW VOLUME small trade bets (cheating bets) will be cancelled .'],
            [ 'description'=>'TENNIS - Unmatched bets are not allowed in tennis . 1 SET must be completed for bet to stands . If Any player retires before 1 SET completed than Bets will be cancelled . After 1 Set completed all bets are valid.'],
        ];

        foreach ($sportList as $rule){
            $rulesObj = new \App\Rules();
            $rulesObj->description = $rule['description'];
            $rulesObj->save();
        }
    }
}
