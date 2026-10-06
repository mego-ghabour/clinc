<?php
$content = file_get_contents("resources/views/clinics/index.blade.php");

// 1. Remove the inline modal
$inlineModalPattern = "/\s*<!-- Custom Delete Confirmation Modal -->\s*<div x-show=\"showDeleteConfirm\".*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s";
$content = preg_replace($inlineModalPattern, "", $content);

// 2. Change the button to open global modal
$content = str_replace(
    '<button @click="open = false; showDeleteConfirm = true" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 border-t border-gray-50">',
    '<button @click="open = false; openDeleteModal({{ $clinic }})" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 border-t border-gray-50">',
    $content
);

// 3. Add Global Modal HTML before closing div of x-data
$globalModal = <<<HTML
    <!-- Global Delete Confirmation Modal -->
    <div x-show="isDeleteModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isDeleteModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="isDeleteModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                <div class="bg-white px-6 py-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-50 rounded-full mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-3xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">هل أنت متأكد؟</h3>
                    <p class="text-sm text-gray-500 text-center">
                        <span x-show="deleteClinic && deleteClinic.deleted_at">
                            هل أنت متأكد من حذف عيادة (<span x-text="deleteClinic?.name"></span>) بشكل نهائي؟ لا يمكن التراجع عن هذه الخطوة.
                        </span>
                        <span x-show="deleteClinic && !deleteClinic.deleted_at">
                            هل أنت متأكد من أرشفة عيادة (<span x-text="deleteClinic?.name"></span>)؟ سيتم إخفاؤها من القوائم العامة.
                        </span>
                    </p>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-center gap-3">
                    <form :action="deleteClinic ? `/clinics/\${deleteClinic.id}` : ''" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                            <span x-show="deleteClinic && deleteClinic.deleted_at">حذف نهائي</span>
                            <span x-show="deleteClinic && !deleteClinic.deleted_at">نعم، قم بالأرشفة</span>
                        </button>
                    </form>
                    <button type="button" @click="closeDeleteModal()" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
HTML;

$content = str_replace("</div>\n@endsection", $globalModal, $content);

// 4. Update Alpine JS
$alpineJS = <<<JS
            isModalOpen: false,
            isDeleteModalOpen: false,
            deleteClinic: null,

            openDeleteModal(clinic) {
                this.deleteClinic = clinic;
                this.isDeleteModalOpen = true;
            },
            closeDeleteModal() {
                this.isDeleteModalOpen = false;
                setTimeout(() => { this.deleteClinic = null; }, 300);
            },
            searchQuery: '',
JS;
$content = str_replace("isModalOpen: false,\n            searchQuery: '',", $alpineJS, $content);

file_put_contents("resources/views/clinics/index.blade.php", $content);
echo "Done";
