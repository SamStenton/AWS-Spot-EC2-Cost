<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Aws\Ec2\Ec2Client;
use Aws;

class AWSController extends Controller
{
    public function getCost(Request $request)
    {


            $credentials = new Aws\Credentials\Credentials(env('AWS_KEY'), env('AWS_SECRET'));
            $client = Ec2Client::factory(array(
                'credentials' => $credentials,
                'version' => 'latest',
                'region'  => $request->input('region'),
            ));


            $terminate = Carbon::parse($request->input('terminateTime'));
            $terminateTime = Carbon::parse($request->input('terminateTime'));

            $terminate->minute = 00;
            $terminate->second = 00;
            $terminate->addHour();  
     
            $launch = Carbon::parse($request->input('launchTime'));
            $launchTime = Carbon::parse($request->input('launchTime'));
 
            $launch->minute = 00;
            $launch->second = 00;

            //$client = \AWS::createClient('ec2');
            $result = $client->describeSpotPriceHistory([
                'AvailabilityZone' => $request->input('availabilityZone'),
                'DryRun' => false,
                'StartTime' => $launch,
                'EndTime' => $terminate,
                'InstanceTypes' => [$request->input('instanceType')],
                'ProductDescriptions' => ['Linux/UNIX']
            ]);

            $total_cost = 0.0;
            $total_seconds = $launch->diffInSeconds($terminate);
            $total_hours = $total_seconds / (60*60);
            $last_time = $terminate;
            $computed_seconds = 0;

            foreach ($result['SpotPriceHistory'] as $price) {
                $price['SpotPrice'] = floatval($price['SpotPrice']);
                $available_seconds = new Carbon($last_time = $price['Timestamp']);
                $available_seconds = $available_seconds->diffInSeconds(Carbon::createFromTimestamp(0));
                $remaining_seconds = $total_seconds - $computed_seconds; 
                $used_seconds = min($available_seconds, $remaining_seconds);

                $total_cost = $total_cost + ($price['SpotPrice'] / (60*60)) * $used_seconds;
                $computed_seconds = $computed_seconds + $used_seconds;

                $last_time = $price['Timestamp'];
            }


            return Response([
                'TotalCost' => $total_cost,
                'PaidHours' => $launch->diffInSeconds($terminate) / (60*60),
                'ActualHours' => $launchTime->diffInSeconds($terminateTime) / (60*60)
        
                ]);
    }
}