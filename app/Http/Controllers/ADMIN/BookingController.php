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

        $bookings = Bookings::with('packages', 'guides')->orderBy($sortBy, $direction)
                            ->paginate($pages);

        if($bookings){
            return ResponseFormatter::success($bookings, 'success');
        }else{
            return ResponseFormatter::error(null, 'success');
        }
    }

    public function add(Request $request)
    {
        $rules = [
            'package_id' => ['required'],
            'guide_id' => ['required'],
            'date' => ['required'],
            'time' => ['required', 'date_format:H:i'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $guide = Guides::find($request->guide_id);
        if(!$guide) return ResponseFormatter::error(null, 'guide not found');

        $package = Packages::find($request->package_id);
        if(!$package) return ResponseFormatter::error(null, 'tour not found');

        $check = Bookings::where('package_id', $request->package_id)
                            ->where('guide_id', $request->guide_id)
                            ->where('date', $request->date)
                            ->where('time', $request->time)
                            ->first();
        if($check) return ResponseFormatter::error(null, 'booking have been registered');

        $create = Bookings::create([
            'package_id' => $request->package_id,
            'guide_id' => $request->guide_id,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'time' => $request->time,
            'supplier' => $request->supplier,
            'status' => $request->status,
            'note' => $request->note,
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
            'package_id' => ['required'],
            'guide_id' => ['required'],
            'date' => ['required'],
            'time' => ['required', 'date_format:H:i'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ResponseFormatter::error($validator->getMessageBag()->toArray(), 'Failed Validation');
        }

        $guide = Guides::find($request->guide_id);
        if(!$guide) return ResponseFormatter::error(null, 'guide not found');

        $package = Packages::find($request->package_id);
        if(!$package) return ResponseFormatter::error(null, 'tour not found');

        $booking = Bookings::find($request->id);
        if(!$booking) return ResponseFormatter::error(null, 'not found');

        $booking->package_id = $request->package_id;
        $booking->guide_id = $request->guide_id;
        $booking->date = Carbon::parse($request->date)->format('Y-m-d');
        $booking->time = $request->time;
        $booking->supplier = $request->supplier;
        $booking->status = $request->status;
        $booking->note = $request->note;


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

        $booking = Bookings::find($request->id);
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
            return ResponseFormatter::error(null, 'success');
        }
    }
}
