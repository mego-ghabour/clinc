<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clinic;
use App\Models\User;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\Patient;
use App\Models\Appointment;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClinicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Clinics
        $clinicsData = [
            [
                'name' => 'عيادة الباطنة والجهاز الهضمي',
                'code' => 'CLN-INT',
                'icon' => 'fa-stethoscope',
                'color' => '#3B82F6',
                'daily_quota' => 30,
                'start_time' => '08:00',
                'end_time' => '16:00',
                'status' => 'نشطة',
                'notes' => 'تشخيص وعلاج أمراض الباطنة، السكري، وضغط الدم، ومشاكل الجهاز الهضمي والكبد.',
                'sort_order' => 1,
            ],
            [
                'name' => 'عيادة طب وجراحة العيون',
                'code' => 'CLN-EYE',
                'icon' => 'fa-eye',
                'color' => '#10B981',
                'daily_quota' => 25,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'status' => 'نشطة',
                'notes' => 'فحص قاع العين، تصحيح النظر، ومتابعة حالات المياه البيضاء والزرقاء.',
                'sort_order' => 2,
            ],
            [
                'name' => 'عيادة العظام والمفاصل',
                'code' => 'CLN-ORTH',
                'icon' => 'fa-bone',
                'color' => '#F59E0B',
                'daily_quota' => 25,
                'start_time' => '08:30',
                'end_time' => '16:30',
                'status' => 'نشطة',
                'notes' => 'علاج الكسور، آلام المفاصل والفقرات، وإصابات الملاعب.',
                'sort_order' => 3,
            ],
            [
                'name' => 'عيادة الأطفال وحديثي الولادة',
                'code' => 'CLN-PED',
                'icon' => 'fa-baby',
                'color' => '#EC4899',
                'daily_quota' => 35,
                'start_time' => '08:00',
                'end_time' => '18:00',
                'status' => 'نشطة',
                'notes' => 'متابعة نمو الطفل، التطعيمات الأساسية، وعلاج أمراض الطفولة وحديثي الولادة.',
                'sort_order' => 4,
            ],
            [
                'name' => 'عيادة طب وجراحة الأسنان',
                'code' => 'CLN-DENT',
                'icon' => 'fa-tooth',
                'color' => '#06B6D4',
                'daily_quota' => 20,
                'start_time' => '09:00',
                'end_time' => '20:00',
                'status' => 'نشطة',
                'notes' => 'طب الأسنان التجميلي، زراعة الأسنان، علاج الجذور، وتنظيف وتبييض الأسنان.',
                'sort_order' => 5,
            ],
            [
                'name' => 'عيادة النساء والتوليد',
                'code' => 'CLN-GYN',
                'icon' => 'fa-female',
                'color' => '#8B5CF6',
                'daily_quota' => 25,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'status' => 'نشطة',
                'notes' => 'متابعة الحمل، السونار رباعي الأبعاد، وعلاج أمراض النساء وتأخر الإنجاب.',
                'sort_order' => 6,
            ],
            [
                'name' => 'عيادة الجلدية والتجميل والليزك',
                'code' => 'CLN-DERM',
                'icon' => 'fa-spa',
                'color' => '#F43F5E',
                'daily_quota' => 25,
                'start_time' => '10:00',
                'end_time' => '18:00',
                'status' => 'نشطة',
                'notes' => 'العلاج بالليزر، نضارة البشرة، علاج حب الشباب، والحقن التجميلي (بوتوكس وفيلر).',
                'sort_order' => 7,
            ],
            [
                'name' => 'عيادة القلب والأوعية الدموية',
                'code' => 'CLN-CARD',
                'icon' => 'fa-heartbeat',
                'color' => '#EF4444',
                'daily_quota' => 20,
                'start_time' => '09:00',
                'end_time' => '15:00',
                'status' => 'نشطة',
                'notes' => 'تخطيط القلب، إيكو القلب، ومتابعة قصور الشرايين وارتفاع ضغط الدم.',
                'sort_order' => 8,
            ],
            [
                'name' => 'عيادة الأنف والأذن والحنجرة',
                'code' => 'CLN-ENT',
                'icon' => 'fa-head-side-cough',
                'color' => '#14B8A6',
                'daily_quota' => 25,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'status' => 'نشطة',
                'notes' => 'مناظير الأنف والأذن، قياس السمع، وعلاج حساسية والتهابات الجيوب الأنفية.',
                'sort_order' => 9,
            ],
        ];

        $clinics = [];
        foreach ($clinicsData as $c) {
            $clinic = Clinic::updateOrCreate(['code' => $c['code']], $c);
            $clinics[$c['code']] = $clinic;
        }

        // 2. Roles verification
        $doctorRole = Role::firstOrCreate(['name' => 'طبيب']);
        $receptionistRole = Role::firstOrCreate(['name' => 'موظف استقبال']);
        $labRole = Role::firstOrCreate(['name' => 'أخصائي مختبر']);
        $pharmaRole = Role::firstOrCreate(['name' => 'صيدلي']);
        $radRole = Role::firstOrCreate(['name' => 'أخصائي أشعة']);

        // 3. Create Doctors
        $doctorsData = [
            [
                'name' => 'د. أحمد محمود الشريف',
                'username' => 'dr.ahmed',
                'email' => 'dr.ahmed@clinic.test',
                'phone' => '0501112233',
                'license_number' => 'MED-SA-88301',
                'specialization' => 'استشاري أمراض الباطنة والجهاز الهضمي',
                'consultation_duration' => 20,
                'clinic_code' => 'CLN-INT',
            ],
            [
                'name' => 'د. سارة خالد العتيبي',
                'username' => 'dr.sara',
                'email' => 'dr.sara@clinic.test',
                'phone' => '0502223344',
                'license_number' => 'MED-SA-88302',
                'specialization' => 'استشارية طب وجراحة العيون والليزك',
                'consultation_duration' => 20,
                'clinic_code' => 'CLN-EYE',
            ],
            [
                'name' => 'د. محمد عبد الرحمن القحطاني',
                'username' => 'dr.mohammed',
                'email' => 'dr.mohammed@clinic.test',
                'phone' => '0503334455',
                'license_number' => 'MED-SA-88303',
                'specialization' => 'استشاري جراحة العظام والمفاصل الصناعية',
                'consultation_duration' => 30,
                'clinic_code' => 'CLN-ORTH',
            ],
            [
                'name' => 'د. ريم فهد الدوسري',
                'username' => 'dr.reem',
                'email' => 'dr.reem@clinic.test',
                'phone' => '0504445566',
                'license_number' => 'MED-SA-88304',
                'specialization' => 'أخصائية طب الأطفال وحديثي الولادة',
                'consultation_duration' => 15,
                'clinic_code' => 'CLN-PED',
            ],
            [
                'name' => 'د. طارق زياد المنصور',
                'username' => 'dr.tariq',
                'email' => 'dr.tariq@clinic.test',
                'phone' => '0505556677',
                'license_number' => 'MED-SA-88305',
                'specialization' => 'استشاري زراعة وتجميل الأسنان',
                'consultation_duration' => 30,
                'clinic_code' => 'CLN-DENT',
            ],
            [
                'name' => 'د. نورة عبد الله السالم',
                'username' => 'dr.noura',
                'email' => 'dr.noura@clinic.test',
                'phone' => '0506667788',
                'license_number' => 'MED-SA-88306',
                'specialization' => 'استشارية النساء والولادة وجراحة المناظير',
                'consultation_duration' => 25,
                'clinic_code' => 'CLN-GYN',
            ],
            [
                'name' => 'د. حسام إبراهيم النجار',
                'username' => 'dr.hossam',
                'email' => 'dr.hossam@clinic.test',
                'phone' => '0507778899',
                'license_number' => 'MED-SA-88307',
                'specialization' => 'أخصائي الأمراض الجلدية والعلاج بالليزر',
                'consultation_duration' => 20,
                'clinic_code' => 'CLN-DERM',
            ],
            [
                'name' => 'د. عمر فاروق الصالح',
                'username' => 'dr.omar',
                'email' => 'dr.omar@clinic.test',
                'phone' => '0508889900',
                'license_number' => 'MED-SA-88308',
                'specialization' => 'استشاري أمراض القلب والأوعية الدموية وقسطرة الشرايين',
                'consultation_duration' => 30,
                'clinic_code' => 'CLN-CARD',
            ],
        ];

        $doctors = [];
        foreach ($doctorsData as $d) {
            $clinic = $clinics[$d['clinic_code']] ?? null;

            $doctor = User::updateOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'username' => $d['username'],
                    'password' => Hash::make('password'),
                    'role' => 'طبيب',
                    'status' => 'نشط',
                    'phone' => $d['phone'],
                    'license_number' => $d['license_number'],
                    'specialization' => $d['specialization'],
                    'consultation_duration' => $d['consultation_duration'],
                    'clinic_id' => $clinic?->id,
                    'is_present' => true,
                ]
            );

            $doctor->syncRoles([$doctorRole]);
            $doctors[$d['username']] = $doctor;

            // Set head doctor of clinic if empty
            if ($clinic && empty($clinic->head_doctor_id)) {
                $clinic->update(['head_doctor_id' => $doctor->id]);
            }

            // Create Doctor Schedules for Sunday(0) to Thursday(4)
            for ($day = 0; $day <= 6; $day++) {
                $isWorking = in_array($day, [0, 1, 2, 3, 4]); // Sun - Thu
                $shifts = $isWorking ? [
                    ['start' => '09:00', 'end' => '13:00'],
                    ['start' => '16:00', 'end' => '20:00']
                ] : [];

                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'shifts' => $shifts,
                        'consultation_duration' => $d['consultation_duration'],
                        'is_working' => $isWorking,
                    ]
                );
            }
        }

        // 4. Create Clinic Staff
        $staffMembers = [
            [
                'name' => 'فهد عبد الله المطيري',
                'username' => 'reception',
                'email' => 'receptionist@clinic.test',
                'phone' => '0509990011',
                'role' => 'موظف استقبال',
                'spatie_role' => $receptionistRole,
            ],
            [
                'name' => 'كريم ممدوح السيد',
                'username' => 'lab',
                'email' => 'lab@clinic.test',
                'phone' => '0509990022',
                'role' => 'أخصائي مختبر',
                'spatie_role' => $labRole,
            ],
            [
                'name' => 'منى زكي عبد العزيز',
                'username' => 'pharmacy',
                'email' => 'pharmacy@clinic.test',
                'phone' => '0509990033',
                'role' => 'صيدلي',
                'spatie_role' => $pharmaRole,
            ],
            [
                'name' => 'زياد طلال الرفاعي',
                'username' => 'radiology',
                'email' => 'radiology@clinic.test',
                'phone' => '0509990044',
                'role' => 'أخصائي أشعة',
                'spatie_role' => $radRole,
            ],
        ];

        foreach ($staffMembers as $s) {
            $user = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'username' => $s['username'],
                    'password' => Hash::make('password'),
                    'role' => $s['role'],
                    'status' => 'نشط',
                    'phone' => $s['phone'],
                ]
            );
            $user->syncRoles([$s['spatie_role']]);
        }

        // 5. Medical Services
        $servicesData = [
            ['name' => 'كشف استشاري باطنة وجهاز هضمي', 'price' => 200.00, 'description' => 'فحص سريري كامل، قياس الضغط والسكر ومراجعة التاريخ المرضي.'],
            ['name' => 'فحص موجات فوق صوتية للبطن والسونار', 'price' => 250.00, 'description' => 'فحص بالموجات فوق الصوتية لأعضاء البطن والكبد والكلى والبنكرياس.'],
            ['name' => 'كشف واستشارة طب الأطفال', 'price' => 150.00, 'description' => 'فحص عام للأطفال وحديثي الولادة ومتابعة التطور والنمو.'],
            ['name' => 'تطعيمات ومتابعة نمو الطفل', 'price' => 120.00, 'description' => 'إعطاء اللقاحات الدورية وجداول التحصين مع قياس الوزن والطول ومحيط الرأس.'],
            ['name' => 'تنظيف وتلميع الأسنان وإزالة الجير', 'price' => 180.00, 'description' => 'إزالة الرواسب الجيرية والتصبغات وتلميع الأسنان بأحدث أجهزة الألتراسونيك.'],
            ['name' => 'حشو عصب وعلاج جذور الأسنان', 'price' => 450.00, 'description' => 'علاج وحشو العصب التالف بجلسة واحدة أو جلستين بأحدث التقنيات.'],
            ['name' => 'تبييض أسنان احترافي بالليزر', 'price' => 700.00, 'description' => 'جلسة تبييض سريعة وفعالة باستخدام تقنية الليزر والضوء البارد.'],
            ['name' => 'فحص قاع العين وقياس ضغط العين', 'price' => 160.00, 'description' => 'فحص شامل لشبكية العين والعصب البصري وقياس دقيق لضغط العين.'],
            ['name' => 'فحص وتقييم عمليات الليزك', 'price' => 300.00, 'description' => 'تخطيط قرنية العين وإجراء قياسات دقيقة لتقييم ملاءمة تصحيح الإبصار بالليزك.'],
            ['name' => 'كشف استشاري عظام ومفاصل', 'price' => 200.00, 'description' => 'تشخيص آلام الركبة والفقرات وإصابات الأربطة والمفاصل.'],
            ['name' => 'حقن مفاصل بالبلازما الغنية بالصفائح PRP', 'price' => 500.00, 'description' => 'حقن علاجي متطور لتجديد الغضاريف وتخفيف آلام خشونة الركبة والمفاصل.'],
            ['name' => 'سونار حمل ومتابعة الجنين رباعي الأبعاد 4D', 'price' => 350.00, 'description' => 'تصوير دقيق وعالي الوضوح للجنين وملامحه والكشف عن صحة وتغذية الحمل.'],
            ['name' => 'كشف نساء وتوليد شامل', 'price' => 220.00, 'description' => 'فحص سريري ومسحة عنق الرحم ومتابعة انتظام الهرمونات والدورة الشهرية.'],
            ['name' => 'جلسة تقشير كربوني ونضارة للبشرة', 'price' => 300.00, 'description' => 'تجديد خلايا البشرة وتضييق المسام والتخلص من البقع والشوائب.'],
            ['name' => 'تخطيط قلب كهربائي متقدم ECG', 'price' => 120.00, 'description' => 'رسم كهربائي للقلب مكون من 12 مساراً للكشف عن اضطراب النبض والجلطات.'],
            ['name' => 'فحص إيكو للقلب مع التقرير الشامل', 'price' => 400.00, 'description' => 'تصوير بالموجات فوق الصوتية لعضلة القلب وصماماته وسريان الدم.'],
        ];

        $services = [];
        foreach ($servicesData as $s) {
            $service = Service::updateOrCreate(
                ['name' => $s['name']],
                [
                    'price' => $s['price'],
                    'description' => $s['description'],
                    'is_active' => true,
                ]
            );
            $services[] = $service;
        }

        // 6. Patients
        $patientsData = [
            [
                'first_name' => 'عبد الله',
                'father_name' => 'محمد',
                'grandfather_name' => 'سعد',
                'family_name' => 'الغامدي',
                'gender' => 'ذكر',
                'date_of_birth' => '1985-04-12',
                'id_type' => 'هوية وطنية',
                'id_number' => '1088492014',
                'mobile' => '0551234567',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'district' => 'الملقا',
                'blood_type' => 'O+',
                'emergency_contact' => '0509876543 (شقيقه)',
                'notes' => 'يعاني من حساسية خفيفة تجاه البنسلين.',
            ],
            [
                'first_name' => 'فاطمة',
                'father_name' => 'علي',
                'grandfather_name' => 'سالم',
                'family_name' => 'الشهري',
                'gender' => 'أنثى',
                'date_of_birth' => '1992-08-23',
                'id_type' => 'هوية وطنية',
                'id_number' => '1072938401',
                'mobile' => '0552345678',
                'nationality' => 'سعودية',
                'city' => 'الرياض',
                'district' => 'الياسمين',
                'blood_type' => 'A+',
                'emergency_contact' => '0554433221 (زوجها)',
                'notes' => 'متابعة حمل في الشهر السادس.',
            ],
            [
                'first_name' => 'إبراهيم',
                'father_name' => 'سعد',
                'grandfather_name' => 'فهد',
                'family_name' => 'الدوسري',
                'gender' => 'ذكر',
                'date_of_birth' => '1978-11-05',
                'id_type' => 'هوية وطنية',
                'id_number' => '1038294710',
                'mobile' => '0553456789',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'district' => 'النرجس',
                'blood_type' => 'B+',
                'emergency_contact' => '0501122334 (ابنه)',
                'notes' => 'يعاني من خشونة الركبة اليمنى.',
            ],
            [
                'first_name' => 'مريم',
                'father_name' => 'خالد',
                'grandfather_name' => 'صالح',
                'family_name' => 'الحربي',
                'gender' => 'أنثى',
                'date_of_birth' => '2001-02-14',
                'id_type' => 'هوية وطنية',
                'id_number' => '1120394857',
                'mobile' => '0554567890',
                'nationality' => 'سعودية',
                'city' => 'الرياض',
                'district' => 'العقيق',
                'blood_type' => 'O-',
                'emergency_contact' => '0556677889 (والدتها)',
                'notes' => 'ترغب في استشارة تبييض وتجميل الأسنان.',
            ],
            [
                'first_name' => 'عبد العزيز',
                'father_name' => 'سلطان',
                'grandfather_name' => 'حمد',
                'family_name' => 'العتيبي',
                'gender' => 'ذكر',
                'date_of_birth' => '1995-07-19',
                'id_type' => 'هوية وطنية',
                'id_number' => '1099283746',
                'mobile' => '0555678901',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'district' => 'حطين',
                'blood_type' => 'AB+',
                'emergency_contact' => '0502233445 (والده)',
                'notes' => 'فحص دوري لضغط الدم والقلب.',
            ],
            [
                'first_name' => 'لمى',
                'father_name' => 'عبد الرحمن',
                'grandfather_name' => 'ناصر',
                'family_name' => 'القحطاني',
                'gender' => 'أنثى',
                'date_of_birth' => '1989-10-30',
                'id_type' => 'هوية وطنية',
                'id_number' => '1066748392',
                'mobile' => '0556789012',
                'nationality' => 'سعودية',
                'city' => 'الرياض',
                'district' => 'الصحافة',
                'blood_type' => 'A-',
                'emergency_contact' => '0557788990 (زوجها)',
                'notes' => 'مراجعة روتينية لعيادة النساء.',
            ],
            [
                'first_name' => 'تركي',
                'father_name' => 'فيصل',
                'grandfather_name' => 'تركي',
                'family_name' => 'الشمري',
                'gender' => 'ذكر',
                'date_of_birth' => '2019-05-15',
                'id_type' => 'هوية وطنية',
                'id_number' => '1155443322',
                'mobile' => '0557890123',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'district' => 'قرطبة',
                'blood_type' => 'B-',
                'emergency_contact' => '0503344556 (والده)',
                'notes' => 'متابعة تطعيمات ووزن الطفل.',
            ],
            [
                'first_name' => 'سارة',
                'father_name' => 'منصور',
                'grandfather_name' => 'عبد الله',
                'family_name' => 'العنزي',
                'gender' => 'أنثى',
                'date_of_birth' => '2021-09-08',
                'id_type' => 'هوية وطنية',
                'id_number' => '1177889900',
                'mobile' => '0558901234',
                'nationality' => 'سعودية',
                'city' => 'الرياض',
                'district' => 'اليرموك',
                'blood_type' => 'O+',
                'emergency_contact' => '0558899001 (والدتها)',
                'notes' => 'فحص نزلات البرد الموسمية.',
            ],
            [
                'first_name' => 'فهد',
                'father_name' => 'سليمان',
                'grandfather_name' => 'عبد المحسن',
                'family_name' => 'الرشيد',
                'gender' => 'ذكر',
                'date_of_birth' => '1963-03-21',
                'id_type' => 'هوية وطنية',
                'id_number' => '1011223344',
                'mobile' => '0559012345',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'district' => 'الورود',
                'blood_type' => 'A+',
                'emergency_contact' => '0505566778 (ابنه)',
                'notes' => 'مريض سكري وضغط منتظم على العلاج.',
            ],
            [
                'first_name' => 'نورة',
                'father_name' => 'صالح',
                'grandfather_name' => 'عبد العزيز',
                'family_name' => 'الزهراني',
                'gender' => 'أنثى',
                'date_of_birth' => '1975-12-11',
                'id_type' => 'هوية وطنية',
                'id_number' => '1044556677',
                'mobile' => '0550123456',
                'nationality' => 'سعودية',
                'city' => 'الرياض',
                'district' => 'المروج',
                'blood_type' => 'AB-',
                'emergency_contact' => '0559900112 (زوجها)',
                'notes' => 'متابعة آلام أسفل الظهر.',
            ],
            [
                'first_name' => 'يوسف',
                'father_name' => 'حمد',
                'grandfather_name' => 'عبد الله',
                'family_name' => 'الهاجري',
                'gender' => 'ذكر',
                'date_of_birth' => '2003-06-25',
                'id_type' => 'هوية وطنية',
                'id_number' => '1133445566',
                'mobile' => '0551239876',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'district' => 'الغدير',
                'blood_type' => 'O+',
                'emergency_contact' => '0506677889 (شقيقه)',
                'notes' => 'فحص نظر لعمل نظارة طبية واستشارة ليزك.',
            ],
            [
                'first_name' => 'هدى',
                'father_name' => 'ناصر',
                'grandfather_name' => 'إبراهيم',
                'family_name' => 'المالكي',
                'gender' => 'أنثى',
                'date_of_birth' => '1998-01-17',
                'id_type' => 'هوية وطنية',
                'id_number' => '1100998877',
                'mobile' => '0552348765',
                'nationality' => 'سعودية',
                'city' => 'الرياض',
                'district' => 'النخيل',
                'blood_type' => 'B+',
                'emergency_contact' => '0551122998 (والدتها)',
                'notes' => 'جلسات نضارة وعلاج آثار حب الشباب.',
            ],
        ];

        $patients = [];
        foreach ($patientsData as $p) {
            $patient = Patient::firstOrCreate(
                ['id_number' => $p['id_number']],
                $p
            );
            $patients[] = $patient;
        }

        // 7. Appointments
        $today = Carbon::today()->format('Y-m-d');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $inTwoDays = Carbon::today()->addDays(2)->format('Y-m-d');
        $inThreeDays = Carbon::today()->addDays(3)->format('Y-m-d');

        $appointmentsData = [
            // Today appointments
            [
                'patient' => $patients[0], // عبد الله الغامدي
                'doctor' => $doctors['dr.ahmed'],
                'service' => 'كشف استشاري باطنة وجهاز هضمي',
                'date' => $today,
                'time' => '09:00',
                'status' => 'مؤكد',
                'notes' => 'متابعة نتائج التحاليل المخبرية للسكري.',
            ],
            [
                'patient' => $patients[8], // فهد الرشيد
                'doctor' => $doctors['dr.ahmed'],
                'service' => 'فحص موجات فوق صوتية للبطن والسونار',
                'date' => $today,
                'time' => '10:00',
                'status' => 'مكتمل',
                'notes' => 'تم عمل فحص السونار للبطن بنجاح.',
            ],
            [
                'patient' => $patients[1], // فاطمة الشهري
                'doctor' => $doctors['dr.sara'],
                'service' => 'فحص قاع العين وقياس ضغط العين',
                'date' => $today,
                'time' => '09:30',
                'status' => 'مؤكد',
                'notes' => 'فحص روتيني لضغط العين وقاع العين.',
            ],
            [
                'patient' => $patients[6], // تركي الشمري (طفل)
                'doctor' => $doctors['dr.reem'],
                'service' => 'تطعيمات ومتابعة نمو الطفل',
                'date' => $today,
                'time' => '11:00',
                'status' => 'مكتمل',
                'notes' => 'تم أخذ جرعة التطعيم الدورية بنجاح.',
            ],
            [
                'patient' => $patients[7], // سارة العنزي (طفلة)
                'doctor' => $doctors['dr.reem'],
                'service' => 'كشف واستشارة طب الأطفال',
                'date' => $today,
                'time' => '11:30',
                'status' => 'مجدول',
                'notes' => 'مراجعة نزلات البرد والسعال.',
            ],
            [
                'patient' => $patients[2], // إبراهيم الدوسري
                'doctor' => $doctors['dr.mohammed'],
                'service' => 'حقن مفاصل بالبلازما الغنية بالصفائح PRP',
                'date' => $today,
                'time' => '10:00',
                'status' => 'مؤكد',
                'notes' => 'الجلسة الثانية لحقن الركبة بالبلازما.',
            ],
            [
                'patient' => $patients[3], // مريم الحربي
                'doctor' => $doctors['dr.tariq'],
                'service' => 'تنظيف وتلميع الأسنان وإزالة الجير',
                'date' => $today,
                'time' => '16:00',
                'status' => 'مجدول',
                'notes' => 'جلسة إزالة الجير وتبييض الأسنان.',
            ],
            [
                'patient' => $patients[5], // لمى القحطاني
                'doctor' => $doctors['dr.noura'],
                'service' => 'سونار حمل ومتابعة الجنين رباعي الأبعاد 4D',
                'date' => $today,
                'time' => '17:00',
                'status' => 'مؤكد',
                'notes' => 'متابعة سلامة نمو الجنين.',
            ],
            [
                'patient' => $patients[11], // هدى المالكي
                'doctor' => $doctors['dr.hossam'],
                'service' => 'جلسة تقشير كربوني ونضارة للبشرة',
                'date' => $today,
                'time' => '16:30',
                'status' => 'مجدول',
                'notes' => 'جلسة الليزر الكربوني الثانية.',
            ],
            [
                'patient' => $patients[4], // عبد العزيز العتيبي
                'doctor' => $doctors['dr.omar'],
                'service' => 'تخطيط قلب كهربائي متقدم ECG',
                'date' => $today,
                'time' => '11:00',
                'status' => 'مؤكد',
                'notes' => 'فحص خفقان القلب وتسجيل التخطيط.',
            ],

            // Tomorrow appointments
            [
                'patient' => $patients[0], // عبد الله الغامدي
                'doctor' => $doctors['dr.tariq'],
                'service' => 'حشو عصب وعلاج جذور الأسنان',
                'date' => $tomorrow,
                'time' => '10:00',
                'status' => 'مجدول',
                'notes' => 'جلسة حشو الضرس السفلي الأيمن.',
            ],
            [
                'patient' => $patients[10], // يوسف الهاجري
                'doctor' => $doctors['dr.sara'],
                'service' => 'فحص وتقييم عمليات الليزك',
                'date' => $tomorrow,
                'time' => '11:30',
                'status' => 'مجدول',
                'notes' => 'تصوير القرنية وفحص ملائمة تصحيح النظر.',
            ],
            [
                'patient' => $patients[8], // فهد الرشيد
                'doctor' => $doctors['dr.omar'],
                'service' => 'فحص إيكو للقلب مع التقرير الشامل',
                'date' => $tomorrow,
                'time' => '12:00',
                'status' => 'مجدول',
                'notes' => 'فحص صمامات القلب بعد العملية.',
            ],

            // In two days
            [
                'patient' => $patients[9], // نورة الزهراني
                'doctor' => $doctors['dr.mohammed'],
                'service' => 'كشف استشاري عظام ومفاصل',
                'date' => $inTwoDays,
                'time' => '09:30',
                'status' => 'مجدول',
                'notes' => 'متابعة علاج آلام العمود الفقري.',
            ],
            [
                'patient' => $patients[1], // فاطمة الشهري
                'doctor' => $doctors['dr.hossam'],
                'service' => 'جلسة تقشير كربوني ونضارة للبشرة',
                'date' => $inTwoDays,
                'time' => '17:00',
                'status' => 'مجدول',
                'notes' => 'استشارة مشاكل تصبغات الجلد.',
            ],

            // In three days
            [
                'patient' => $patients[3], // مريم الحربي
                'doctor' => $doctors['dr.noura'],
                'service' => 'كشف نساء وتوليد شامل',
                'date' => $inThreeDays,
                'time' => '17:30',
                'status' => 'مجدول',
                'notes' => 'فحص سنوي روتيني.',
            ],
            [
                'patient' => $patients[4], // عبد العزيز العتيبي
                'doctor' => $doctors['dr.ahmed'],
                'service' => 'كشف استشاري باطنة وجهاز هضمي',
                'date' => $inThreeDays,
                'time' => '10:30',
                'status' => 'مجدول',
                'notes' => 'متابعة الحموضة والقولون العصبي.',
            ],
        ];

        foreach ($appointmentsData as $apt) {
            // Avoid duplicates
            $exists = Appointment::where('doctor_id', $apt['doctor']->id)
                ->where('patient_id', $apt['patient']->id)
                ->where('appointment_date', $apt['date'])
                ->where('appointment_time', $apt['time'])
                ->exists();

            if (!$exists) {
                Appointment::create([
                    'patient_id' => $apt['patient']->id,
                    'doctor_id' => $apt['doctor']->id,
                    'service' => $apt['service'],
                    'appointment_date' => $apt['date'],
                    'appointment_time' => $apt['time'],
                    'status' => $apt['status'],
                    'notes' => $apt['notes'],
                ]);
            }
        }
    }
}
