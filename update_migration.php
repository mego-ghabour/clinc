<?php
$files = glob("database/migrations/*_add_avatar_to_users_table.php");
$file = $files[0];
$content = file_get_contents($file);
$content = str_replace(
    "public function up(): void\n    {\n        Schema::table('users', function (Blueprint \$table) {\n            //\n        });",
    "public function up(): void\n    {\n        Schema::table('users', function (Blueprint \$table) {\n            \$table->string('avatar')->nullable();\n        });",
    $content
);
$content = str_replace(
    "public function down(): void\n    {\n        Schema::table('users', function (Blueprint \$table) {\n            //\n        });",
    "public function down(): void\n    {\n        Schema::table('users', function (Blueprint \$table) {\n            \$table->dropColumn('avatar');\n        });",
    $content
);
file_put_contents($file, $content);

