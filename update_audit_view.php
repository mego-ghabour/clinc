<?php
$content = file_get_contents("resources/views/audit_logs/index.blade.php");

// Add select options for login/logout
$content = preg_replace(
    "/<option value=\"rollback\".*?<\/option>/",
    "<option value=\"rollback\" {{ request('event_type') == 'rollback' ? 'selected' : '' }}>????? (???????)</option>\n                        <option value=\"login\" {{ request('event_type') == 'login' ? 'selected' : '' }}>????? ????</option>\n                        <option value=\"logout\" {{ request('event_type') == 'logout' ? 'selected' : '' }}>????? ????</option>",
    $content
);

// Event Logic Colors
$content = preg_replace(
    "/'rollback' => 'text-purple-600 bg-purple-50 border-purple-100',/",
    "'rollback' => 'text-purple-600 bg-purple-50 border-purple-100',\n                            'login' => 'text-emerald-600 bg-emerald-50 border-emerald-100',\n                            'logout' => 'text-amber-600 bg-amber-50 border-amber-100',",
    $content
);

// Event Logic Text
$content = preg_replace(
    "/'rollback' => '???????? ?? ????? ??',/",
    "'rollback' => '???????? ?? ????? ??',\n                            'login' => '?????? ?????? ??????',\n                            'logout' => '?????? ?????? ?? ??????',",
    $content
);

// Human Readable text handling if there is no subject
$content = str_replace(
    "if (\$log->subject) {",
    "if (\$log->event === 'login' || \$log->event === 'logout') {\n                            \$humanText = \"??? \" . (\$log->causer->name ?? '??????') . \" <strong class=\\\"text-teal-700\\\">\" . \$arEvent . \"</strong>\";\n                        } else if (\$log->subject) {",
    $content
);

// Rollback condition
$content = str_replace(
    "\$canRollback = in_array(\$log->event, ['updated', 'deleted']);",
    "\$canRollback = in_array(\$log->event, ['updated', 'deleted']);\n                        \$isAuthEvent = in_array(\$log->event, ['login', 'logout']);",
    $content
);

// Hide action buttons for login/logout (they have no JSON data)
$content = preg_replace(
    "/<td class=\"px-6 py-4 text-center\">\n.*?<div class=\"flex items-center justify-center gap-2 print:hidden\" x-data=\"\{ openJSON: false \}\">/",
    "<td class=\"px-6 py-4 text-center\">\n                            <div class=\"flex items-center justify-center gap-2 print:hidden\" x-data=\"{ openJSON: false }\">\n                                @if(!\$isAuthEvent)",
    $content
);

$content = str_replace(
    "</div>\n                        </td>",
    "</div>\n                                @else\n                                <div class=\"w-8 h-8 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center cursor-not-allowed tooltip\" title=\"???? ???? ?????\">\n                                    <i class=\"fa-solid fa-fingerprint text-[10px]\"></i>\n                                </div>\n                                @endif\n                            </div>\n                        </td>",
    $content
);


file_put_contents("resources/views/audit_logs/index.blade.php", $content);

