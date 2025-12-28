<?php

namespace Webkul\Admin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Webkul\Admin\Models\Attendance;
use Webkul\Admin\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $todayAttendance = Attendance::where('user_id', auth()->guard('user')->user()->id)
            ->where('date', Carbon::today()->toDateString())
            ->first();

        $history = Attendance::where('user_id', auth()->guard('user')->user()->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return view('admin::attendance.index', compact('todayAttendance', 'history'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $userId = auth()->guard('user')->user()->id;
        $today = Carbon::today()->toDateString();

        $existing = Attendance::where('user_id', $userId)->where('date', $today)->first();

        if ($existing) {
            session()->flash('warning', 'You have already checked in today.');
            return redirect()->back();
        }

        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => Carbon::now(),
            'check_in_lat' => $request->lat,
            'check_in_lng' => $request->lng,
            'ip_address' => $request->ip(),
        ]);

        session()->flash('success', 'Checked In Successfully');

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $attendance = Attendance::find($id);

        if (!$attendance || $attendance->user_id !== auth()->guard('user')->user()->id) {
            session()->flash('error', 'Attendance record not found.');
            return redirect()->back();
        }

        $attendance->update([
            'check_out' => Carbon::now(),
            'check_out_lat' => $request->lat,
            'check_out_lng' => $request->lng,
        ]);

        session()->flash('success', 'Checked Out Successfully');

        return redirect()->back();
    }
}
