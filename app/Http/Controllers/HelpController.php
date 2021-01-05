<?php

namespace App\Http\Controllers;

use App\HelpCategory;
use App\HelpQuestion;
use App\HelpSubcategory;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HelpController extends Controller
{
    use FileUpload;
    use Slug;

    public function helpCategoryIndex()
    {
        return DB::table('help_categories')->get();
    }

    public function helpCategoryStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:help_categories',
        ]);

        $data = $request->all();
        if ($request->icon != '') {
            $data['icon'] = $this->saveImages($request, 'icon', 'upload/help/icon/', 100, 100);
        }
        $data['slug'] = $this->slugText($request, 'name');
        return HelpCategory::create($data);
    }

    public function helpCategoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:help_categories,name,' . $id,
        ]);
        $category = HelpCategory::findOrFail($id);
        $data = $request->all();
        if ($request->icon != '' && strlen($request->icon) > 200) {
            File::delete(public_path($category->icon));
            $data['icon'] = $this->saveImages($request, 'icon', 'upload/help/icon/', 100, 100);
        }
        $data['slug'] = $this->slugText($request, 'name');
        $category->update($data);
        return HelpCategory::findOrFail($id);
    }

    public function helpCategoryDestroy($id)
    {
        $subcategory = HelpSubcategory::where('category_id', $id)->first();
        if ($subcategory) return response()->json(['result' => 'Error', 'message' => 'Category already used create a subcategory'], 200);
        $brand = HelpCategory::findOrFail($id);
        File::delete(public_path($brand->icon));
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Category has been deleted'], 200);
    }

    public function helpSubcategoryIndex()
    {
        return DB::table('help_subcategories')->join('help_categories', 'help_categories.id', '=', 'help_subcategories.category_id')
            ->select('help_categories.name as categoryName', 'help_subcategories.*')->get();
    }

    public function helpSubcategoryStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:help_subcategories',
        ]);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        $subcategory = HelpSubcategory::create($data);
        return (array)DB::table('help_subcategories')->join('help_categories', 'help_categories.id', '=', 'help_subcategories.category_id')
            ->select('help_categories.name as categoryName', 'help_subcategories.*')->where('help_subcategories.id', $subcategory->id)->first();
    }

    public function helpSubcategoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:help_subcategories,name,' . $id,
        ]);
        $category = HelpSubcategory::findOrFail($id);
        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        $category->update($data);
        return HelpSubcategory::join('help_categories', 'help_categories.id', '=', 'help_subcategories.category_id')
            ->select('help_categories.name as categoryName', 'help_subcategories.*')->findOrFail($id);
    }

    public function helpSubcategoryDestroy($id)
    {
        $brand = HelpSubcategory::findOrFail($id);
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Subcategory has been deleted'], 200);
    }

    public function helpQuestionIndex(Request $request)
    {
        return DB::table('help_questions')->get();
    }

    public function helpQuestionStore(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|max:255|unique:help_questions',
            'answer' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
        ]);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'question');
        return HelpQuestion::create($data);
    }

    public function helpQuestionUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'question' => 'required|max:255|unique:help_questions,question,' . $id,
            'answer' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
        ]);
        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'question');
        HelpQuestion::findOrFail($id)->update($data);
        return (array)DB::table('help_questions')->where('id', $id)->first();
    }

    public function helpQuestionDestroy($id)
    {
        HelpQuestion::findOrFail($id)->delete();
        return response()->json(['result' => 'Success', 'message' => 'Question has been deleted'], 200);
    }

    public function helpQuestionStatus(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required',
            'id' => 'required',
        ]);
        $status = HelpQuestion::findOrFail($id);
        $status->status = $request->status;
        $status->save();
    }

}
