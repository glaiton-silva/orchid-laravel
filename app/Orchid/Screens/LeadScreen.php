<?php
namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Lead;
use App\Models\LeadType;
use App\Models\LeadThermometer;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\TD;
use Orchid\Support\Color;

class LeadScreen extends Screen
{
    public $name = 'Lead';

    // Adiciona a possibilidade de listar, editar e criar lead
    public function query(Lead $lead = null): array
    {
        return [
            'lead' => Lead::paginate(10), // Para listagem com paginação
            'lead' => $lead ?? new Lead(), // Verifica se há um Lead para editar ou cria um novo
            'leadTypes' => LeadType::all(),
            'leadThermometers' => LeadThermometer::all(),
        ];
    }

    // Layout para a listagem e o formulário de criação/edição
    public function layout(): array
    {
        return [
            // Layout da Listagem
            Layout::table('lead', [
                TD::make('id', 'ID')->sort(),
                TD::make('name', 'Nome')->sort(),
                TD::make('email', 'E-mail')->sort(),
                TD::make('whats', 'WhatsApp')->sort(),
                TD::make('origin', 'Origem')->sort(),
                TD::make('actions', 'Ações')
                    ->render(function (Lead $lead) {
                        return Button::make('Editar')
                            ->route('platform.lead.edit', $lead->id)
                            ->icon('pencil')
                            . Button::make('Excluir')
                            ->method('softDelete')
                            ->icon('trash')
                            ->confirm('Deseja excluir este Lead?')
                            ->parameters(['id' => $lead->id])
                            . Button::make('Restaurar')
                            ->method('restore')
                            ->icon('refresh')
                            ->confirm('Deseja restaurar este Lead?')
                            ->parameters(['id' => $lead->id]);
                    }),
            ]),

            // Layout do formulário de criação/edição
            Layout::rows([
                Input::make('lead.name')
                    ->title('Nome')
                    ->placeholder('Digite o nome do lead')
                    ->value($this->query()['lead']->name),
                Input::make('lead.email')
                    ->title('E-mail')
                    ->placeholder('Digite o e-mail do lead')
                    ->value($this->query()['lead']->email),
                Input::make('lead.whats')
                    ->title('WhatsApp')
                    ->placeholder('Digite o número do WhatsApp')
                    ->value($this->query()['lead']->whats),
                Input::make('lead.origin')
                    ->title('Origem')
                    ->placeholder('Digite a origem do lead')
                    ->value($this->query()['lead']->origin),
                Select::make('lead.lead_type_id')
                    ->title('Tipo de Lead')
                    ->options($this->getLeadTypes())
                    ->placeholder('Selecione o tipo de Lead')
                    ->value($this->query()['lead']->lead_type_id),
                Select::make('lead.lead_thermometer_id')
                    ->title('Termômetro')
                    ->options($this->getLeadThermometers())
                    ->placeholder('Selecione o termômetro')
                    ->value($this->query()['lead']->lead_thermometer_id),
                Button::make('Salvar')
                    ->method('save')
                    ->type(Color::BASIC),
            ]),
        ];
    }

    // Função para salvar ou atualizar o Lead
    public function save()
    {
        $data = request()->get('lead');

        $lead = Lead::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'whats' => $data['whats'],
                'origin' => $data['origin'],
                'lead_type_id' => $data['lead_type_id'],
                'lead_thermometer_id' => $data['lead_thermometer_id'],
                'password' => bcrypt($data['password'] ?? ''),
            ]
        );

        Toast::info('Lead salvo com sucesso!');
    }

    // Função para excluir (soft delete) o Lead
    public function softDelete(int $id)
    {
        $lead = Lead::find($id);

        if ($lead) {
            $lead->delete();
            Toast::info('Lead excluído com sucesso!');
        } else {
            Toast::error('Lead não encontrado!');
        }
    }

    // Função para restaurar o Lead (undo soft delete)
    public function restore(int $id)
    {
        $lead = Lead::withTrashed()->find($id);

        if ($lead) {
            $lead->restore();
            Toast::info('Lead restaurado com sucesso!');
        } else {
            Toast::error('Lead não encontrado!');
        }
    }

    // Função para obter os tipos de Lead
    private function getLeadTypes()
    {
        return LeadType::all()->pluck('name', 'id')->toArray();
    }

    // Função para obter os termômetros de Lead
    private function getLeadThermometers()
    {
        return LeadThermometer::all()->pluck('name', 'id')->toArray();
    }
}
