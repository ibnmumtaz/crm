<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MeetingType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $meetingTypes = MeetingType::where('is_active', true)->get();

        return view('bookings.index', compact('meetingTypes'));
    }

    public function show(MeetingType $meetingType)
    {
        $slots = $this->availableSlots($meetingType);

        return view('bookings.show', compact('meetingType', 'slots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'starts_at' => 'required|date',
            'timezone' => 'required|string|timezone',
        ]);

        $meetingType = MeetingType::whereKey($validated['meeting_type_id'])->where('is_active', true)->firstOrFail();
        $timezone = $validated['timezone'];
        try {
            $startsAt = Carbon::parse($validated['starts_at'])->setTimezone($timezone);
        } catch (\Throwable $exception) {
            return back()->withErrors(['starts_at' => 'Please choose a valid time slot.'])->withInput();
        }
        $endsAt = $startsAt->copy()->addMinutes($meetingType->duration_minutes);

        // server-side availability revalidation
        if (! $this->isSlotAvailable($meetingType, $startsAt, $endsAt)) {
            return back()->with('error', 'That time slot is no longer available. Please choose another.')->withInput();
        }

        $booking = \DB::transaction(function () use ($validated, $meetingType, $startsAt, $endsAt, $timezone) {
            // lock to reduce race
            $conflict = Booking::where('meeting_type_id', $meetingType->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($startsAt, $endsAt) {
                    $q->where('starts_at', '<', $endsAt)
                        ->where('ends_at', '>', $startsAt);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                return null;
            }

            return Booking::create([
                'meeting_type_id' => $meetingType->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'company' => $validated['company'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'timezone' => $timezone,
                'status' => 'confirmed',
                'cancellation_token' => Str::random(48),
            ]);
        });

        if (! $booking) {
            return back()->with('error', 'That time slot was just booked. Please choose another.')->withInput();
        }

        return redirect()->route('bookings.confirmation', $booking)->with('success', 'Booking confirmed.');
    }

    public function confirmation(Booking $booking)
    {
        $booking->load('meetingType');

        return view('bookings.confirmation', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        $token = $request->input('token');
        if (! $token || ! hash_equals($booking->cancellation_token, $token)) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled.');
    }

    public function adminIndex()
    {
        if (! auth()->user()->can('user.manage')) {
            abort(403);
        }
        $bookings = Booking::with('meetingType')->latest()->paginate(20);

        return view('bookings.admin', compact('bookings'));
    }

    private function availableSlots(MeetingType $meetingType): array
    {
        $slots = [];
        $existing = Booking::where('meeting_type_id', $meetingType->id)
            ->where('status', '!=', 'cancelled')
            ->get();

        $now = Carbon::now();
        for ($d = 0; $d < 14; $d++) {
            $day = $now->copy()->addDays($d)->startOfDay();
            if ($day->isWeekend()) {
                continue;
            }
            $cursor = $day->copy()->setTime(9, 0);
            $endOfDay = $day->copy()->setTime(17, 0);
            while ($cursor->copy()->addMinutes($meetingType->duration_minutes)->lte($endOfDay)) {
                $slotEnd = $cursor->copy()->addMinutes($meetingType->duration_minutes);
                if ($cursor->gt($now)) {
                    $conflict = $existing->first(function ($b) use ($cursor, $slotEnd) {
                        return $b->starts_at->lt($slotEnd) && $b->ends_at->gt($cursor);
                    });
                    if (! $conflict) {
                        $slots[] = [
                            'start' => $cursor->copy(),
                            'end' => $slotEnd,
                            'label' => $cursor->format('D, M j \a\t g:i A'),
                        ];
                    }
                }
                $cursor->addMinutes(30);
            }
        }

        return $slots;
    }

    private function isSlotAvailable(MeetingType $meetingType, Carbon $startsAt, Carbon $endsAt): bool
    {
        $now = Carbon::now($startsAt->timezone);
        $windowEnd = $now->copy()->addDays(14)->endOfDay();
        $opening = $startsAt->copy()->setTime(9, 0);
        $closing = $startsAt->copy()->setTime(17, 0);

        if ($startsAt->lte($now) || $startsAt->gt($windowEnd) || $startsAt->isWeekend() || $startsAt->lt($opening) || $endsAt->gt($closing)) {
            return false;
        }

        return ! Booking::where('meeting_type_id', $meetingType->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();
    }
}
