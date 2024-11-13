<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class TestController extends Controller
{
    public function vulnerable(Request $request)
    {
        // Nhận serialized data từ request
        $serialized = $request->input('data');

        if ($serialized) {
            // Không an toàn: Unserialize trực tiếp input
            $obj = unserialize($serialized);
            return "Data đã được unserialize";
        }

        return view('serialize.form');
    }

    // Route đã được fix
    public function safe(Request $request)
    {
        $serialized = $request->input('data');

        if ($serialized) {
            try {
                // An toàn: Chỉ cho phép unserialize các class được whitelist
                $allowed = ['App\\SafeClass'];
                $obj = unserialize($serialized, ['allowed_classes' => $allowed]);
                return "Data đã được unserialize an toàn";
            } catch (\Exception $e) {
                return "Lỗi: Class không được phép unserialize";
            }
        }

        return view('serialize.form');
    }

    // Helper để tạo payload
    public function createPayload()
    {
        $obj = new VulnerableObject();
        $obj->command = 'whoami'; // Command muốn thực thi
        return serialize($obj);
    }

    public function unsafeSearch(Request $request)
    {
        $keyword = $request->input('keyword');

        if ($keyword) {
            try {
                // Tạo connection với PDO cho phép multiple queries
                $pdo = DB::connection()->getPdo();
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

                // Query dễ bị tấn công
                $query = "SELECT * FROM users WHERE name = '$keyword'";

                // Thực thi và lấy kết quả
                $statement = $pdo->query($query);
                $users = $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : [];

                // Log query để debug
                Log::info('Executed SQL Query: ' . $query);

                return response()->json([
                    'success' => true,
                    'query' => $query,
                    'users' => $users
                ]);
            } catch (\Exception $e) {
                Log::error('SQL Error: ' . $e->getMessage());
                Log::error('Query attempted: ' . $query);

                return response()->json([
                    'success' => false,
                    'query' => $query,
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        return response()->json(['success' => false, 'message' => 'No keyword provided']);
    }
}

class VulnerableObject
{
    public $command;

    public function __wakeup()
    {
        if (isset($this->command)) {
            // Nguy hiểm: Thực thi command từ dữ liệu được unserialize
            system($this->command);
        }
    }
}
