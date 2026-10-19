<?php

namespace Database\Seeders;

use App\Models\InsuranceCompany;
use Illuminate\Database\Seeder;

class InsuranceCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'شركة بوبا العربية للتأمين التعاوني (Bupa Arabia)',
                'code' => 'BUPA',
                'contact_person' => 'إدارة الموافقات الطبية',
                'phone' => '8002440307',
                'email' => 'approvals@bupa.com.sa',
                'address' => 'جدة - شارع الأندلس',
                'discount_percentage' => 15.00,
                'notes' => 'تغطية شاملة لكافة الفئات VIP والماسية والذهبية.',
                'is_active' => true,
            ],
            [
                'name' => 'شركة التعاونية للتأمين (Tawuniya)',
                'code' => 'TAWUNIYA',
                'contact_person' => 'قسم المطالبات الصحية',
                'phone' => '920019990',
                'email' => 'medicalclaims@tawuniya.com.sa',
                'address' => 'الرياض - طريق الثمامة',
                'discount_percentage' => 12.00,
                'notes' => 'شبكة طبية معتمدة.',
                'is_active' => true,
            ],
            [
                'name' => 'شركة المتوسط والخليج للتأمين (Medgulf)',
                'code' => 'MEDGULF',
                'contact_person' => 'إدارة شبكة المزودين',
                'phone' => '8004414442',
                'email' => 'providers@medgulf.com.sa',
                'address' => 'الرياض - شارع المعذر',
                'discount_percentage' => 10.00,
                'notes' => 'موافقات سريعة عبر بوابة التأمين.',
                'is_active' => true,
            ],
            [
                'name' => 'شركة تكافل الراجحي (Al Rajhi Takaful)',
                'code' => 'RAJHI-TAKAFUL',
                'contact_person' => 'خدمات الرعاية الصحية',
                'phone' => '920004414',
                'email' => 'health@alrajhitakaful.com',
                'address' => 'الرياض - حي الملز',
                'discount_percentage' => 10.00,
                'notes' => 'تأمين متوافق مع أحكام الشريعة الإسلامية.',
                'is_active' => true,
            ],
            [
                'name' => 'شركة سلامة للتأمين التعاوني (Salama)',
                'code' => 'SALAMA',
                'contact_person' => 'قسم الرعاية الطبية',
                'phone' => '8002440000',
                'email' => 'care@salama.com.sa',
                'address' => 'جدة - شارع المدينة',
                'discount_percentage' => 8.00,
                'notes' => 'معتمد للشركات والأفراد.',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $comp) {
            InsuranceCompany::updateOrCreate(['code' => $comp['code']], $comp);
        }
    }
}
