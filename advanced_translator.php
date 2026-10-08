<?php
$content = file_get_contents("resources/views/audit_logs/index.blade.php");

$advancedLogic = <<<PHP
                        // Human Readable Generator
                        if (\$isAuthEvent) {
                            \$humanText = "??? " . (\$log->causer->name ?? '??????') . " <strong class='text-teal-700'>" . \$arEvent . "</strong>";
                        } else {
                            // Smart Update Description
                            \$changedFields = "";
                            if (\$log->event === 'updated' && isset(\$log->properties['attributes'])) {
                                \$keys = array_keys(\$log->properties['attributes']);
                                \$keys = array_filter(\$keys, fn(\$k) => !in_array(\$k, ['updated_at', 'created_at', 'last_login_at']));
                                if (count(\$keys) > 0) {
                                    \$translatedKeys = array_map(fn(\$k) => __('attributes.' . \$k) !== 'attributes.' . \$k ? __('attributes.' . \$k) : \$k, \$keys);
                                    \$changedFields = " (<span class='text-blue-600 text-xs'>" . implode('? ', \$translatedKeys) . "</span>)";
                                }
                            }

                            if (\$log->event === 'created') {
                                \$arEvent = '?????? ' . \$arSubject . ' ????';
                                \$arSubject = '';
                            }
                            
                            \$humanText = "??? " . (\$log->causer->name ?? '??????') . " <strong>" . \$arEvent . "</strong> " . \$arSubject . \$changedFields;
                            
                            if (\$log->subject) {
                                \$recordName = \$log->subject->name ?? \$log->subject->title ?? \$log->subject->full_name ?? "#" . \$log->subject_id;
                                \$humanText .= " <span class='text-teal-700 font-bold'>(\$recordName)</span>";
                            } else {
                                \$humanText .= " (??? #\$log->subject_id)";
                            }
                        }
PHP;

$content = preg_replace("/\/\/ Human Readable Generator.*?} else \{.*?\}/s", $advancedLogic, $content);
// This regex will fail because of the complex matching, let's just write the view file completely instead of regex to avoid errors.

