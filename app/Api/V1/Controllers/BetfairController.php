<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetfairController extends ApiController
{

    public static function testfuncron()
    {
        $sport_ids = ['1','2','4','7'];
        foreach ($sport_ids as $sport_id){
            $getTournament = self::getTournament($sport_id);
            $getTournament = json_decode($getTournament);
            Tournament::whereSportId($sport_id)->forceDelete();
            $attachment = $getTournament->attachments;
            if(isset($attachment->competitions)){
                foreach ($attachment->competitions as $tournament){
                    $tournamentData = new Tournament();
                    $tournamentData->sport_id = $sport_id;
                    $tournamentData->name = $tournament->name;
                    $tournamentData->tournament_id = $tournament->competitionId;
                    $tournamentData->save();
                }
            }
        }
    }


    public static function getTournament($sport_id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.betfair.com/www/sports/navigation/facet/v1/search?alt=json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"filter\":{\"eventTypeIds\":[$sport_id],\"productTypes\":[\"EXCHANGE\"],\"maxResults\":0},\"facets\":[{\"type\":\"COMPETITION\",\"maxValues\":0,\"skipValues\":0,\"applyNextTo\":0}]}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }

    public function getGamesByTournament(Request $request)
    {
        $tournament = Tournament::whereId($request->get('tournament_id'))->first();
        $tId = $tournament->tournament_id;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.betfair.com/www/sports/navigation/facet/v1/search?alt=json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"filter\":{\"competitionIds\":[$tId],\"attachments\":[\"EVENT\"]},\"currencyCode\":\"USD\",\"locale\":\"en\"}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }

    public function getMarketIdGameData(Request $request)
    {
        $gId = $request->get('event_id');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ips.betfair.com/inplayservice/v1/eventDetails?alt=json&eventIds=$gId&locale=en&regionCode=ASIA",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response);

        if(!empty($response)) {

            if ($response[0]->eventTypeId == 1) {
                //$response[0]->startTime = Carbon::parse($response[0]->startTime)->format('H:i:s');
                $response[0]->game_date = Carbon::parse($response[0]->startTime)->format('Y-m-d');
                $runners = $response[0]->runners;
                $runners = (array)$runners;

                $runnerNameArr = array();
                foreach (array_keys($runners) as $key => $value) {
                    if (preg_match('/^runner(\\d+)Name$/', $value, $matches)) {
                        $runnerNameArr[$key]['name'] = $runners[$value];
                        $runnerNameArr[$key]['id'] = $runners[str_replace('Name', 'SelectionId', $value)];
                    }
                    if (preg_match('/^drawName$/', $value, $matches)) {
                        $runnerNameArr[$key]['name'] = $runners[$value];
                        $runnerNameArr[$key]['id'] = $runners[str_replace('Name', 'SelectionId', $value)];
                    }
                }
                $response[0]->new_runners = $runnerNameArr;
            }

            /*if ($response[0]->eventTypeId == 2) {
                //$response[0]->startTime = Carbon::parse($response[0]->startTime)->format('H:i:s');
                $response[0]->game_date = Carbon::parse($response[0]->startTime)->format('Y-m-d');
                $runners = $response[0]->runners;
                $runners = (array)$runners;

                $runnerNameArr = array();
                foreach (array_keys($runners) as $key => $value) {
                    if (preg_match('/^runner(\\d+)Name$/', $value, $matches)) {
                        $runnerNameArr[$key]['name'] = $runners[$value];
                        $runnerNameArr[$key]['id'] = $runners[str_replace('Name', 'SelectionId', $runners[$value])];
                    }
                    if (preg_match('/^drawName$/', $value, $matches)) {
                        $runnerNameArr[$key]['name'] = $runners[$value];
                        $runnerNameArr[$key]['id'] = $runners[str_replace('Name', 'SelectionId', $value)];
                    }
                }
                $response[0]->new_runners = $runnerNameArr;
            }*/

            if ($response[0]->eventTypeId == 4 || $response[0]->eventTypeId == 2) {
                //$response[0]->start_time = Carbon::parse($response[0]->startTime)->format('H:i:s');
                $response[0]->game_date = Carbon::parse($response[0]->startTime)->format('Y-m-d H:i:s');
                $runners = $response[0]->runners;
                $runners = (array)$runners;

                $runnerNameArr = array();
                foreach (array_keys($runners) as $key => $value) {
                    if (preg_match('/^runner(\\d+)Name$/', $value, $matches)) {
                        $runnerNameArr[$key]['name'] = $runners[$value];
                        $runnerNameArr[$key]['id'] = $runners[str_replace('Name', 'SelectionId', $value)];
                    }
                    if (preg_match('/^drawName$/', $value, $matches)) {
                        $runnerNameArr[$key]['name'] = $runners[$value];
                        $runnerNameArr[$key]['id'] = $runners[str_replace('Name', 'SelectionId', $value)];
                    }
                }
                $response[0]->new_runners = $runnerNameArr;
            }

        }
        return $response;
    }









    public function getMarketBhav(Request $request)
    {
        $token = 'd8p7T+dEPzbsDOI6XoWq4asOHG1selyS1gOrdMCGTXI=';

        $market_id = '1.147790733'; //1.147304581

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://www.betfair.com/www/sports/exchange/readonly/v1/bymarket?currencyCode=EUR&locale=en&marketIds=" . $market_id . "&types=MARKET_STATE%2CRUNNER_STATE%2CRUNNER_EXCHANGE_PRICES_BEST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPHEADER => array(
                "cookie: ssoid=" . str_replace(' ', '+', $token)
            )
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;

    }



    public function object_to_array($obj) {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = $this->object_to_array($val);
            }
        }
        else $new = $obj;
        return $new;
    }


}
