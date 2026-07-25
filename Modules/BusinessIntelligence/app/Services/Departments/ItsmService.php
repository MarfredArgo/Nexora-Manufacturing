<?php

namespace App\Services\Departments;

use App\Models\ItsmTicket;

/**
 * Computes ITSM KPIs from itsm_tickets.
 */
class ItsmService
{
    /**
     * @return array<string, mixed>
     */
    public function getSnapshot(): array
    {
        return [
            'open_tickets' => $this->openTicketsCount(),
            'critical_tickets' => $this->criticalTicketsCount(),
            'avg_resolution_hours' => $this->averageResolutionHours(),
            'tickets_by_priority' => $this->ticketsByPriority(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getKpiSummaryForAi(): array
    {
        return [
            'open_tickets' => $this->openTicketsCount(),
            'critical_tickets' => $this->criticalTicketsCount(),
            'avg_resolution_hours' => $this->averageResolutionHours(),
        ];
    }

    public function openTicketsCount(): int
    {
        return ItsmTicket::open()->count();
    }

    public function criticalTicketsCount(): int
    {
        return ItsmTicket::open()->where('priority', 'critical')->count();
    }

    public function averageResolutionHours(): float
    {
        $resolved = ItsmTicket::whereNotNull('resolved_at')->get();

        if ($resolved->isEmpty()) {
            return 0.0;
        }

        $totalHours = $resolved->sum(
            fn (ItsmTicket $ticket) => $ticket->opened_at->diffInHours($ticket->resolved_at)
        );

        return round($totalHours / $resolved->count(), 2);
    }

    /**
     * @return array<string, int>
     */
    public function ticketsByPriority(): array
    {
        return ItsmTicket::open()
            ->selectRaw('priority, count(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();
    }
}
