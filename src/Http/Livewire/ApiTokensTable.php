<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasSanctumPermissionsProperty;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Livewire\Component;

class ApiTokensTable extends Component implements HasTable
{
    use InteractsWithTable;
    use HasSanctumPermissionsProperty;

    protected $listeners = [
        'tokenCreated' => '$refresh',
    ];

    public function render()
    {
        return view('filament-jet::livewire.api-tokens-table');
    }

    protected function getTableQuery(): Builder|Relation
    {
        return app(Sanctum::$personalAccessTokenModel)->where([
            ['tokenable_id', '=', Filament::auth()->id()],
            ['tokenable_type', '=', FilamentJet::userModel()],
        ])->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('filament-jet::api.fields.token_name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('last_used_at')
                ->label(__('filament-jet::api.fields.last_used_at'))
                ->searchable()
                ->sortable()
                ->formatStateUsing(
                    fn (string|null $state): string|null => $state ? Carbon::parse($state)->diffForHumans() : __('filament-jet::api.table.never')
                ),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('permissions')
                ->action('edit')
                ->icon('heroicon-o-pencil-alt')
                ->modalWidth('sm')
                ->mountUsing(
                    fn ($form, $record) => $form->fill($record->toArray())
                )
                ->form([
                    CheckboxList::make('abilities')
                        ->label(__('filament-jet::api.fields.permissions'))
                        ->options($this->sanctumPermissions)
                        ->columns(2)
                        ->required()
                        ->afterStateHydrated(function ($component, $state) {
                            $permissions = FilamentJet::$permissions;

                            $tokenPermissions = collect($permissions)
                                ->filter(function ($permission) use ($state) {
                                    return in_array($permission, $state);
                                })
                                ->values()
                                ->toArray();

                            $component->state($tokenPermissions);
                        }),
                ]),
            Action::make('delete')
                ->action('delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    public function edit($record, $data)
    {
        $record->forceFill([
            'abilities' => FilamentJet::validPermissions($data['abilities']),
        ])->save();

        Filament::notify('success', __('filament-jet::api.update.notify'));
    }

    public function delete($record)
    {
        $record->delete();

        Filament::notify('success', __('filament-jet::api.delete.notify'));
    }
}
