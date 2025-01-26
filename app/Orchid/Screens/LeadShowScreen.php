<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Lead;
use App\Models\LeadType;
use App\Models\LeadThermometer;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class LeadShowScreen extends Screen
{
    public $name = 'Visualizar Lead';

    public function query(Lead $lead): array
    {
        return [
            'lead' => $lead,
            'leadTypes' => LeadType::all(),
            'leadThermometers' => LeadThermometer::all(),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('lead.name')->title('Nome')->value($this->query()['lead']->name)->disabled(),
                Input::make('lead.email')->title('E-mail')->value($this->query()['lead']->email)->disabled(),
                Input::make('lead.whats')->title('WhatsApp')->value($this->query()['lead']->whats)->disabled(),
                Input::make('lead.origin')->title('Origem')->value($this->query()['lead']->origin)->disabled(),
                Select::make('lead.lead_type_id')
                    ->title('Tipo de Lead')
                    ->options($this->getLeadTypes())
                    ->value($this->query()['lead']->lead_type_id)->disabled(),
                Select::make('lead.lead_thermometer_id')
                    ->title('Termômetro')
                    ->options($this->getLeadThermometers())
                    ->value($this->query()['lead']->lead_thermometer_id)->disabled(),
            ]),
        ];
    }
}
