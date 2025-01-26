<?php
namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\LeadThermometer;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\TD;
use Orchid\Support\Color;

class LeadThermometerScreen extends Screen
{
    public $name = 'Termômetro de Leads';

    public function query(): array
    {
        return [
            'leadThermometers' => LeadThermometer::onlyTrashed()->paginate(10),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('leadThermometer', [
                TD::make('id', 'ID')->sort(),
                TD::make('name', 'Nome')->sort(),
                TD::make('actions', 'Ações')
                    ->render(function (LeadThermometer $leadThermometer) {
                        return Button::make('Excluir')
                            ->method('softDelete')
                            ->icon('trash')
                            ->confirm('Deseja excluir este Termômetro?')
                            ->parameters([
                                'id' => $leadThermometer->id
                            ])
                            . Button::make('Restaurar')
                            ->method('restore')
                            ->icon('refresh')
                            ->confirm('Deseja restaurar este Termômetro?')
                            ->parameters([
                                'id' => $leadThermometer->id
                            ]);
                    }),
            ]),
            Layout::rows([
                Input::make('leadThermometer.name')->title('Nome')->placeholder('Digite o nome do termômetro'),
                Button::make('Submit')
                    ->method('save')
                    ->type(Color::BASIC),
            ]),
        ];
    }

    public function save()
    {
        $data = request()->get('leadThermometer');

        $leadThermometer = LeadThermometer::updateOrCreate(
            ['id' => $data['id'] ?? null],
            ['name' => $data['name']]
        );

        Toast::info('Termômetro salvo com sucesso!');
    }

    public function softDelete(int $id)
    {
        $leadThermometer = LeadThermometer::find($id);

        if ($leadThermometer) {
            $leadThermometer->delete();
            Toast::info('Termômetro excluído com sucesso!');
        } else {
            Toast::error('Termômetro não encontrado!');
        }
    }

    public function restore(int $id)
    {
        $leadThermometer = LeadThermometer::withTrashed()->find($id);

        if ($leadThermometer) {
            $leadThermometer->restore();
            Toast::info('Termômetro restaurado com sucesso!');
        } else {
            Toast::error('Termômetro não encontrado!');
        }
    }
}
