<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Facades\Storage;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Product Information')
                    ->description('Enter the basic details about your gown or suit rental product')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Product Name')
                                    ->helperText('Enter a descriptive name for your product (e.g., "Elegant Navy Blue Ball Gown")')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('e.g., Royal Blue Mermaid Evening Gown')
                                    ->rules(['required', 'string', 'max:100'])
                                    ->columnSpan(2),
                                Select::make('type')
                                    ->label('Product Type')
                                    ->helperText('Select whether this is a gown or suit')
                                    ->options([
                                        'Gown' => 'Gown',
                                        'Dress' => 'Dress',
                                        'Suit' => 'Suit',
                                        'Jacket' => 'Jacket',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->placeholder('Choose product type')
                                    ->columnSpan(1),
                            ])
                            ->columns(3),
                        Group::make()
                            ->schema([
                                Select::make('subtype')
                                    ->label('Style/Cut')
                                    ->helperText('Select the specific style or cut of the garment')
                                    ->options(function ($get) {
                                        if ($get('type') === 'Gown') {
                                            return [
                                                'Wedding Gown' => 'Wedding Gown',
                                                'Ball Gown' => 'Ball Gown',
                                                'Night Gown' => 'Night Gown',
                                                'Evening Gown' => 'Evening Gown',
                                                'Cocktail Gown' => 'Cocktail Gown',
                                                'A-line Gown' => 'A-line Gown',
                                                'Sheath Gown' => 'Sheath Gown',
                                                'Mermaid Gown' => 'Mermaid Gown',
                                                'Off-shoulder Gown' => 'Off-shoulder Gown',
                                                'Princess Gown' => 'Princess Gown',
                                                'Empire Waist Gown' => 'Empire Waist Gown',
                                                'V-neck Gown' => 'V-neck Gown',
                                                'Trumpet Gown' => 'Trumpet Gown',
                                                'filipiniana Gown' => 'filipiniana Gown',
                                                'Slip Gown' => 'Slip Gown',
                                                'Tea-length Gown' => 'Tea-length Gown',
                                                'High-low Gown' => 'High-low Gown',
                                                'Long Sleeve Gown' => 'Long Sleeve Gown',
                                                'Halter Gown' => 'Halter Gown',
                                                'Sweetheart Gown' => 'Sweetheart Gown',
                                                'Backless Gown' => 'Backless Gown',
                                                'Sequined Gown' => 'Sequined Gown',
                                                'Lace Gown' => 'Lace Gown',
                                                'Chiffon Gown' => 'Chiffon Gown',
                                                'Satin Gown' => 'Satin Gown',
                                                'Tulle Gown' => 'Tulle Gown',
                                                'Beaded Gown' => 'Beaded Gown',
                                                'Prom Gown' => 'Prom Gown',
                                                'Debutante Gown' => 'Debutante Gown',
                                                'Mother of the Bride Gown' => 'Mother of the Bride Gown',
                                                'Bridesmaid Gown' => 'Bridesmaid Gown',
                                                'Formal Maxi Gown' => 'Formal Maxi Gown',
                                                'One-shoulder Gown' => 'One-shoulder Gown',
                                                'Cowl Neck Gown' => 'Cowl Neck Gown',
                                            ];
                                        } elseif ($get('type') === 'Dress') {
                                            return [
                                                'Wedding Dress' => 'Wedding Dress',
                                                'Prom Dress' => 'Prom Dress',
                                                'Evening Dress' => 'Evening Dress',
                                                'Cocktail Dress' => 'Cocktail Dress',
                                                'A-line Dress' => 'A-line Dress',
                                                'Sheath Dress' => 'Sheath Dress',
                                                'Mermaid Dress' => 'Mermaid Dress',
                                                'Off-shoulder Dress' => 'Off-shoulder Dress',
                                                'Princess Dress' => 'Princess Dress',
                                                'Empire Waist Dress' => 'Empire Waist Dress',
                                                'V-neck Dress' => 'V-neck Dress',
                                                'Trumpet Dress' => 'Trumpet Dress',
                                            ];
                                        } elseif ($get('type') === 'Suit') {
                                            return [
                                                'Tuxedo' => 'Tuxedo',
                                                'Two Piece Suit' => 'Two Piece Suit',
                                                'Three Piece Suit' => 'Three Piece Suit',
                                                'Italian Suit' => 'Italian Suit',
                                                'Single Breasted Suit' => 'Single Breasted Suit',
                                                'Double Breasted Suit' => 'Double Breasted Suit',
                                                'Barong Tagalog' => 'Barong Tagalog',
                                                'Morning Coat' => 'Morning Coat',
                                                'Tailcoat' => 'Tailcoat',
                                                'Dinner Jacket' => 'Dinner Jacket',
                                                'Slim Fit Suit' => 'Slim Fit Suit',
                                                'Classic Fit Suit' => 'Classic Fit Suit',
                                                'Modern Fit Suit' => 'Modern Fit Suit',
                                                'Notch Lapel Suit' => 'Notch Lapel Suit',
                                                'Peak Lapel Suit' => 'Peak Lapel Suit',
                                                'Shawl Lapel Tuxedo' => 'Shawl Lapel Tuxedo',
                                                'Linen Suit' => 'Linen Suit',
                                                'Tweed Suit' => 'Tweed Suit',
                                                'Mandarin Collar Suit' => 'Mandarin Collar Suit',
                                                'Prince Coat' => 'Prince Coat',
                                                'Other' => 'Other',
                                            ];
                                        } elseif ($get('type') === 'Jacket') {
                                            return [
                                                'Blazer' => 'Blazer',
                                                'Bomber Jacket' => 'Bomber Jacket',
                                                'Leather Jacket' => 'Leather Jacket',
                                                'Denim Jacket' => 'Denim Jacket',
                                                'Other' => 'Other',
                                            ];
                                        } elseif ($get('type') === 'Other') {
                                            return [
                                                'Other' => 'Other',
                                            ];
                                        }
                                        return [];
                                    })
                                    ->required()
                                    ->placeholder('Choose style')
                                    ->columnSpan(1),
                                Select::make('size')
                                    ->label('Size Range')
                                    ->helperText('Select the precise size range for this garment')
                                    ->options([
                                        // Individual Sizes
                                        'XS' => 'XS',
                                        'S' => 'S',
                                        'M' => 'M',
                                        'L' => 'L',
                                        'XL' => 'XL',
                                        'XXL' => 'XXL',
                                        'XXXL' => 'XXXL',
                                        // --- Most Common Ranges ---
                                        'XS-S' => 'XS-S',
                                        'S-M' => 'S-M',
                                        'M-L' => 'M-L',
                                        'L-XL' => 'L-XL',
                                        'XL-XXL' => 'XL-XXL',
                                        // --- Extended Ranges ---
                                        'XXS-S' => 'XXS-S',
                                        'XS-M' => 'XS-M',
                                        'S-L' => 'S-L',
                                        'M-XL' => 'M-XL',
                                        'L-XXL' => 'L-XXL',
                                        'XXS-M' => 'XXS-M',
                                        'XS-L' => 'XS-L',
                                        'S-XL' => 'S-XL',
                                        'M-XXL' => 'M-XXL',
                                        // --- Broad Ranges ---
                                        'XXS-L' => 'XXS-L',
                                        'XS-XL' => 'XS-XL',
                                        'S-XXL' => 'S-XXL',
                                        'XXS-XL' => 'XXS-XL',
                                        'XS-XXL' => 'XS-XXL',
                                        'Adjustable' => 'Adjustable/Customizable',
                                    ])
                                    ->required()
                                    ->placeholder('Select size range')
                                    ->searchable()
                                    ->columnSpan(1),
                                TextInput::make('rental_price')
                                    ->label('Rental Price')
                                    ->helperText('Set the rental price for this product (in Philippine Pesos)')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₱')
                                    ->minValue(100)
                                    ->maxValue(50000)
                                    ->placeholder('e.g., 2500')
                                    ->rules(['required', 'numeric', 'min:100', 'max:50000'])
                                    ->columnSpan(1),
                            ])
                            ->columns(3),
                        Group::make()
                            ->schema([
                                TextInput::make('product_events')
                                    ->label('Events')
                                    ->helperText('List all events this product is suitable for (separate with commas)')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('e.g., Wedding, Anniversary, Formal')
                                    ->rules(['required', 'string', 'max:100'])
                                    ->columnSpan(1),
                                TextInput::make('colors')
                                    ->label('Available Colors')
                                    ->helperText('List all available colors for this product (separate with commas)')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('e.g., Navy Blue, Royal Blue, Midnight Blue')
                                    ->rules(['required', 'string', 'max:100'])
                                    ->columnSpan(1),
                                TextInput::make('fabric')
                                    ->label('Fabric Type')
                                    ->helperText('Specify the type of fabric used for this product')
                                    ->maxLength(100)
                                    ->placeholder('e.g., Silk, Satin, Chiffon')
                                    ->rules(['string', 'max:100'])
                                    ->columnSpan(1),
                            ])
                            ->columns(3),
                    ])
                    ->columns(1),
                Section::make('Product Images')
                    ->hiddenOn('edit')
                    ->description('Upload high-quality images to showcase your product')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('thumbnail_image')
                            ->label('Main Thumbnail Image')
                            ->helperText('Upload the main image that will be displayed as the product thumbnail. Recommended size: 800x800px')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('product-images/thumbnails')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                                '16:9',
                            ])
                            ->maxSize(5120)  // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->deletable(true)
                            ->openable()
                            ->required()
                            ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                            ->columnSpan(1),
                        FileUpload::make('images')
                            ->label('Additional Gallery Images')
                            ->helperText('Upload additional images to show different angles and details of your product. Maximum 10 images, 5MB each')
                            ->multiple()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('product-images')
                            ->image()
                            ->imageEditor()
                            ->maxFiles(10)
                            ->maxSize(5120)  // 5MB per file
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->deletable(true)
                            ->openable()
                            ->reorderable()
                            ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                Section::make('Product Measurements')
                    ->hiddenOn('edit')
                    ->description('Enter precise measurements for your product. All measurements should be in inches.')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Section::make('Gown Measurements (inches)')
                            ->description('Enter precise measurements for gown products')
                            ->icon('heroicon-o-sparkles')
                            ->visible(fn($get) => $get('type') === 'Gown' || $get('type') === 'Dress')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('gown_neck')->label('Neck')->numeric()->suffix('inches')->placeholder('e.g., 14'),
                                        TextInput::make('gown_shoulder')->label('Shoulder')->numeric()->suffix('inches')->placeholder('e.g., 16'),
                                        TextInput::make('gown_back_width')->label('Back Width')->numeric()->suffix('inches')->placeholder('e.g., 14'),
                                        TextInput::make('gown_bust')->label('Bust')->numeric()->suffix('inches')->placeholder('e.g., 38'),
                                        TextInput::make('gown_chest')->label('Chest')->numeric()->suffix('inches')->placeholder('e.g., 36'),
                                        TextInput::make('gown_bust_point')->label('Bust Point')->numeric()->suffix('inches')->placeholder('e.g., 10'),
                                        TextInput::make('gown_bust_distance')->label('Bust Distance')->numeric()->suffix('inches')->placeholder('e.g., 8'),
                                        TextInput::make('gown_arm_hole')->label('Arm Hole')->numeric()->suffix('inches')->placeholder('e.g., 18'),
                                    ])
                                    ->columns(2),
                                Group::make()
                                    ->schema([
                                        TextInput::make('gown_waist')->label('Waist')->numeric()->suffix('inches')->placeholder('e.g., 28'),
                                        TextInput::make('gown_hips')->label('Hips')->numeric()->suffix('inches')->placeholder('e.g., 40'),
                                        TextInput::make('gown_figure')->label('Figure')->numeric()->suffix('inches')->placeholder('e.g., 38'),
                                        TextInput::make('gown_sleeve_width')->label('Sleeve Width')->numeric()->suffix('inches')->placeholder('e.g., 6'),
                                        TextInput::make('gown_length')->label('Gown Length')->numeric()->suffix('inches')->placeholder('e.g., 60'),
                                    ])
                                    ->columns(2),
                            ]),
                        Section::make('Jacket Measurements (inches)')
                            ->description('Enter precise measurements for the jacket/blazer part of the suit')
                            ->icon('heroicon-o-squares-2x2')
                            ->visible(fn($get) => $get('type') === 'Suit' || $get('type') === 'Jacket')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('jacket_shoulder')->label('Shoulder Width')->numeric()->suffix('inches')->placeholder('e.g., 18'),
                                        TextInput::make('jacket_back_width')->label('Back Width')->numeric()->suffix('inches')->placeholder('e.g., 16'),
                                        TextInput::make('jacket_chest')->label('Jacket Chest')->numeric()->suffix('inches')->placeholder('e.g., 42'),
                                        TextInput::make('jacket_bust')->label('Jacket Bust')->numeric()->suffix('inches')->placeholder('e.g., 40'),
                                        TextInput::make('jacket_arm_hole')->label('Arm Hole')->numeric()->suffix('inches')->placeholder('e.g., 20'),
                                        TextInput::make('jacket_sleeve_length')->label('Sleeve Length')->numeric()->suffix('inches')->placeholder('e.g., 25'),
                                    ])
                                    ->columns(2),
                                Group::make()
                                    ->schema([
                                        TextInput::make('jacket_waist')->label('Jacket Waist')->numeric()->suffix('inches')->placeholder('e.g., 36'),
                                        TextInput::make('jacket_hip')->label('Jacket Hip')->numeric()->suffix('inches')->placeholder('e.g., 44'),
                                        TextInput::make('jacket_figure')->label('Jacket Figure')->numeric()->suffix('inches')->placeholder('e.g., 42'),
                                        TextInput::make('jacket_sleeve_width')->label('Sleeve Width')->numeric()->suffix('inches')->placeholder('e.g., 6'),
                                        TextInput::make('jacket_bicep')->label('Bicep')->numeric()->suffix('inches')->placeholder('e.g., 14'),
                                        TextInput::make('jacket_length')->label('Jacket Length')->numeric()->suffix('inches')->placeholder('e.g., 30'),
                                    ])
                                    ->columns(2),
                            ]),
                        Section::make('Trouser Measurements (inches)')
                            ->description('Enter precise measurements for the trouser/pants part of the suit')
                            ->icon('heroicon-o-rectangle-stack')
                            ->visible(fn($get) => $get('type') === 'Suit' || $get('type') === 'Jacket')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('trouser_waist')->label('Trouser Waist')->numeric()->suffix('inches')->placeholder('e.g., 32'),
                                        TextInput::make('trouser_hip')->label('Trouser Hip')->numeric()->suffix('inches')->placeholder('e.g., 40'),
                                        TextInput::make('trouser_crotch')->label('Crotch')->numeric()->suffix('inches')->placeholder('e.g., 28'),
                                        TextInput::make('trouser_thigh')->label('Thigh')->numeric()->suffix('inches')->placeholder('e.g., 24'),
                                    ])
                                    ->columns(2),
                                Group::make()
                                    ->schema([
                                        TextInput::make('trouser_knee')->label('Knee')->numeric()->suffix('inches')->placeholder('e.g., 16'),
                                        TextInput::make('trouser_bottom')->label('Bottom')->numeric()->suffix('inches')->placeholder('e.g., 14'),
                                        TextInput::make('trouser_leg_opening')->label('Leg Opening')->numeric()->suffix('inches')->placeholder('e.g., 8'),
                                        TextInput::make('trouser_inseam')->label('Inseam')->numeric()->suffix('inches')->placeholder('e.g., 32'),
                                        TextInput::make('trouser_outseam')->label('Outseam')->numeric()->suffix('inches')->placeholder('e.g., 42'),
                                        TextInput::make('trouser_length')->label('Trouser Length')->numeric()->suffix('inches')->placeholder('e.g., 42'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
