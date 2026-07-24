@extends('layouts.app')

@section('title', 'My Grocery Shopping Lists — MealIt')

@section('styles')
<style>
    .shopping-item-card {
        padding: 12px 18px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        transition: var(--transition-fast);
    }
    
    .shopping-item-card:hover {
        border-color: var(--primary);
    }
    
    .shopping-item-card.checked {
        opacity: 0.55;
        background-color: var(--bg-page);
    }
    
    .shopping-item-card.checked span {
        text-decoration: line-through;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h1 class="playfair fw-bold mb-1"><i class="fa-solid fa-list-check text-primary me-2"></i>My Shopping Lists</h1>
            <p class="text-muted">Review, cross off, and categorize your weekly grocery checklist.</p>
        </div>
    </div>

    @if(count($lists) > 0)
        @foreach($lists as $list)
            <div class="glass-card p-5 mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 border-bottom pb-3 border-light">
                    <div>
                        <h3 class="playfair fw-bold mb-1 text-primary">{{ $list->name }}</h3>
                        <small class="text-muted">Created: {{ $list->created_at->format('M j, Y') }}</small>
                    </div>
                    
                    <!-- Quick custom item entry form -->
                    <form onsubmit="addNewShoppingItem(event, {{ $list->id }}, this)" class="d-flex gap-2">
                        <input type="text" name="name" class="form-control" placeholder="Add custom item..." required style="border-radius: 30px;">
                        <input type="text" name="quantity" class="form-control" placeholder="Qty..." style="width: 80px; border-radius: 30px;">
                        <button type="submit" class="btn btn-custom btn-sm px-3"><i class="fa-solid fa-plus"></i></button>
                    </form>
                </div>

                @php 
                    $categorized = $list->items->groupBy('category');
                @endphp

                <div class="row g-4">
                    @forelse($categorized as $category => $items)
                        <div class="col-md-6 col-lg-4">
                            <div class="card bg-light border-0 p-4 h-100" style="border-radius: 16px;">
                                <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-box-open me-2 text-primary"></i>{{ $category }}</h5>
                                
                                <div class="d-flex flex-column">
                                    @foreach($items as $item)
                                        <div class="shopping-item-card {{ $item->is_checked ? 'checked' : '' }}" id="item-row-{{ $item->id }}">
                                            <div class="d-flex align-items-center gap-3" onclick="toggleShoppingItem({{ $item->id }}, this)" style="cursor: pointer;">
                                                <input class="form-check-input" type="checkbox" {{ $item->is_checked ? 'checked' : '' }} aria-label="Toggle item">
                                                <span class="fw-semibold text-dark">{{ $item->name }}</span>
                                                @if($item->quantity)
                                                    <span class="badge bg-light text-primary border">{{ $item->quantity }}</span>
                                                @endif
                                            </div>
                                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteShoppingItem({{ $item->id }})"><i class="fa-solid fa-trash-can"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <h6 class="text-muted">No items in this checklist yet.</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-5">
            <i class="fa-solid fa-clipboard-list text-primary mb-3" style="font-size: 65px;"></i>
            <h4 class="fw-bold">No shopping lists discovered</h4>
            <p class="text-muted">Go to the weekly meal planner to auto-build lists, or create a custom list below.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function toggleShoppingItem(itemId, element) {
        const row = document.getElementById(`item-row-${itemId}`);
        const check = row.querySelector('input');
        
        fetch(`{{ url("/shopping-list/item") }}/${itemId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                row.classList.toggle('checked');
                check.checked = data.checked;
            }
        })
        .catch(() => {});
    }

    function deleteShoppingItem(itemId) {
        if (!confirm('Remove this item?')) return;
        
        fetch(`{{ url("/shopping-list/item") }}/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`item-row-${itemId}`).remove();
            }
        })
        .catch(() => {});
    }

    function addNewShoppingItem(e, listId, form) {
        e.preventDefault();
        const fd = new FormData(form);
        
        fetch(`{{ url("/shopping-list") }}/${listId}/item`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: fd.get('name'),
                quantity: fd.get('quantity'),
                category: 'Other'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(() => {});
    }
</script>
@endsection
