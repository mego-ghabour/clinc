<?php

namespace Database\Seeders;

use App\Models\LabTest;
use Illuminate\Database\Seeder;

class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        $tests = [
            [
                'name' => 'صورة دم كاملة (CBC)',
                'code' => 'LAB-CBC',
                'category' => 'أمراض دم',
                'price' => 80.00,
                'sample_type' => 'دم كامل (EDTA)',
                'normal_range' => 'WBC: 4.0-11.0, RBC: 4.5-5.9, Hb: 13.5-17.5 g/dL',
                'unit' => 'متعدد',
                'description' => 'فحص كريات الدم البيضاء والحمراء والصفائح الدموية والهيموجلوبين',
                'is_active' => true,
            ],
            [
                'name' => 'السكر التراكمي (HbA1c)',
                'code' => 'LAB-A1C',
                'category' => 'كيمياء حيوية',
                'price' => 100.00,
                'sample_type' => 'دم كامل',
                'normal_range' => '4.5 - 5.6 % (طبيعي)',
                'unit' => '%',
                'description' => 'قياس متوسط معدل السكر في الدم لآخر 3 أشهر',
                'is_active' => true,
            ],
            [
                'name' => 'سكر الدم الصائم (Fasting Blood Glucose)',
                'code' => 'LAB-FBG',
                'category' => 'كيمياء حيوية',
                'price' => 40.00,
                'sample_type' => 'مصل دم',
                'normal_range' => '70 - 99',
                'unit' => 'mg/dL',
                'description' => 'قياس مستوى الجلوكوز بعد صيام 8 إلى 12 ساعة',
                'is_active' => true,
            ],
            [
                'name' => 'وظائف الكلى - الكرياتينين (Creatinine)',
                'code' => 'LAB-CREAT',
                'category' => 'كيمياء حيوية',
                'price' => 60.00,
                'sample_type' => 'مصل دم',
                'normal_range' => '0.7 - 1.3',
                'unit' => 'mg/dL',
                'description' => 'تقييم كفاءة عمل الكليتين وتصفية الفضلات',
                'is_active' => true,
            ],
            [
                'name' => 'وظائف الكبد (SGPT / ALT)',
                'code' => 'LAB-ALT',
                'category' => 'كيمياء حيوية',
                'price' => 60.00,
                'sample_type' => 'مصل دم',
                'normal_range' => '7 - 56',
                'unit' => 'U/L',
                'description' => 'إنزيم الكبد للكشف عن التهابات أو تلف خلايا الكبد',
                'is_active' => true,
            ],
            [
                'name' => 'فيتامين د (Vitamin D - 25-OH)',
                'code' => 'LAB-VITD',
                'category' => 'هرمونات وفيتامينات',
                'price' => 150.00,
                'sample_type' => 'مصل دم',
                'normal_range' => '30 - 100',
                'unit' => 'ng/mL',
                'description' => 'فحص تركيز فيتامين د لصحة العظام والمناعة',
                'is_active' => true,
            ],
            [
                'name' => 'هرمون الغدة الدرقية (TSH)',
                'code' => 'LAB-TSH',
                'category' => 'هرمونات وفيتامينات',
                'price' => 90.00,
                'sample_type' => 'مصل دم',
                'normal_range' => '0.4 - 4.0',
                'unit' => 'uIU/mL',
                'description' => 'فحص كفاءة ونشاط الغدة الدرقية',
                'is_active' => true,
            ],
            [
                'name' => 'فحص البول الكامل (Urine Analysis)',
                'code' => 'LAB-URINE',
                'category' => 'بول وسوائل',
                'price' => 50.00,
                'sample_type' => 'عينة بول عشوائية',
                'normal_range' => 'Clear, Color: Yellow, Pus: 0-5, RBCs: 0-2',
                'unit' => 'ميكروسكوبي',
                'description' => 'كشف التهابات المسالك البولية والأملاح والبروتين والدم الخفي',
                'is_active' => true,
            ],
        ];

        foreach ($tests as $test) {
            LabTest::firstOrCreate(['code' => $test['code']], $test);
        }
    }
}
