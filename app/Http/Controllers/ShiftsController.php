<?php

namespace App\Http\Controllers;
use App\Models\Shift;
use App\Models\Company;
use App\Models\Preference;
use App\Lib\ShiftRange;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;
use MoneyHelper;

class ShiftsController extends Controller

{
    protected function create()
    {
        Shift::create([
            'date' => Carbon::parse(request('date'))->format('Y-m-d H:i:s'),
            'description' => request('description'),
            'duration' => request('duration'),
            'hourly_rate' => request('hourly_rate'),
            'company_id' => request('company_id'),
            'user_id' => Auth::id(),
        ]);

        return redirect('/shifts')->with('flash_message', ["type" => "success", "message" => "Shift was created successfully!"]);
    }

    protected function index()
    {
        $this->update_preferences();

        $from_date = Carbon::today()->subDays(30);
        $to_date = Carbon::today();
        if (isset($_REQUEST["from_date"]) && isset($_REQUEST["to_date"]))
        {
            $from_date = Carbon::parse($_REQUEST["from_date"]);
            $to_date = Carbon::parse($_REQUEST["to_date"]);
        }

        $options = [
            'shift_filter' => $_REQUEST["shiftFilter"] ?? null,
            'company' => $_REQUEST["company"] ?? null,
        ];
        $shift_range = new ShiftRange($from_date, $to_date, $options);
        $companies = Company::all()->where('user_id', Auth::id());


        return view('shifts.index', compact('shift_range', 'companies'));
    }

    protected function new()
    {
        $companies = Company::all();
        $today = Carbon::today();
        $shift = new Shift(['date' => $today]);

        return view('shifts.new', compact('companies', 'shift'));
    }

    protected function edit(Shift $shift)
    {
        $companies = Company::all();

        return view('shifts.edit', compact('shift', 'companies'));
    }

    protected function update(Shift $shift)
    {
        // model validation?
        request()->validate([
            'duration' => 'required',
            'hourly_rate' => 'required',
        ]);

        $shift->update([
            'description' => request('description'),
            'duration' => request('duration'),
            'hourly_rate' => request('hourly_rate'),
            'date' => Carbon::parse(request('date'))->format('Y-m-d H:i:s'),
            'company_id' => request('company_id')
        ]);

        return redirect('/shifts')->with('flash_message', ["type" => "success", "message" => "Shift was updated successfully!"]);;
    }

    protected function update_preferences()
    {
        $user = Auth::user();

        if (isset($_REQUEST["today"]))
        {
            $today = true;
        } else {
            $today = false;
        }

        if ($today)
        {
            $to_date = Carbon::today();
        } else {
            $to_date = $_REQUEST["to_date"] ?? $user->preference?->to_date ?? Carbon::today();
        }

        $attributes = [
            'user_id' => Auth::id(),
            'shift_filter' => $_REQUEST["shift_filter"] ?? $user->preference?->shift_filter ?? "",
            'company_filter' => $_REQUEST["company_filter"] ?? $user->preference?->company_filter ?? "",
            'from_date' => $_REQUEST["from_date"] ?? $user->preference?->from_date ?? Carbon::today()->subDays(30),
            'to_date' => $to_date,
            'today' => $today
        ];
        if ($user->preference)
        {
            $user->preference->update($attributes);
        }
        else
        {
            Preference::create($attributes)->save();
        }
    }
}
