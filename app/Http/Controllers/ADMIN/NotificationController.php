<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Guides;
use App\Models\Packages;
use App\Models\Tours;
use App\Models\Notification;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function selectGuide(Request $request)
    {
        $rules = [
            'booking_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::find($request->booking_id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $guide = Guides::find($booking->guide_id);
        if(!$guide) return ResponseFormatter::error(null, 'guide not found');

         // TOUR AND OPTIONS
         if($booking->is_custom){
            $package = $booking->custom;
            $option = '';

            $details = [
                'to' => $guide->email,
                'name' => $guide->name,
                'ref' => $booking->ref_id,
                'package' => $package,
                'option' => $option,
                'date' => Carbon::parse($booking->date)->format('M d Y'),
                'time' => $booking->time->format('H:m'),
                'supplier' => $booking->supplier,
                'note' => $booking->note,
                'guestName' => $booking->name,
                'phone' => $booking->phone,
                'hotel' => $booking->hotel,
                'status_payment' => $booking->status_payment,
                'collect' => $booking->collect,
                'country' => $booking->country,
                'adult' => $booking->adult,
                'child' => $booking->child,
                'price' => $booking->price,
            ];
         }else{
            $package = Packages::where('id', $booking->package_id)->first();
            $option = Tours::where('id', $booking->tour_id)->first();

            $details = [
                'to' => $guide->email,
                'name' => $guide->name,
                'ref' => $booking->ref_id,
                'package' => $package->title,
                'option' => $option->title,
                'date' => Carbon::parse($booking->date)->format('M d Y'),
                'time' => $booking->time->format('H:m'),
                'supplier' => $booking->supplier,
                'note' => $booking->note,
                'guestName' => $booking->name,
                'phone' => $booking->phone,
                'hotel' => $booking->hotel,
                'status_payment' => $booking->status_payment,
                'collect' => $booking->collect,
                'country' => $booking->country,
                'adult' => $booking->adult,
                'child' => $booking->child,
                'price' => $booking->price,
            ];
         }
         
        

        

        \App\Jobs\AssignGuideJob::dispatch($details);

        $selectNotif = Notification::where('booking_id', $booking->id)->first();
        if($selectNotif) {
            Notification::where('booking_id', $booking->id)->delete();
        }
        

        $query = Notification::create([
            'booking_id' => $booking->id,
            'guide_id' => $guide->id,
        ]);

        $booking = Bookings::where('id',$request->booking_id)->first();
        $booking->status = 6;
        $booking->save();

        if($query) {
            return ResponseFormatter::success(null, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
