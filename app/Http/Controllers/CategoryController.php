<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Daftar kategori
     */
    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'has_items' => $request->input('has_items'),
        ];

        $categories = $this->categoryService->getCategories($filters, 15);
        $stats = $this->categoryService->getStats();

        return view('categories.index', compact('categories', 'stats', 'filters'));
    }

    /**
     * Form tambah
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Simpan
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());

            return redirect()
                ->route('categories.index')
                ->with('success', "Kategori \"{$category->name}\" berhasil ditambahkan.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Detail
     */
    public function show(Category $category)
    {
        $category->loadCount('items');
        $items = $category->items()
            ->with('location')
            ->latest()
            ->take(10)
            ->get();

        return view('categories.show', compact('category', 'items'));
    }

    /**
     * Form edit
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->updateCategory($category, $request->validated());

            return redirect()
                ->route('categories.index')
                ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Hapus
     */
    public function destroy(Category $category)
    {
        try {
            $name = $category->name;
            $this->categoryService->deleteCategory($category);

            return redirect()
                ->route('categories.index')
                ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}