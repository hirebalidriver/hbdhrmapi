<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Guides;
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

        //get a user to get the fcm_token that already sent. from mobile apps
        FCMService::send(
            $guide->fcm_token,
            [
                'title' => 'New Booking Hire Bali Driver',
                'body' => $booking->date->format('d M Y').' '.$booking->time->format('H:m'),
            ],
            [
                'title' => 'New Booking Hire Bali Driver',
                'body' => $booking->date->format('d M Y').' '.$booking->time->format('H:m'),
                'route' => '/',
            ]
        );

        $details = [
            'title' => "Booking Hire Bali Driver " . Carbon::parse($booking->date)->format('Y-m-d'),
            'to' => $guide->email,
            'name' => $guide->name,
            'ref_id' => $booking->ref_id,
            'package_id' => $booking->package_id,
            'option_id' => $booking->option_id,
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

        \App\Jobs\BookingMailJob::dispatch($details);

        $query = Notification::create([
            'booking_id' => $booking->id,
            'guide_id' => $guide->id,
        ]);

        if($query) {
            return ResponseFormatter::success(null, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }

    }
}
