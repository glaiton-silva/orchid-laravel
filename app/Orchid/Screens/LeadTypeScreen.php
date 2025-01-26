<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\LeadType;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class LeadTypeScreen extends Screen
{
    public $name = 'Tipos de Leads';

    public function query(): array
    {
        return [
            'leadTypes' => LeadType::onlyTrashed()->paginate(10), 
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('leadTypes', [
                TD::make('id', 'ID')->sort(),
                TD::make('name', 'Nome')->sort(),
                TD::make('actions', 'Ações')
                    ->render(function (LeadType $leadType) {
                        return Button::make('Excluir')
                            ->method('softDelete')
                            ->icon('trash')
                            ->confirm('Deseja excluir este Tipo de Lead?')
                            ->parameters([
                                'id' => $leadType->id
                            ])
                            . Button::make('Restaurar')
                            ->method('restore')
                            ->icon('refresh')
                            ->confirm('Deseja restaurar este Tipo de Lead?')
                            ->parameters([
                                'id' => $leadType->id
                            ]);
                    }),
            ]),
            Layout::rows([
                Input::make('leadType.name')->title('Nome')->placeholder('Digite o tipo de lead'),
                Button::make('Submit')
                    ->method('save')
                    ->type(Color::BASIC),
            ]),
        ];
    }

    public function save()
    {
        $data = request()->get('leadType');

        $leadType = LeadType::updateOrCreate(
            ['id' => $data['id'] ?? null],
            ['name' => $data['name']]
        );

        Toast::info('Tipo de Lead salvo com sucesso!');
    }

    public function softDelete(int $id)
    {
        $leadType = LeadType::find($id);

        if ($leadType) {
            $leadType->delete();
            Toast::info('Tipo de Lead excluído com sucesso!');
        } else {
            Toast::error('Tipo de Lead não encontrado!');
        }
    }

    public function restore(int $id)
    {
        $leadType = LeadType::withTrashed()->find($id);

        if ($leadType) {
            $leadType->restore();
            Toast::info('Tipo de Lead restaurado com sucesso!');
        } else {
            Toast::error('Tipo de Lead não encontrado!');
        }
    }
}
