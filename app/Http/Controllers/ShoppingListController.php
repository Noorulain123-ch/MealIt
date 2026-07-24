<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $lists = auth()->user()->shoppingLists()->with('items')->latest()->get();
        return view('dashboard.shopping-list', compact('lists'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $list = ShoppingList::create(['user_id' => auth()->id(), 'name' => $request->name]);
        return response()->json(['success' => true, 'list' => $list]);
    }

    public function toggle(Request $request, $itemId)
    {
        $item = ShoppingListItem::whereHas('shoppingList', fn($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($itemId);
        $item->update(['is_checked' => !$item->is_checked]);
        return response()->json(['success' => true, 'checked' => $item->is_checked]);
    }

    public function addItem(Request $request, ShoppingList $list)
    {
        $request->validate(['name' => 'required|string', 'quantity' => 'nullable|string']);
        $item = $list->items()->create(['name' => $request->name, 'quantity' => $request->quantity, 'category' => $request->category ?? 'Other']);
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroyItem($itemId)
    {
        ShoppingListItem::whereHas('shoppingList', fn($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($itemId)->delete();
        return response()->json(['success' => true]);
    }
}
