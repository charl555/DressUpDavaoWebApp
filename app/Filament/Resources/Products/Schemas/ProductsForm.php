<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                                        'Suit' => 'Suit',
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
                                    ->searchable()
                                    ->options(function ($get) {
                                        if ($get('type') === 'Gown') {
                                            return [
                                                'A-line Gown' => 'A-line Gown',
                                                'Anarkali Gown' => 'Anarkali Gown',
                                                'Angel Sleeve Gown' => 'Angel Sleeve Gown',
                                                'Asymmetrical Gown' => 'Asymmetrical Gown',
                                                'Backless Gown' => 'Backless Gown',
                                                'Ball Gown' => 'Ball Gown',
                                                'Bandage Gown' => 'Bandage Gown',
                                                'Bandeau Gown' => 'Bandeau Gown',
                                                'Beaded Gown' => 'Beaded Gown',
                                                'Bias-cut Gown' => 'Bias-cut Gown',
                                                'Boat Neck Gown' => 'Boat Neck Gown',
                                                'Bodysuit Gown' => 'Bodysuit Gown',
                                                'Boho Gown' => 'Boho Gown',
                                                'Bolero Gown' => 'Bolero Gown',
                                                'Bouffant Gown' => 'Bouffant Gown',
                                                'Bridesmaid Gown' => 'Bridesmaid Gown',
                                                'Brocade Gown' => 'Brocade Gown',
                                                'Bustier Gown' => 'Bustier Gown',
                                                'Cape Gown' => 'Cape Gown',
                                                'Cap-sleeve Gown' => 'Cap-sleeve Gown',
                                                'Cargo Gown' => 'Cargo Gown',
                                                'Chantilly Lace Gown' => 'Chantilly Lace Gown',
                                                'Chemise Gown' => 'Chemise Gown',
                                                'Chiffon Gown' => 'Chiffon Gown',
                                                'Choker Neck Gown' => 'Choker Neck Gown',
                                                'Classic Column Gown' => 'Classic Column Gown',
                                                'Cleopatra Gown' => 'Cleopatra Gown',
                                                'Clover Neck Gown' => 'Clover Neck Gown',
                                                'Cocktail Gown' => 'Cocktail Gown',
                                                'Cold-shoulder Gown' => 'Cold-shoulder Gown',
                                                'Column Gown' => 'Column Gown',
                                                'Contrast Panel Gown' => 'Contrast Panel Gown',
                                                'Convertible Gown' => 'Convertible Gown',
                                                'Court Train Gown' => 'Court Train Gown',
                                                'Cowl Neck Gown' => 'Cowl Neck Gown',
                                                'Crepe Gown' => 'Crepe Gown',
                                                'Cut-out Gown' => 'Cut-out Gown',
                                                'Debutante Gown' => 'Debutante Gown',
                                                'Deep V-neck Gown' => 'Deep V-neck Gown',
                                                'Denim Gown' => 'Denim Gown',
                                                'Detachable Train Gown' => 'Detachable Train Gown',
                                                'Diagonal Neck Gown' => 'Diagonal Neck Gown',
                                                'Draped Gown' => 'Draped Gown',
                                                'Drop Waist Gown' => 'Drop Waist Gown',
                                                'Duster Gown' => 'Duster Gown',
                                                'Embellished Gown' => 'Embellished Gown',
                                                'Embossed Gown' => 'Embossed Gown',
                                                'Empire Waist Gown' => 'Empire Waist Gown',
                                                'Envelope Neck Gown' => 'Envelope Neck Gown',
                                                'Evening Gown' => 'Evening Gown',
                                                'Fabric Wrap Gown' => 'Fabric Wrap Gown',
                                                'Faille Gown' => 'Faille Gown',
                                                'Faux Wrap Gown' => 'Faux Wrap Gown',
                                                'Feathered Gown' => 'Feathered Gown',
                                                'Fitted Gown' => 'Fitted Gown',
                                                'Flared Gown' => 'Flared Gown',
                                                'Flipper Sleeve Gown' => 'Flipper Sleeve Gown',
                                                'Filipiniana Gown' => 'Filipiniana Gown',
                                                'Flounce Gown' => 'Flounce Gown',
                                                'Flyaway Gown' => 'Flyaway Gown',
                                                'Formal Maxi Gown' => 'Formal Maxi Gown',
                                                'Frilled Gown' => 'Frilled Gown',
                                                'Gathered Gown' => 'Gathered Gown',
                                                'Georgette Gown' => 'Georgette Gown',
                                                'Glitter Gown' => 'Glitter Gown',
                                                'Godet Gown' => 'Godet Gown',
                                                'Grecian Gown' => 'Grecian Gown',
                                                'Guest Gown' => 'Guest Gown',
                                                'Halter Gown' => 'Halter Gown',
                                                'Handkerchief Hem Gown' => 'Handkerchief Hem Gown',
                                                'High-collar Gown' => 'High-collar Gown',
                                                'High-low Gown' => 'High-low Gown',
                                                'High-neck Gown' => 'High-neck Gown',
                                                'Hourglass Gown' => 'Hourglass Gown',
                                                'Illusion Gown' => 'Illusion Gown',
                                                'Jabot Gown' => 'Jabot Gown',
                                                'Jersey Gown' => 'Jersey Gown',
                                                'Jumpsuit Gown' => 'Jumpsuit Gown',
                                                'Kaftan Gown' => 'Kaftan Gown',
                                                'Keyhole Back Gown' => 'Keyhole Back Gown',
                                                'Keyhole Neck Gown' => 'Keyhole Neck Gown',
                                                'Kimono Gown' => 'Kimono Gown',
                                                'Knit Gown' => 'Knit Gown',
                                                'Lace Gown' => 'Lace Gown',
                                                'Layered Gown' => 'Layered Gown',
                                                'Lehenga Gown' => 'Lehenga Gown',
                                                'Linen Gown' => 'Linen Gown',
                                                'Long Sleeve Gown' => 'Long Sleeve Gown',
                                                'Mermaid Gown' => 'Mermaid Gown',
                                                'Mesh Gown' => 'Mesh Gown',
                                                'Metallic Gown' => 'Metallic Gown',
                                                'Military Gown' => 'Military Gown',
                                                'Modest Gown' => 'Modest Gown',
                                                'Mother of the Bride Gown' => 'Mother of the Bride Gown',
                                                'Mousseline Gown' => 'Mousseline Gown',
                                                'Nehru Collar Gown' => 'Nehru Collar Gown',
                                                'Night Gown' => 'Night Gown',
                                                'Off-shoulder Gown' => 'Off-shoulder Gown',
                                                'Ombré Gown' => 'Ombré Gown',
                                                'One-shoulder Gown' => 'One-shoulder Gown',
                                                'Organza Gown' => 'Organza Gown',
                                                'Overlay Gown' => 'Overlay Gown',
                                                'Panel Gown' => 'Panel Gown',
                                                'Peasant Gown' => 'Peasant Gown',
                                                'Peek-a-boo Gown' => 'Peek-a-boo Gown',
                                                'Pencil Gown' => 'Pencil Gown',
                                                'Peplum Gown' => 'Peplum Gown',
                                                'Peter Pan Collar Gown' => 'Peter Pan Collar Gown',
                                                'Pinafore Gown' => 'Pinafore Gown',
                                                'Pleated Gown' => 'Pleated Gown',
                                                'Plunge Neck Gown' => 'Plunge Neck Gown',
                                                'Polo Neck Gown' => 'Polo Neck Gown',
                                                'Princess Gown' => 'Princess Gown',
                                                'Prom Gown' => 'Prom Gown',
                                                'Quilted Gown' => 'Quilted Gown',
                                                'Racerback Gown' => 'Racerback Gown',
                                                'Raglan Sleeve Gown' => 'Raglan Sleeve Gown',
                                                'Raja Poshak Gown' => 'Raja Poshak Gown',
                                                'Ravissant Gown' => 'Ravissant Gown',
                                                'Rhinestone Gown' => 'Rhinestone Gown',
                                                'Ribbon Gown' => 'Ribbon Gown',
                                                'Robe Gown' => 'Robe Gown',
                                                'Ruching Gown' => 'Ruching Gown',
                                                'Ruffle Gown' => 'Ruffle Gown',
                                                'Sari Gown' => 'Sari Gown',
                                                'Satin Gown' => 'Satin Gown',
                                                'Scalloped Gown' => 'Scalloped Gown',
                                                'Sequin Gown' => 'Sequin Gown',
                                                'Sequined Gown' => 'Sequined Gown',
                                                'Set-in Sleeve Gown' => 'Set-in Sleeve Gown',
                                                'Shantung Gown' => 'Shantung Gown',
                                                'Sheath Gown' => 'Sheath Gown',
                                                'Shiffon Gown' => 'Shiffon Gown',
                                                'Shirred Gown' => 'Shirred Gown',
                                                'Shirtwaist Gown' => 'Shirtwaist Gown',
                                                'Silk Gown' => 'Silk Gown',
                                                'Skater Gown' => 'Skater Gown',
                                                'Slip Gown' => 'Slip Gown',
                                                'Smocked Gown' => 'Smocked Gown',
                                                'Spaghetti Strap Gown' => 'Spaghetti Strap Gown',
                                                'Strapless Gown' => 'Strapless Gown',
                                                'Surplice Gown' => 'Surplice Gown',
                                                'Sweetheart Gown' => 'Sweetheart Gown',
                                                'Taffeta Gown' => 'Taffeta Gown',
                                                'Tea-length Gown' => 'Tea-length Gown',
                                                'Teddy Gown' => 'Teddy Gown',
                                                'Tented Gown' => 'Tented Gown',
                                                'Tie-back Gown' => 'Tie-back Gown',
                                                'Tie-dye Gown' => 'Tie-dye Gown',
                                                'Tiered Gown' => 'Tiered Gown',
                                                'Trapeze Gown' => 'Trapeze Gown',
                                                'Trumpet Gown' => 'Trumpet Gown',
                                                'Tulle Gown' => 'Tulle Gown',
                                                'Tunic Gown' => 'Tunic Gown',
                                                'Turtleneck Gown' => 'Turtleneck Gown',
                                                'Two-piece Gown' => 'Two-piece Gown',
                                                'Velvet Gown' => 'Velvet Gown',
                                                'Venetian Gown' => 'Venetian Gown',
                                                'V-neck Gown' => 'V-neck Gown',
                                                'Watteau Train Gown' => 'Watteau Train Gown',
                                                'Wedding Gown' => 'Wedding Gown',
                                                'Wrap Gown' => 'Wrap Gown',
                                                'Yoke Gown' => 'Yoke Gown',
                                                'Other' => 'Other',
                                            ];
                                        } elseif ($get('type') === 'Suit') {
                                            return [
                                                '2-Button Suit' => '2-Button Suit',
                                                '3-Button Suit' => '3-Button Suit',
                                                '4-Button Suit' => '4-Button Suit',
                                                '6-Button Suit' => '6-Button Suit',
                                                'Admiral Suit' => 'Admiral Suit',
                                                'Athletic Fit Suit' => 'Athletic Fit Suit',
                                                'Balmacaan Coat Suit' => 'Balmacaan Coat Suit',
                                                'Banana Republic Suit' => 'Banana Republic Suit',
                                                'Barong Tagalog' => 'Barong Tagalog',
                                                'Basketweave Suit' => 'Basketweave Suit',
                                                'Beach Suit' => 'Beach Suit',
                                                'Biker Jacket Suit' => 'Biker Jacket Suit',
                                                'Black Tie Suit' => 'Black Tie Suit',
                                                'Blazer Suit' => 'Blazer Suit',
                                                'Boating Blazer Suit' => 'Boating Blazer Suit',
                                                'Bold Stripe Suit' => 'Bold Stripe Suit',
                                                'Bomber Jacket Suit' => 'Bomber Jacket Suit',
                                                'Bond Suit' => 'Bond Suit',
                                                'Business Suit' => 'Business Suit',
                                                'Café Racer Suit' => 'Café Racer Suit',
                                                'Canvas Suit' => 'Canvas Suit',
                                                'Cape Suit' => 'Cape Suit',
                                                'Cap-toe Suit' => 'Cap-toe Suit',
                                                'Cardigan Suit' => 'Cardigan Suit',
                                                'Casual Suit' => 'Casual Suit',
                                                'Chalk Stripe Suit' => 'Chalk Stripe Suit',
                                                'Chesterfield Coat Suit' => 'Chesterfield Coat Suit',
                                                'Chino Suit' => 'Chino Suit',
                                                'Classic Fit Suit' => 'Classic Fit Suit',
                                                'Clean Front Suit' => 'Clean Front Suit',
                                                'Clergy Suit' => 'Clergy Suit',
                                                'Corduroy Suit' => 'Corduroy Suit',
                                                'Country Suit' => 'Country Suit',
                                                'Court Suit' => 'Court Suit',
                                                'Cricket Blazer Suit' => 'Cricket Blazer Suit',
                                                'Cropped Suit' => 'Cropped Suit',
                                                'Cruise Suit' => 'Cruise Suit',
                                                'Custom Suit' => 'Custom Suit',
                                                'Cutaway Coat' => 'Cutaway Coat',
                                                'Day Suit' => 'Day Suit',
                                                'Denim Suit' => 'Denim Suit',
                                                'Dinner Jacket' => 'Dinner Jacket',
                                                'Dinner Suit' => 'Dinner Suit',
                                                'Directors Cut Suit' => 'Directors Cut Suit',
                                                'Double Breasted Suit' => 'Double Breasted Suit',
                                                'Double Rider Suit' => 'Double Rider Suit',
                                                'Double Vent Suit' => 'Double Vent Suit',
                                                'Drape Cut Suit' => 'Drape Cut Suit',
                                                'Dress Suit' => 'Dress Suit',
                                                'Electric Suit' => 'Electric Suit',
                                                'Embossed Suit' => 'Embossed Suit',
                                                'Emperor Suit' => 'Emperor Suit',
                                                'Evening Suit' => 'Evening Suit',
                                                'Executive Suit' => 'Executive Suit',
                                                'Fashion Suit' => 'Fashion Suit',
                                                'Fitted Suit' => 'Fitted Suit',
                                                'Flannel Suit' => 'Flannel Suit',
                                                'Formal Suit' => 'Formal Suit',
                                                'French Cuff Suit' => 'French Cuff Suit',
                                                'Glen Check Suit' => 'Glen Check Suit',
                                                'Glen Plaid Suit' => 'Glen Plaid Suit',
                                                'Golf Suit' => 'Golf Suit',
                                                'Gurkha Trousers Suit' => 'Gurkha Trousers Suit',
                                                'Harris Tweed Suit' => 'Harris Tweed Suit',
                                                'Herringbone Suit' => 'Herringbone Suit',
                                                'Hiking Suit' => 'Hiking Suit',
                                                'Holiday Suit' => 'Holiday Suit',
                                                'Hombre Suit' => 'Hombre Suit',
                                                'Houndstooth Suit' => 'Houndstooth Suit',
                                                'Hunting Suit' => 'Hunting Suit',
                                                'Italian Suit' => 'Italian Suit',
                                                'Jacquard Suit' => 'Jacquard Suit',
                                                'Jodhpuri Suit' => 'Jodhpuri Suit',
                                                'Kashmir Suit' => 'Kashmir Suit',
                                                'Khadi Suit' => 'Khadi Suit',
                                                'Khaki Suit' => 'Khaki Suit',
                                                'Kimono Suit' => 'Kimono Suit',
                                                'Kissing Buttons Suit' => 'Kissing Buttons Suit',
                                                'Knit Suit' => 'Knit Suit',
                                                'Leather Suit' => 'Leather Suit',
                                                'Leisure Suit' => 'Leisure Suit',
                                                'Linen Suit' => 'Linen Suit',
                                                'Lounge Suit' => 'Lounge Suit',
                                                'Made-to-Measure Suit' => 'Made-to-Measure Suit',
                                                'Mandarin Collar Suit' => 'Mandarin Collar Suit',
                                                'Mariner Suit' => 'Mariner Suit',
                                                'Matador Suit' => 'Matador Suit',
                                                'Medal Ribbon Suit' => 'Medal Ribbon Suit',
                                                'Military Suit' => 'Military Suit',
                                                'Modern Fit Suit' => 'Modern Fit Suit',
                                                'Mohair Suit' => 'Mohair Suit',
                                                'Morning Coat' => 'Morning Coat',
                                                'Morning Suit' => 'Morning Suit',
                                                'Mourning Suit' => 'Mourning Suit',
                                                'Navy Blazer Suit' => 'Navy Blazer Suit',
                                                'Nehru Suit' => 'Nehru Suit',
                                                'Night Suit' => 'Night Suit',
                                                'No Vent Suit' => 'No Vent Suit',
                                                'Norfolk Jacket Suit' => 'Norfolk Jacket Suit',
                                                'Notch Lapel Suit' => 'Notch Lapel Suit',
                                                'Official Suit' => 'Official Suit',
                                                'Off-the-Rack Suit' => 'Off-the-Rack Suit',
                                                'One Button Suit' => 'One Button Suit',
                                                'Opera Suit' => 'Opera Suit',
                                                'Overcoat Suit' => 'Overcoat Suit',
                                                'Oxford Suit' => 'Oxford Suit',
                                                'Pacific Blazer Suit' => 'Pacific Blazer Suit',
                                                'Paisley Suit' => 'Paisley Suit',
                                                'Palm Beach Suit' => 'Palm Beach Suit',
                                                'Panama Suit' => 'Panama Suit',
                                                'Pant Suit' => 'Pant Suit',
                                                'Patch Pocket Suit' => 'Patch Pocket Suit',
                                                'Peak Lapel Suit' => 'Peak Lapel Suit',
                                                'Pea Coat Suit' => 'Pea Coat Suit',
                                                'Pencil Stripe Suit' => 'Pencil Stripe Suit',
                                                'Pinstripe Suit' => 'Pinstripe Suit',
                                                'Plaid Suit' => 'Plaid Suit',
                                                'Polo Suit' => 'Polo Suit',
                                                'Polyester Suit' => 'Polyester Suit',
                                                'Polo Coat Suit' => 'Polo Coat Suit',
                                                'Prince Coat' => 'Prince Coat',
                                                'Prince of Wales Suit' => 'Prince of Wales Suit',
                                                'Quilted Suit' => 'Quilted Suit',
                                                'Racing Suit' => 'Racing Suit',
                                                'Rajput Suit' => 'Rajput Suit',
                                                'Ready-to-Wear Suit' => 'Ready-to-Wear Suit',
                                                'Relaxed Fit Suit' => 'Relaxed Fit Suit',
                                                'Riding Suit' => 'Riding Suit',
                                                'Robe Suit' => 'Robe Suit',
                                                'Rough Suit' => 'Rough Suit',
                                                'Safari Suit' => 'Safari Suit',
                                                'Sailor Suit' => 'Sailor Suit',
                                                'Satin Suit' => 'Satin Suit',
                                                'School Blazer Suit' => 'School Blazer Suit',
                                                'Seersucker Suit' => 'Seersucker Suit',
                                                'Separates Suit' => 'Separates Suit',
                                                'Shawl Collar Suit' => 'Shawl Collar Suit',
                                                'Shawl Lapel Tuxedo' => 'Shawl Lapel Tuxedo',
                                                'Shell Suit' => 'Shell Suit',
                                                'Shirt Jacket Suit' => 'Shirt Jacket Suit',
                                                'Side Vent Suit' => 'Side Vent Suit',
                                                'Silk Suit' => 'Silk Suit',
                                                'Single Breasted Suit' => 'Single Breasted Suit',
                                                'Single Vent Suit' => 'Single Vent Suit',
                                                'Skirt Suit' => 'Skirt Suit',
                                                'Slim Fit Suit' => 'Slim Fit Suit',
                                                'Smoking Jacket Suit' => 'Smoking Jacket Suit',
                                                'Space Suit' => 'Space Suit',
                                                'Spanish Suit' => 'Spanish Suit',
                                                'Sport Coat Suit' => 'Sport Coat Suit',
                                                'Sports Suit' => 'Sports Suit',
                                                'Square Suit' => 'Square Suit',
                                                'Stroller Suit' => 'Stroller Suit',
                                                'Summer Suit' => 'Summer Suit',
                                                'Super 100s Suit' => 'Super 100s Suit',
                                                'Super 150s Suit' => 'Super 150s Suit',
                                                'Surcoat Suit' => 'Surcoat Suit',
                                                'Suspenders Suit' => 'Suspenders Suit',
                                                'Tailcoat' => 'Tailcoat',
                                                'Tailored Suit' => 'Tailored Suit',
                                                'Tartan Suit' => 'Tartan Suit',
                                                'Tennis Blazer Suit' => 'Tennis Blazer Suit',
                                                'Three Button Roll Suit' => 'Three Button Roll Suit',
                                                'Three Piece Suit' => 'Three Piece Suit',
                                                'Tracksuit' => 'Tracksuit',
                                                'Trapeze Suit' => 'Trapeze Suit',
                                                'Travel Suit' => 'Travel Suit',
                                                'Trench Coat Suit' => 'Trench Coat Suit',
                                                'Trousers Suit' => 'Trousers Suit',
                                                'T-Shirt Suit' => 'T-Shirt Suit',
                                                'Tunic Suit' => 'Tunic Suit',
                                                'Tuxedo' => 'Tuxedo',
                                                'Tweed Suit' => 'Tweed Suit',
                                                'Two Button Suit' => 'Two Button Suit',
                                                'Two Piece Suit' => 'Two Piece Suit',
                                                'Uniform Suit' => 'Uniform Suit',
                                                'Unstructured Suit' => 'Unstructured Suit',
                                                'Utility Suit' => 'Utility Suit',
                                                'Valet Suit' => 'Valet Suit',
                                                'Varsity Suit' => 'Varsity Suit',
                                                'Velvet Suit' => 'Velvet Suit',
                                                'Ventless Suit' => 'Ventless Suit',
                                                'Vest Suit' => 'Vest Suit',
                                                'Vintage Suit' => 'Vintage Suit',
                                                'Walking Suit' => 'Walking Suit',
                                                'Wardrobe Suit' => 'Wardrobe Suit',
                                                'Wash-and-Wear Suit' => 'Wash-and-Wear Suit',
                                                'Wedding Suit' => 'Wedding Suit',
                                                'Weekender Suit' => 'Weekender Suit',
                                                'Western Suit' => 'Western Suit',
                                                'Whipcord Suit' => 'Whipcord Suit',
                                                'White Tie Suit' => 'White Tie Suit',
                                                'Windsor Suit' => 'Windsor Suit',
                                                'Wing Collar Suit' => 'Wing Collar Suit',
                                                'Winter Suit' => 'Winter Suit',
                                                'Wool Suit' => 'Wool Suit',
                                                'Woolen Suit' => 'Woolen Suit',
                                                'Work Suit' => 'Work Suit',
                                                'Yachting Suit' => 'Yachting Suit',
                                                'Zoot Suit' => 'Zoot Suit',
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
                                    ->searchable()
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
                                Select::make('product_events')
                                    ->label('Event Categories')
                                    ->helperText('Select multiple event categories')
                                    ->multiple()
                                    ->options([
                                        // Weddings & Related
                                        'Wedding' => 'Wedding',
                                        'Engagement Party' => 'Engagement Party',
                                        'Bridal Shower' => 'Bridal Shower',
                                        'Rehearsal Dinner' => 'Rehearsal Dinner',
                                        // Formal & Red Carpet
                                        'Gala' => 'Gala',
                                        'Black Tie Event' => 'Black Tie Event',
                                        'Awards Night' => 'Awards Night',
                                        'Charity Ball' => 'Charity Ball',
                                        'Red Carpet Event' => 'Red Carpet Event',
                                        // School/Formal Youth Events
                                        'Prom' => 'Prom',
                                        'Graduation' => 'Graduation',
                                        'Homecoming' => 'Homecoming',
                                        'Formal Dance' => 'Formal Dance',
                                        // Cultural & Ceremonial
                                        'Debut / 18th Birthday' => 'Debut / 18th Birthday',
                                        'Quinceañera' => 'Quinceañera',
                                        'Pageant' => 'Pageant',
                                        // Professional/Formal Business
                                        'Corporate Event' => 'Corporate Event',
                                        'Business Gala' => 'Business Gala',
                                        // Holiday & Seasonal
                                        'Christmas Party' => 'Christmas Party',
                                        "New Year's Eve" => "New Year's Eve",
                                        'Holiday Ball' => 'Holiday Ball',
                                        // Special Shoots / Exhibitions
                                        'Photo Shoot' => 'Photo Shoot',
                                        'Fashion Show' => 'Fashion Show',
                                        // Family Occasions
                                        'Anniversary' => 'Anniversary',
                                        'Birthday Celebration' => 'Birthday Celebration',
                                        // Other
                                        'Other' => 'Other',
                                    ])
                                    ->required()
                                    ->placeholder('Select event categories...')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->afterStateHydrated(function (Select $component, $state) {
                                        // When loading data for editing, convert existing events to array
                                        if (is_string($state) && !empty($state)) {
                                            $events = array_map('trim', explode(',', $state));
                                            $component->state($events);
                                        }
                                    }),
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
                            ->maxSize(10240)  // 10MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->deletable(true)
                            ->openable()
                            ->required()
                            ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                            ->columnSpan(1),
                        FileUpload::make('images')
                            ->label('Additional Gallery Images')
                            ->helperText('Upload additional images to show different angles and details of your product. Maximum 10 images, 10MB each')
                            ->multiple()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('product-images')
                            ->image()
                            ->imageEditor()
                            ->maxFiles(10)
                            ->maxSize(10240)  // 10MB per file
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
                            ->visible(fn($get) => $get('type') === 'Gown')
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
                            ->visible(fn($get) => $get('type') === 'Suit')
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
                            ->visible(fn($get) => $get('type') === 'Suit')
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
