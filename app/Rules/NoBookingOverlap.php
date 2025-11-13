<?php

namespace App\Rules;

use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class NoBookingOverlap implements ValidationRule, DataAwareRule
{
    protected array $data = [];
    protected ?int $excludeId = null;

    public function __construct(?int $excludeId = null)
    {
        $this->excludeId = $excludeId;
    }

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userId = $this->data['user_id'] ?? auth()->id();
        $startTime = $this->data['start_time'] ?? null;
        $endTime = $this->data['end_time'] ?? null;

        if (!$userId || !$startTime || !$endTime) {
            return;
        }

        // Convert to Carbon instances for proper comparison
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);

        $query = Booking::where('user_id', $userId)
            ->where(function ($q) use ($start, $end) {
                // Check if new booking overlaps with existing bookings
                // Overlaps if: new_start < existing_end AND new_end > existing_start
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            });

        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }

        if ($query->exists()) {
            $fail('This booking overlaps with an existing booking.');
        }
    }
}
