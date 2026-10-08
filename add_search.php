<?php
$content = file_get_contents("app/Http/Controllers/PatientController.php");
$searchMethod = <<<PHP
    public function search(Request \$request)
    {
        \$query = \$request->get("q");
        if (!\$query) return response()->json([]);

        \$patients = Patient::where("first_name", "like", "%{\$query}%")
            ->orWhere("family_name", "like", "%{\$query}%")
            ->orWhere("id_number", "like", "%{\$query}%")
            ->orWhere("mobile", "like", "%{\$query}%")
            ->orWhere("mrn", "like", "%{\$query}%")
            ->take(5)
            ->get(["id", "first_name", "family_name", "mrn", "mobile", "avatar"]);
        
        return response()->json(\$patients);
    }
PHP;
$content = str_replace("public function index()", $searchMethod . "\n\n    public function index()", $content);
file_put_contents("app/Http/Controllers/PatientController.php", $content);

