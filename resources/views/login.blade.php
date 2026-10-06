<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - فكرتك الرقمية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
        }
        .bg-teal-custom {
            background-color: #0b4a60;
        }
        .text-teal-custom {
            color: #0b4a60;
        }
        .bg-light-blue-custom {
            background-color: #e5f0f4;
        }
    </style>
</head>
<body class="h-screen flex overflow-hidden">
    
    <!-- Right Panel (Login Form) - Since RTL, right panel is the first in DOM visually if flex-row, wait, RTL reverses flex direction. 
         flex-row in RTL means the first element is on the right. 
         Wait, in the image, the white login form is on the LEFT side, and the teal panel is on the RIGHT side.
         In RTL, flex-row means first child is on the RIGHT. 
         So the teal panel should be the first child in HTML to be on the right, and the login form should be the second child to be on the left. -->
    
    <!-- Dark Teal Panel (Right visually) -->
    <div class="hidden md:flex md:w-1/2 bg-teal-custom text-white flex-col justify-between p-12 relative">
        <div class="flex justify-end items-center space-x-3 space-x-reverse">
            <span class="text-xl font-bold">فكرتك الرقمية</span>
            <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center font-bold text-xl">
                ن
            </div>
        </div>
        
        <div class="absolute bottom-16 right-12 text-right">
            <h1 class="text-3xl font-bold mb-2">منصة تشغيل العيادات</h1>
            <p class="text-gray-300 text-sm">نظرة واحدة على العيادات، المواعيد، الملف الطبي، والمالية.</p>
        </div>
    </div>

    <!-- White Panel (Left visually) -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-white p-8">
        <div class="w-full max-w-md bg-white border border-gray-100 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">تسجيل الدخول</h2>
                <p class="text-gray-400 text-sm">كلمة المرور التجريبية: Demo@123</p>
            </div>

            <form action="/" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1 text-right">اسم المستخدم</label>
                    <input type="text" name="username" id="username_input" value="admin" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-500 text-right text-gray-700 bg-white" placeholder="admin">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1 text-right">كلمة المرور</label>
                    <input type="password" name="password" id="password_input" value="........" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-500 text-right text-gray-700 bg-white" placeholder="........">
                </div>

                <button type="submit" class="w-full bg-teal-custom hover:bg-teal-700 text-white font-medium py-3 rounded-lg transition duration-200 mt-2">
                    دخول
                </button>
            </form>

            <a href="/booking" class="block text-center w-full bg-light-blue-custom text-teal-custom font-medium py-3 rounded-lg transition duration-200 mt-4 mb-6">
                حجز موعد أونلاين للمرضى
            </a>

            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="setRole('admin', 'admin@123', this)" class="role-btn py-2 px-1 text-xs border border-teal-custom bg-light-blue-custom text-teal-custom rounded-full transition duration-200">مدير النظام</button>
                <button type="button" onclick="setRole('manager', 'manager@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">مدير المركز</button>
                <button type="button" onclick="setRole('reception', 'reception@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">الاستقبال</button>
                
                <button type="button" onclick="setRole('doctor', 'doctor@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">طبيب</button>
                <button type="button" onclick="setRole('nurse', 'nurse@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">تمريض</button>
                <button type="button" onclick="setRole('lab', 'lab@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">مخبر</button>
                
                <button type="button" onclick="setRole('radiology', 'radiology@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">أشعة</button>
                <button type="button" onclick="setRole('pharmacy', 'pharmacy@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">صيدلية</button>
                <button type="button" onclick="setRole('cashier', 'cashier@123', this)" class="role-btn py-2 px-1 text-xs border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 transition duration-200">صندوق</button>
            </div>
        </div>
    </div>

    <script>
        function setRole(username, password, btnElement) {
            // Update inputs
            document.getElementById('username_input').value = username;
            document.getElementById('password_input').value = password;

            // Reset all buttons styling
            const buttons = document.querySelectorAll('.role-btn');
            buttons.forEach(btn => {
                btn.classList.remove('border-teal-custom', 'bg-light-blue-custom', 'text-teal-custom');
                btn.classList.add('border-gray-200', 'text-gray-500', 'hover:bg-gray-50');
            });

            // Set active styling to clicked button
            btnElement.classList.remove('border-gray-200', 'text-gray-500', 'hover:bg-gray-50');
            btnElement.classList.add('border-teal-custom', 'bg-light-blue-custom', 'text-teal-custom');
        }
    </script>
</body>
</html>
