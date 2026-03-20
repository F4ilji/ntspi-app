<?php

namespace App\Filament\Resources\CustomFormResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Ответы на форму';


    public function form(Form $form): Form
    {
        return $form
            ->schema(self::formColumns());
    }

    public function formColumns(): array
    {
        $columns = collect($this->ownerRecord->columns);

        return $columns
            ->pluck('data.name_field', 'data.title_field')
            ->map(function ($name, $label) use ($columns) {
                // Находим информацию о текущем поле по его имени
                $columnInfo = $columns->firstWhere('data.name_field', $name);

                // Проверяем тип поля
                if ($columnInfo['type'] === 'multiple_choice') {
                    $columnInfo = collect($columnInfo['data']['columns']);
                    $options = $columnInfo->map(function ($column) {
                        return [
                            $column['name_field'] => $column['title_field']
                        ];
                    })->reduce(function ($carry, $item) {
                        return array_merge($carry, $item);
                    }, []);
                    return Forms\Components\CheckboxList::make('answers.' . $name)
                        ->options($options)
                        ->label($label)
                        ->columnSpanFull();
                }

                if ($columnInfo['type'] === 'single_choice') {
                    $columnInfo = collect($columnInfo['data']['columns']);
                    $options = $columnInfo->map(function ($column) {
                        return [
                            $column['name_field'] => $column['title_field']
                        ];
                    })->reduce(function ($carry, $item) {
                        return array_merge($carry, $item);
                    }, []);
                    return Forms\Components\Radio::make('answers.' . $name)
                        ->options($options)
                        ->label($label)
                        ->columnSpanFull();
                }


                // Обработка других типов полей
                return Forms\Components\TextInput::make('answers.' . $name)
                    ->label($label)
                    ->columnSpanFull();
            })
            ->values()
            ->toArray();
    }



    public function infoListColumns(): array
    {
        $columns = collect($this->ownerRecord->columns);

        return $columns
            ->pluck('data.name_field', 'data.title_field')
            ->map(function ($name, $label) use ($columns) {
                // Находим информацию о текущем поле по его имени
                $columnInfo = $columns->firstWhere('data.name_field', $name);


                // Проверяем тип поля
                if ($columnInfo['type'] === 'multiple_choice') {
                    $columnInfo = collect($columnInfo['data']['columns']);
                    $options = $columnInfo->map(function ($column) {
                        return [
                            $column['name_field'] => $column['title_field']
                        ];
                    })->reduce(function ($carry, $item) {
                        return array_merge($carry, $item);
                    }, []);
                    return TextEntry::make('answers.' . $name)
                        ->label($label)
                        ->columnSpanFull();
                }

                if ($columnInfo['type'] === 'single_choice') {
                    $columnInfo = collect($columnInfo['data']['columns']);
                    $options = $columnInfo->map(function ($column) {
                        return $column['title_field'];
                    });
                    return TextEntry::make('answers.' . $name)
                        ->label($label)
                        ->columnSpanFull();
                }


                // Обработка других типов полей
                return TextEntry::make('answers.' . $name)
                    ->label($label)
                    ->columnSpanFull();
            })
            ->values()
            ->toArray();
    }

    public function exportColumns(): array
    {
        $columns = collect($this->ownerRecord->columns);

        $columnObjects = $columns
            ->pluck('data.name_field', 'data.title_field')
            ->map(function ($name, $label) use ($columns) {
                // Находим информацию о текущем поле по его имени
                $columnInfo = $columns->firstWhere('data.name_field', $name);
                // Проверяем тип поля
                if ($columnInfo['type'] === 'multiple_choice') {
                    $columnInfo = collect($columnInfo['data']['columns']);
                    $options = $columnInfo->map(function ($column) {
                        return [
                            $column['name_field'] => $column['title_field']
                        ];
                    })->reduce(function ($carry, $item) {
                        return array_merge($carry, $item);
                    }, []);
                    return Column::make('answers.' . $name)->heading($label)
                        ->formatStateUsing(function ($state) use ($options) {
                            return $options[$state];
                        });
                }

                if ($columnInfo['type'] === 'single_choice') {
                    $columnInfo = collect($columnInfo['data']['columns']);
                    $options = $columnInfo->map(function ($column) {
                        return [
                            $column['name_field'] => $column['title_field']
                        ];
                    })->reduce(function ($carry, $item) {
                        return array_merge($carry, $item);
                    }, []);
                    return Column::make('answers.' . $name)->heading($label)
                        ->formatStateUsing(function ($state) use ($options) {
                            return $options[$state];
                        });
                }
                // Обработка других типов полей
                return Column::make('answers.' . $name)->heading($label);
            });

        $columnObjects->prepend(Column::make('id')->heading('ID'));
        $columnObjects->push(Column::make('created_at')->heading('Время создания'));


        return $columnObjects->toArray();
    }


// Метод для получения опций для множественного выбора

    public function tableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\ToggleColumn::make('checked')->label('Просмотрено'),
            Tables\Columns\TextColumn::make('created_at')->label('Врямя создания')->sortable(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(self::tableColumns())
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make()->withColumns(self::exportColumns()),
                ]),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()->withColumns(self::exportColumns())
                            ->askForFilename()
                            ->askForWriterType(),
                    ]),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
