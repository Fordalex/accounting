<?php

namespace App\Lib;
use Auth;
use App\Models\Shift;

class ShiftRange
{
    public array $options;
    public $from_date, $to_date;

    public function __construct()
    {
        $this->shift_filter = Auth::user()->preference->shift_filter;
        $this->company_filter = Auth::user()->preference->company_filter;
        $this->from_date = Auth::user()->preference->from_date;
        $this->to_date = Auth::user()->preference->to_date;
        $this->shifts = $this->get_shifts();
    }

    public function get_shifts()
    {
        $shifts = Shift::all()->where('user_id', Auth::id())->sortby('date');
        if ($this->shift_filter == 'not_invoiced') {
            $shifts = $shifts->where('billed_shift', null);
        } else if ($this->shift_filter == 'invoiced') {
            $shifts = $shifts->where('billed_shift', '!=', null);
        }

        if ($this->company_filter) {
            $shifts = $shifts->where('company_id', $this->company_filter);
        }
        $shifts = $shifts->where('date', '>=', $this->from_date)->where('date', '<=', $this->to_date);
        return $shifts;
    }

    public function total_amount()
    {
        $total_amount = 0;
        foreach($this->shifts as $shift)
        {
            $total_amount += ($shift->duration / 60) * $shift->hourly_rate;
        }
        return $total_amount;
    }

    public function total_amount_after_tax()
    {
        return $this->total_amount() * 0.8;
    }

    public function total_duration()
    {
        return $this->shifts->sum('duration');
    }

    public function total_days()
    {
        return $this->from_date->diffInDays($this->to_date);
    }

    public function shift_count()
    {
        return count($this->shifts);
    }

    public function average_per_day()
    {
        return $this->total_amount() / $this->total_days();
    }
}

