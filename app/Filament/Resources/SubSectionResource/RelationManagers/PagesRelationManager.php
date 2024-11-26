<?php

namespace App\Filament\Resources\SubSectionResource\RelationManagers;

use App\Filament\Components\Forms\PageForm;
use App\Models\SubSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagesRelationManager extends RelationManager
{
    protected static string $relationship = 'pages';

    protected static ?string $title = 'Страницы';


    public function form(Form $form): Form
    {
        return PageForm::getForm($form);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function ($data) {
                    $subSection = SubSection::find($data['sub_section_id']);


                    if ($subSection == null) {
                        $data['path'] = $data['slug'];
                    } elseif($subSection->mainSection == null) {
                        $data['path'] =  $subSection->slug . '/' . $data['slug'];
                    } else {
                        $data['path'] = $subSection->mainSection->slug . '/' . $subSection->slug . '/' . $data['slug'];
                    }
                    unset($data['sub_section_id']);




                    $result = "";
                    foreach ($data['content'] as $block) {
                        $result .= $this->getDataFromBlocks($block);
                    }

                    // Удаляем лишние пробелы и переносы строк
                    $result = preg_replace('/\s+/', ' ', $result);
                    $result = trim($result);


                    // Приводим текст к нижнему регистру
                    $data['search_data'] = strtolower($result);

                    return $data;
                }),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }


    private function getDataFromBlocks($block) : string
    {
        $data = "";
        switch ($block['type']) {
            case 'paragraph':
                $data .= strip_tags($block['data']['content']) . " ";
                break;
            case 'heading':
                $data .= strip_tags($block['data']['content']) . " ";
                break;
            case 'files':
                foreach ($block['data']['file'] as $file) {
                    $data .= $file['title'] . " ";
                }
                break;
            case 'person':
                $data .= $block['data']['name'] . " ";
                break;
            case 'stepper':
                $data .= $block['data']['step_name'] . " ";
                foreach ($block['data']['steps'] as $step) {
                    $data .= $step['title'] . " ";
                    $data .= strip_tags($step['content']) . " ";
                }
                break;
            case 'tabs':
                foreach ($block['data']['tab'] as $item) {
                    foreach ($item['content'] as $block) {
                        $data .= $this->getDataFromBlocks($block);
                    };
                };
                break;

        }
        return $data;
    }

}
