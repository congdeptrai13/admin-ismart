<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            session(["module_active" => "user"]);
            return $next($request);
        });
    }
    //
    function list(Request $request)
    {
        $list_act = [
            "delete" => "Xóa tạm thời"
        ];

        if ($request->input("status") == "trash") {
            $list_act = [
                "restore" => "Khôi phục",
                "forceDelete" => "Xóa vĩnh viễn"
            ];
        }
        $keyword = "";
        if ($request->input("keyword")) {
            $keyword = $request->input("keyword");
        }
        $users = User::where("name", "LIKE", "%{$keyword}%")->simplePaginate(10);
        if ($request->input("status") == "trash") {
            $users = User::onlyTrashed()->simplePaginate(10);
        } else {
            $users = User::where("name", "LIKE", "%{$keyword}%")->simplePaginate(10);
        }
        $count_user_active = User::count();
        $count_user_trash = User::onlyTrashed()->count();
        $count = [$count_user_active, $count_user_trash];
        // dd($users->items());
        return view("admin.user.list", compact(["users", "count", "list_act"]));
    }

    function add()
    {
        return view("admin.user.add");
    }

    function store(Request $request)
    {
        $request->validate(
            [
                "name" => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ],
            [
                "required" => "không được để trống :attribute",
                "email" => "không đúng định dạng :email"
            ]
        );
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return redirect("admin/user/list");
    }

    function delete($id)
    {
        if (Auth::id() != $id) {
            $user = User::find($id);
            $user->delete();
            return redirect("admin/user/list")->with("status", "xóa người dùng thành công");
        } else {
            return redirect("admin/user/list")->with("status", "không thể tự xóa chính mình");
        }
    }

    function action(Request $request)
    {
        $lists_check = $request->input("check_lists");
        if ($lists_check) {
            foreach ($lists_check as $k => $id) {
                if (Auth::id() == $id) {
                    unset($lists_check[$k]);
                }
            }
            if (!empty($lists_check)) {
                $act = $request->input("act");
                if ($act == "delete") {
                    User::destroy($lists_check);
                    return redirect("admin/user/list")->with("status", "xóa người dùng thành công");
                }
                if ($act == "restore") {
                    User::withTrashed()->whereIn("id", $lists_check)->restore();
                    return redirect("admin/user/list")->with("status", "khôi phục người dùng thành công");
                }
                if ($act == "forceDelete") {
                    User::withTrashed()->whereIn("id", $lists_check)->forceDelete();
                    return redirect("admin/user/list")->with("status", "xóa vĩnh viễn người dùng thành công");
                }
                return redirect("admin/user/list")->with("status", "vui lòng chọn tác vụ");
            } else {
                return redirect("admin/user/list")->with("status", "không thể tự xóa chính mình ra khỏi hệ thống");
            }
        } else {
            return redirect("admin/user/list")->with("status", "vui lòng chọn người dùng cần thao tác");
        }
    }

    function edit($id)
    {
        $user = User::find($id);
        return view("admin.user.edit", compact("user"));
    }

    function update(Request $request, $id)
    {
        $request->validate(
            [
                "name" => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ],
            [
                "required" => "không được để trống :attribute",
                "confirmed" => ":attribute không đúng"
            ]
        );
        User::where("id", $id)->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);
        return redirect("admin/user/list")->with("status", "cập nhật người dùng thành công");
    }
}
