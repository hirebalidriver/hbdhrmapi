<?php

namespace App\Http\Controllers\ADMIN;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Guides;
use App\Models\PackageRelations;
use App\Models\Packages;
use App\Models\Tours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'pages' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $pages = $request->pages != null ? $request->pages : 10;
        $sortBy = $request->sortby == null ? $sortBy = 'id' : $sortBy = $request->sortby;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

        $bookings = Bookings::with('packages', 'guides', 'user', 'options')->orderBy($sortBy, $direction)
                            ->paginate($pages);

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
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $check = Bookings::where('package_id', $request->package_id)
                            ->where('guide_id', $request->guide_id)
                            ->where('date', $request->date)
                            ->first();
        if($check) return ResponseFormatter::error(null, 'booking have been registered');

        $user = Auth::user();

        $create = Bookings::create([
            'ref_id' => $request->ref_id,
            'package_id' => $request->package_id,
            'option_id' => $request->option_id,
            'guide_id' => $request->guide_id,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'time' => $request->time,
            'supplier' => $request->supplier,
            'status' => 2,
            'note' => $request->note,
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
            'down_payment' => $request->down_payment,
        ]);


        if($create) {
            return ResponseFormatter::success($create, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
        }
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

        $booking = Bookings::find($request->id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $booking->package_id = $request->package_id;
        $booking->guide_id = $request->guide_id;
        $booking->date = Carbon::parse($request->date)->format('Y-m-d');
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
        $booking->option_id = $request->option_id;
        $booking->country = $request->country;
        $booking->adult = $request->adult;
        $booking->child = $request->child;
        $booking->price = $request->price;
        $booking->down_payment = $request->down_payment;

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

        $guide = Guides::find($request->guide_id);
        if(!$guide) return ResponseFormatter::error(null, 'guide not found');

        $booking = Bookings::find($request->id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $booking->guide_id = $request->guide_id;

        if($booking->save()) {
            return ResponseFormatter::success($booking, 'success');
        }else{
            return ResponseFormatter::error(null, 'failed');
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

        $booking = Bookings::find($request->id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $booking->status = $request->status;

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

        $booking = Bookings::find($request->id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        if($booking->delete()) {
            return ResponseFormatter::success(null, 'success');
        }else{
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
                    ->with('packages', 'guides', 'user', 'options')->first();
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        if($booking) {
            return ResponseFormatter::success($booking, 'success');
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
        $sortBy = $request->sortby == null ? $sortBy = 'id' : $sortBy = $request->sortby;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

        $find = Bookings::where('date', $request->date)->with('packages', 'guides')
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

        $pages = $request->pages != null ? $request->pages : 10;
        $sortBy = $request->sortby == null ? $sortBy = 'id' : $sortBy = $request->sortby;
        $direction = $request->direction!= null ? 'DESC' : 'ASC';

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

        if($request->ref_id != '' || $request->ref_id != null) {
            $find = Bookings::where('ref_id', $request->ref_id)
                    ->with('packages', 'guides')
                    ->orderBy($sortBy, $direction)
                    ->paginate($pages);
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
                        ->with('packages', 'guides')
                        ->orderBy($sortBy, $direction)
                        ->paginate($pages);
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
