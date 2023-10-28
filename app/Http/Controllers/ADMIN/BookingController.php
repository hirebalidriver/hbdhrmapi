<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Bookings;
use App\Models\Guides;
use App\Models\PackageRelations;
use App\Models\Packages;
use App\Models\Tours;
use App\Services\FCMService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'date' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'ASC');

        $bookings = Bookings::with('packages', 'guides', 'user', 'options', 'notification')->orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);

        if($bookings){
            return ResponseFormatter::success($bookings, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'date' => ['required'],
            'is_custom' => ['required'],
            'ref_id' => ['unique:bookings,ref_id']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $user = Auth::user();

        $option = Tours::where('id', $request->tour_id)->first();

        $count = count($request->date);
        $sortedDate = Arr::sort($request->date);

        // dd(head($sortedDate));

        $date_start = Carbon::parse(head($sortedDate))->format('Y-m-d');

        if($count > 1){
            $isMulti = 1;
            $dateAll = $sortedDate;
            $refId0 = $request->ref_id.'_MULTIDAYS0';
        }else{
            $isMulti = 0;
            $dateAll = null;
            $refId0 = $request->ref_id;
        }

        if($request->is_custom) {

            $create = Bookings::insertGetId([
                'ref_id' => $refId0,
                'package_id' => 0,
                'tour_id' => 0,
                'guide_id' => 0,
                'date' => $date_start,
                'time' => $request->time,
                'supplier' => $request->supplier,
                'status' => 2,
                'note' => $request->note,
                'name' => $request->name,
                'phone' => $request->phone,
                'hotel' => $request->hotel,
                'status_payment' => $request->status_payment,
                'collect' => $request->collect,
                'created_by' => $user->id,
                'country' => $request->country,
                'adult' => $request->adult,
                'child' => $request->child,
                'price' => $request->price,
                'guide_fee' => $request->guide_fee,
                'down_payment' => $request->down_payment,
                'is_custom' => $request->is_custom,
                'custom' => $request->custom,
                'is_multi_days' => $isMulti,
            ]);

            if($count > 1){
                $sortedDate = array_diff($sortedDate, array($date_start));
                $n = 1;
                foreach($sortedDate as $item){
                    $date = Carbon::parse($item)->format('Y-m-d');
                    $create = Bookings::create([
                        'ref_id' => $request->ref_id.'_MULTIDAYS'.$n,
                        'package_id' => 0,
                        'tour_id' => 0,
                        'guide_id' => 0,
                        'date' => $date,
                        'time' => $request->time,
                        'supplier' => $request->supplier,
                        'status' => 2,
                        'note' => $request->note,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'hotel' => $request->hotel,
                        'status_payment' => $request->status_payment,
                        'collect' => 0,
                        'created_by' => $user->id,
                        'country' => $request->country,
                        'adult' => $request->adult,
                        'child' => $request->child,
                        'price' => 0,
                        'guide_fee' => 0,
                        'down_payment' => 0,
                        'is_custom' => $request->is_custom,
                        'custom' => $request->custom,
                        'is_multi_days' => $create,
                    ]);
                    $n++;
                }

            }

        }else{
            $create = Bookings::insertGetId([
                'ref_id' => $refId0,
                'package_id' => $request->package_id,
                'tour_id' => $request->tour_id,
                'guide_id' => 0,
                'date' => $date_start,
                'time' => $request->time,
                'supplier' => $request->supplier,
                'status' => 2,
                'note' => $request->note,
                'name' => $request->name,
                'phone' => $request->phone,
                'hotel' => $request->hotel,
                'status_payment' => $request->status_payment,
                'collect' => $request->collect,
                'created_by' => $user->id,
                'country' => $request->country,
                'adult' => $request->adult,
                'child' => $request->child,
                'price' => $request->price,
                'guide_fee' => $option->guide_fee,
                'down_payment' => $request->down_payment,
                'is_multi_days' => $isMulti,
                'is_custom' => $request->is_custom,
            ]);

            if($count > 1){
                $sortedDate = array_diff($sortedDate, array($date_start));
                $n = 1;
                foreach($sortedDate as $item){
                    $date = Carbon::parse($item)->format('Y-m-d');
                    $create = Bookings::create([
                        'ref_id' => $request->ref_id.'_MULTIDAYS'.$n,
                        'package_id' => $request->package_id,
                        'tour_id' => $request->tour_id,
                        'guide_id' => 0,
                        'date' => $date_start,
                        'time' => $request->time,
                        'supplier' => $request->supplier,
                        'status' => 2,
                        'note' => $request->note,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'hotel' => $request->hotel,
                        'status_payment' => $request->status_payment,
                        'collect' => 0,
                        'created_by' => $user->id,
                        'country' => $request->country,
                        'adult' => $request->adult,
                        'child' => $request->child,
                        'price' => 0,
                        'guide_fee' => 0,
                        'down_payment' => 0,
                        'is_multi_days' => $create,
                        'is_custom' => $request->is_custom,
                    ]);
                    $n++;
                }

            }
        }


        if($create) {
            return ResponseFormatter::success($create, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
        $reference_array = array();

        foreach($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, $direction, $array);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'date' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('id',$request->id)->first();
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $dateOld = Carbon::parse($booking->date)->format('Y-m-d');

        if($date != $dateOld) {
            $booking->guide_id = 0;
            $booking->date = $date;
            Availability::where('booking_id', $booking->id)->delete();
        }

        $booking->package_id = $request->package_id;
        $booking->time = $request->time;
        $booking->supplier = $request->supplier;
        $booking->status = $request->status;
        $booking->note = $request->note;
        $booking->ref_id = $request->ref_id;
        $booking->name = $request->name;
        $booking->phone = $request->phone;
        $booking->hotel = $request->hotel;
        $booking->status_payment = $request->status_payment;
        $booking->collect = $request->collect;
        $booking->tour_id = $request->tour_id;
        $booking->country = $request->country;
        $booking->adult = $request->adult;
        $booking->child = $request->child;
        $booking->price = $request->price;    
        $booking->down_payment = $request->down_payment;

        $booking->guide_fee = $request->guide_fee;
        $booking->is_custom = $request->is_custom;

        if($request->is_custom) {
            
            $booking->custom = $request->custom;
        }else{
            $booking->custom = null;
        }

        if($request->is_multi_days > 1) {
            $booking->guide_fee = 0;
        }

        if($booking->save()) {
            return ResponseFormatter::success($booking, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function updateGuide(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'guide_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();
        try{

            $guide = Guides::find($request->guide_id);
            if(!$guide) return ResponseFormatter::error(null, 'guide not found');

            $booking = Bookings::find($request->id);
            if(!$booking) return ResponseFormatter::error(null, 'not found');

            $check = Availability::where('guide_id', $guide->id)
                                    ->whereDate('date', $booking->date)
                                    ->first();
            if($check) return ResponseFormatter::error(null, 'not available');

            $booking->guide_id = $request->guide_id;
            $booking->status = 6;

            $booking->save();

            Availability::where('booking_id', $booking->id)
                                    ->whereDate('date', $booking->date)
                                    ->delete();

            Availability::create([
                'guide_id' => $guide->id,
                'booking_id' => $booking->id,
                'date' => $booking->date,
                'note' => 'tour',
            ]);

            // TOUR AND OPTIONS
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

            DB::commit();

           

            return ResponseFormatter::success($booking, 'success');

        }catch(Exception $e){
            return ResponseFormatter::error($e, 'failed');
        }
    }

    public function updateStatus(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'status' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $check = Bookings::where('status', '>', 2)->first();
        if(!$check) return ResponseFormatter::error(null, 'change status failed');

        $booking = Bookings::find($request->id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $booking->status = $request->status;

        if($request->status == 1){

            $guide = Guides::find($booking->guide_id);
            if(!$guide) return ResponseFormatter::error(null, 'guide not found');

            // TOUR AND OPTIONS
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

            \App\Jobs\CancelBookingJob::dispatch($details);

        }

        if($booking->save()) {
            return ResponseFormatter::success($booking, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function delete(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        DB::beginTransaction();
            try{

            $booking = Bookings::where('id',$request->id)->first();
            if(!$booking) return ResponseFormatter::error(null, 'not found');


            if($booking->is_multi_days == 1){
                Bookings::where('is_multi_days', $booking->id)->delete();
            }

            $booking->delete();

            DB::commit();
            return ResponseFormatter::success(null, 'success');

        }catch(Exception $e){
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function find(Request $request)
    {
        $rules = [
            'id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('id', $request->id)
                    ->with('packages', 'guides', 'user', 'options', 'notification')
                    ->first();

        $multidays = null;
        if($booking->is_multi_days == 1) {
            $multidays = Bookings::where('is_multi_days', $booking->id)
                    ->get();
        }

        $data = [
            'data' => $booking,
            'multidays' => $multidays
        ];

        if($booking) {
            return ResponseFormatter::success($data, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function findByRefId(Request $request)
    {
        $rules = [
            'ref_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $booking = Bookings::where('ref_id', $request->ref_id)->get();

        if(!$booking) return ResponseFormatter::error(null, 'not found');

        if($booking) {
            return ResponseFormatter::success($booking, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }


    public function findByDate(Request $request)
    {
        $rules = [
            'date' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $pages = $request->pages != null ? $request->pages : 10;
        $sortBy = $request->sortby == null ? $sortBy = 'date' : $sortBy = $request->sortby;
        $direction =$request->input('direction', 'ASC');

        $find = Bookings::where('date', $request->date)->with('packages', 'guides', 'notification')
                            ->orderBy($sortBy, $direction)
                            ->paginate($pages);

        if($find) {
            return ResponseFormatter::success($find, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function filter(Request $request)
    {

        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->sortBy == null ? $sortBy = 'date' : $sortBy = $request->sortBy;
        $direction =$request->input('direction', 'ASC');

        if($request->date_from > $request->date_end){
            $start = $request->date_end;
            $end = $request->date_from;
        }else{
            $start = $request->date_from;
            $end = $request->date_end;
        }

        $guest_name = $request->guest_name;
        $supplier = $request->supplier;
        $status = $request->status;

        $multidays = null;

        if($request->ref_id != '' || $request->ref_id != null) {
            $find = Bookings::where('ref_id', $request->ref_id)
                    // ->where('is_multi_days', '<=', 1)
                    ->with('packages', 'guides', 'user', 'options', 'notification')
                    ->orderBy($sortBy, $direction)
                    ->paginate($per_page, ['*'], 'page', $page);
        }else{

            $find = Bookings::when($start, function($query) use ($start, $end){
                            return $query->whereBetween('date', [$start, $end]);
                        })
                        ->when($supplier, function($query) use ($supplier){
                            return $query->where('supplier', $supplier);
                        })
                        ->when($status, function($query) use ($status){
                            return $query->where('status', $status);
                        })
                        ->when($guest_name, function($query) use ($guest_name){
                            return $query->where('name', 'LIKE', '%'.$guest_name.'%');
                        })
                        // ->where('is_multi_days', '<=', 1)
                        ->with('packages', 'guides', 'user', 'options', 'notification')
                        ->orderBy($sortBy, $direction)
                        ->paginate($per_page, ['*'], 'page', $page);
        }

        if($find) {
            return ResponseFormatter::success($find, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }

    public function getOptions(Request $request)
    {
        $rules = [
            'package_id' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $options = DB::table('tours')->select('tours.*')
                        ->join('package_relations', 'package_relations.tour_id', 'tours.id')
                        ->where('package_relations.package_id', $request->package_id)
                        ->get();

        if($options) {
            return ResponseFormatter::success($options, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
    }
}
