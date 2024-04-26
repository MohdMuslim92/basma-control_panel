<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define provinces associated with their respective states
        $provinces = [
            // البحر الأحمر (1)
            ['name' => 'جييت', 'state_id' => 1],
            ['name' => 'بورتسودان', 'state_id' => 1],
            ['name' => 'القوليب وأوليب', 'state_id' => 1],
            ['name' => 'حلايب', 'state_id' => 1],
            ['name' => 'درديب', 'state_id' => 1],
            ['name' => 'سنكات', 'state_id' => 1],
            ['name' => 'سواكن', 'state_id' => 1],
            ['name' => 'طوكر', 'state_id' => 1],
            ['name' => 'هيا', 'state_id' => 1],

            // الجزيرة (2)
            ['name' => 'الحصاحيصا', 'state_id' => 2],
            ['name' => 'الكاملين', 'state_id' => 2],
            ['name' => 'المناقل', 'state_id' => 2],
            ['name' => 'أم القرى', 'state_id' => 2],
            ['name' => 'جنوب الجزيرة', 'state_id' => 2],
            ['name' => 'شرق الجزيرة', 'state_id' => 2],
            ['name' => 'مدني الكبرى', 'state_id' => 2],

            // الخرطوم (3)
            ['name' => 'الخرطوم', 'state_id' => 3],
            ['name' => 'بحري', 'state_id' => 3],
            ['name' => 'أم بدة', 'state_id' => 3],
            ['name' => 'أم درمان', 'state_id' => 3],
            ['name' => 'جبل أولياء', 'state_id' => 3],
            ['name' => 'كرري', 'state_id' => 3],
            ['name' => 'شرق النيل', 'state_id' => 3],

            // الشمالية (4)
            ['name' => 'الدبة', 'state_id' => 4],
            ['name' => 'دنقلا', 'state_id' => 4],
            ['name' => 'مروي', 'state_id' => 4],
            ['name' => 'وادي حلفا', 'state_id' => 4],

            // القضارف (5)
            ['name' => 'البطانة', 'state_id' => 5],
            ['name' => 'الرهد', 'state_id' => 5],
            ['name' => 'الفاو', 'state_id' => 5],
            ['name' => 'الفشقة', 'state_id' => 5],
            ['name' => 'القريشة', 'state_id' => 5],
            ['name' => 'القلابات الشرقية', 'state_id' => 5],
            ['name' => 'القلابات الغربية', 'state_id' => 5],
            ['name' => 'بلدية القضارف', 'state_id' => 5],
            ['name' => 'قلع النحل', 'state_id' => 5],

            // النيل الأبيض (6)
            ['name' => 'الجبلين', 'state_id' => 6],
            ['name' => 'الدويم', 'state_id' => 6],
            ['name' => 'القطينة', 'state_id' => 6],
            ['name' => 'أم رمته', 'state_id' => 6],
            ['name' => 'كوستي', 'state_id' => 6],

            // النيل الأزرق (7)
            ['name' => 'الدمازين', 'state_id' => 7],
            ['name' => 'الروصيرص', 'state_id' => 7],
            ['name' => 'الكرمك', 'state_id' => 7],
            ['name' => 'باو', 'state_id' => 7],
            ['name' => 'قيسان', 'state_id' => 7],

            // جنوب دارفور (8)
            ['name' => 'الردوم', 'state_id' => 8],
            ['name' => 'السلام', 'state_id' => 8],
            ['name' => 'السنطة', 'state_id' => 8],
            ['name' => 'الوحدة', 'state_id' => 8],
            ['name' => 'أم دافوق', 'state_id' => 8],
            ['name' => 'بليل', 'state_id' => 8],
            ['name' => 'تلس', 'state_id' => 8],
            ['name' => 'دمسو', 'state_id' => 8],
            ['name' => 'رهيد البردي', 'state_id' => 8],
            ['name' => 'شاطايا', 'state_id' => 8],
            ['name' => 'شرق الجبل', 'state_id' => 8],
            ['name' => 'عد الفرسان', 'state_id' => 8],
            ['name' => 'قريضة', 'state_id' => 8],
            ['name' => 'كاس', 'state_id' => 8],
            ['name' => 'كتم', 'state_id' => 8],
            ['name' => 'كنيلا', 'state_id' => 8],
            ['name' => 'مرشنج', 'state_id' => 8],
            ['name' => 'نتيقا', 'state_id' => 8],
            ['name' => 'نيالا', 'state_id' => 8],

            // جنوب كردفان (9)
            ['name' => 'الدلنج', 'state_id' => 9],
            ['name' => 'الريف الشرقي', 'state_id' => 9],
            ['name' => 'السلام', 'state_id' => 9],
            ['name' => 'العباسية', 'state_id' => 9],
            ['name' => 'القوز', 'state_id' => 9],
            ['name' => 'أبو جبيهة', 'state_id' => 9],
            ['name' => 'أم دورين', 'state_id' => 9],
            ['name' => 'برام', 'state_id' => 9],
            ['name' => 'تلودي', 'state_id' => 9],
            ['name' => 'دلامي', 'state_id' => 9],
            ['name' => 'رشاد', 'state_id' => 9],
            ['name' => 'كادقلي', 'state_id' => 9],
            ['name' => 'كليك', 'state_id' => 9],
            ['name' => 'هبيلا', 'state_id' => 9],
            ['name' => 'هيبان', 'state_id' => 9],

            // سنار (10)
            ['name' => 'الدندر', 'state_id' => 10],
            ['name' => 'سنار', 'state_id' => 10],
            ['name' => 'سنجة', 'state_id' => 10],

            // شرق دارفور (11)
            ['name' => 'الضعين', 'state_id' => 11],
            ['name' => 'الفردوس', 'state_id' => 11],
            ['name' => 'أبو جابرة', 'state_id' => 11],
            ['name' => 'أبو كارنكا', 'state_id' => 11],
            ['name' => 'بحر العرب', 'state_id' => 11],
            ['name' => 'شعيرية', 'state_id' => 11],
            ['name' => 'عديلة', 'state_id' => 11],
            ['name' => 'عسلاية', 'state_id' => 11],
            ['name' => 'ياسين', 'state_id' => 11],

            // شمال دارفور (12)
            ['name' => 'الفاشر', 'state_id' => 12],
            ['name' => 'أم كدادة', 'state_id' => 12],
            ['name' => 'كبكابية', 'state_id' => 12],
            ['name' => 'كتم', 'state_id' => 12],
            ['name' => 'كلبس', 'state_id' => 12],
            ['name' => 'مليط', 'state_id' => 12],

            // شمال كردفان (13)
            ['name' => 'أم دم', 'state_id' => 13],
            ['name' => 'أم روابة', 'state_id' => 13],
            ['name' => 'بارا', 'state_id' => 13],
            ['name' => 'جبرة الشيخ', 'state_id' => 13],
            ['name' => 'سودري', 'state_id' => 13],
            ['name' => 'شيكان', 'state_id' => 13],
            ['name' => 'كلبس', 'state_id' => 13],

            // غرب دارفور (14)
            ['name' => 'الجنينة', 'state_id' => 14],
            ['name' => 'بيضة', 'state_id' => 14],
            ['name' => 'جبل مون', 'state_id' => 14],
            ['name' => 'سربا', 'state_id' => 14],
            ['name' => 'قوز برنقا', 'state_id' => 14],
            ['name' => 'كرينك', 'state_id' => 14],
            ['name' => 'كلبس', 'state_id' => 14],
            ['name' => 'هبيلة', 'state_id' => 14],

            // كسلا (15)
            ['name' => 'القاش', 'state_id' => 15],
            ['name' => 'ريفي كسلا', 'state_id' => 15],
            ['name' => 'كسلا', 'state_id' => 15],
            ['name' => 'نهر عطبرة', 'state_id' => 15],
            ['name' => 'همشكوريب', 'state_id' => 15],

            // نهر النيل (16)
            ['name' => 'المتمة', 'state_id' => 16],
            ['name' => 'الدامر', 'state_id' => 16],
            ['name' => 'أبو حمد', 'state_id' => 16],
            ['name' => 'بربر', 'state_id' => 16],
            ['name' => 'عطبرة', 'state_id' => 16],

            // وسط دارفور (17)
            ['name' => 'أزوم', 'state_id' => 17],
            ['name' => 'أم دخن', 'state_id' => 17],
            ['name' => 'بندسي', 'state_id' => 17],
            ['name' => 'جبل مرة', 'state_id' => 17],
            ['name' => 'روكورو', 'state_id' => 17],
            ['name' => 'زالنجي', 'state_id' => 17],
            ['name' => 'مكجر', 'state_id' => 17],
            ['name' => 'وادي صالح', 'state_id' => 17],

            // غرب كردفان (18)
            ['name' => 'الفولة', 'state_id' => 18],
            ['name' => 'النهود', 'state_id' => 18],
            ['name' => 'أبيي', 'state_id' => 18],
            ['name' => 'بابنوسة', 'state_id' => 18],
            ['name' => 'هبيلا', 'state_id' => 18],
        ];

        // Insert provinces into the database
        foreach ($provinces as $province) {
            Province::create($province);
        }

    }}
