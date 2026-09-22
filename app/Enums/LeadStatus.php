<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Consultation = 'consultation';
    case ProposalSent = 'proposal_sent';
    case Negotiation = 'negotiation';
    case Booked = 'booked';
    case Lost = 'lost';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Qualified => 'Qualified',
            self::Consultation => 'Consultation',
            self::ProposalSent => 'Proposal Sent',
            self::Negotiation => 'Negotiation',
            self::Booked => 'Booked',
            self::Lost => 'Lost',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'slate',
            self::Contacted => 'sky',
            self::Qualified => 'indigo',
            self::Consultation => 'violet',
            self::ProposalSent => 'amber',
            self::Negotiation => 'orange',
            self::Booked => 'emerald',
            self::Lost => 'rose',
            self::Closed => 'zinc',
        };
    }

    public static function pipeline(): array
    {
        return [
            self::New, self::Contacted, self::Qualified, self::Consultation,
            self::ProposalSent, self::Negotiation, self::Booked,
        ];
    }

}
