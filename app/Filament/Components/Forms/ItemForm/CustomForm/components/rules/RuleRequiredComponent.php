<?php

namespace App\Filament\Components\Forms\ItemForm\CustomForm\components\rules;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class RuleRequiredComponent
{
    public static function getComponent() : Toggle
    {
        return Toggle::make('required')->label('Обязательное поле');
    }


}