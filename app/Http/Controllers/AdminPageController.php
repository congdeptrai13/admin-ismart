<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class AdminPageController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            session(["module_active" => "page"]);
            return $next($request);
        });
    }
    function list(Request $request)
    {
        $act_list = [
            "delete" => "xóa",
            "published" => "công khai"
        ];
        if ($request->input("status") == "published") {
            $act_list = [
                "delete" => "xóa",
                "pending" => "chờ duyệt"
            ];
        }
        $keyword = "";
        if ($request->input("btn-search")) {
            $keyword = $request->input("keyword");
        }
        $count_page_pending = Page::where("status", "=", "pending")->count();
        $count_page_public = Page::where("status", "=", "published")->count();
        $count = [$count_page_pending, $count_page_public];
        $pages = Page::where("title", "like", "%{$keyword}%")->simplePaginate(10);
        if ($request->input("status") == "pending") {
            $pages = Page::where("status", "=", "pending")->where("title", "like", "%{$keyword}%")->simplePaginate(10);
        }
        if ($request->input("status") == "published") {
            $pages = Page::where("status", "=", "published")->where("title", "like", "%{$keyword}%")->simplePaginate(10);
        }
        return view("admin.page.list", compact("pages", "count", "act_list"));
    }

    function add()
    {
        return view("admin.page.add");
    }

    function store(Request $request)
    {

        $request->validate(
            [
                "title" => "required",
                "content" => "required"
            ],
            [
                "required" => ":attribute không được để trống",

            ],
            [
                "title" => "Tiêu đề",
                "content" => "Nội dung"
            ]
        );
        $input = $request->all();
        $title = $input["title"];
        $input["slug"] = Str::slug($title);
        Page::create([
            "title" => $request->input("title"),
            "slug" => $input["slug"],
            "content" => $request->input("content")
        ]);
        return redirect("admin/page/list")->with("status", "thêm trang mới thành công!");
    }

    function delete($id)
    {
        $page = Page::find($id);
        $page->delete();
        return redirect("admin/page/list")->with("status", "xóa trang thành công");
    }

    function edit($id)
    {


        $page = Page::find($id);
        return view("admin.page.edit", compact("page"));
    }

    function update(Request $request, $id)
    {
        $request->validate(
            [
                "title" => "required",
                "content" => "required"
            ],
            [
                "required" => ":attribute không được để trống",

            ],
            [
                "title" => "Tiêu đề",
                "content" => "Nội dung"
            ]
        );
        $slug = Str::slug($request->input("title"));
        Page::where("id", $id)->update([
            "title" => $request->input("title"),
            "slug" => $slug,
            "content" => $request->input("content")
        ]);
        return redirect("admin/page/list")->with("status", "cập nhật trang thành công");
    }

    function action(Request $request)
    {
        $list_check = $request->input("check_list");
        if (!empty($list_check)) {
            $act = $request->input("act");
            if (!empty($act)) {
                if ($act == "delete") {
                    Page::destroy($list_check);
                    return redirect("admin/page/list")->with("status", "Xóa trang thành công");
                }
                if ($act == "published") {
                    Page::whereIn("id", $list_check)->update([
                        "status" => "published"
                    ]);
                    return redirect("admin/page/list")->with("status", "publish trang thành công");
                }
                if ($act == "pending") {
                    Page::whereIn("id", $list_check)->update([
                        "status" => "pending"
                    ]);
                    return redirect("admin/page/list")->with("status", "pending trang thành công");
                }
            } else {
                return redirect("admin/page/list")->with("status", "vui lòng chọn tác vụ");
            }
        }
        return redirect("admin/page/list")->with("status", "Vui lòng chọn người dùng");
    }
}
