@forelse($recipes as $recipe)
    <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="glass-card h-100 overflow-hidden d-flex flex-column"
             style="background: var(--bg-card); box-shadow: var(--shadow);">

            {{-- Recipe Image --}}
            <div class="recipe-img-container"
                 style="position: relative; height: 180px; overflow: hidden;">

                <img src="{{ $recipe->image }}"
                     class="recipe-img"
                     style="width: 100%; height: 100%; object-fit: cover;"
                     alt="{{ $recipe->title }}">

                {{-- Cuisine --}}
                <span class="recipe-cuisine-badge"
                      style="position: absolute; top: 12px; left: 12px; background: rgba(0, 0, 0, 0.6); color: white; padding: 4px 10px; border-radius: 30px; font-size: 0.75rem;">
                    {{ $recipe->cuisine_type }}
                </span>

                {{-- Rating --}}
                <span class="recipe-rating"
                      style="position: absolute; top: 12px; right: 12px; background: white; color: var(--primary); padding: 4px 8px; border-radius: 30px; font-size: 0.75rem; font-weight: 700;">
                    <i class="fa-solid fa-star me-1"></i>
                    {{ $recipe->avg_rating }}
                </span>
            </div>

            {{-- Recipe Information --}}
            <div class="p-4 d-flex flex-column flex-grow-1">

                <h5 class="fw-bold mb-2">
                    <a href="{{ route('recipes.show', $recipe->slug) }}"
                       class="text-decoration-none"
                       style="color: var(--text);">
                        {{ $recipe->title }}
                    </a>
                </h5>

                <p class="text-muted small mb-3 flex-grow-1"
                   style="font-size: 0.85rem;">
                    {{ Str::limit($recipe->description, 80) }}
                </p>

                <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3 border-light">

                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                        <i class="fa-regular fa-clock me-1"></i>
                        {{ $recipe->cooking_time }} mins
                    </span>

                    <span>
                        {!! $recipe->difficulty_badge !!}
                    </span>

                </div>
            </div>
        </div>
    </div>

@empty

    {{-- No Recipes Found --}}
    <div class="col-12 text-center py-5">

        <i class="fa-solid fa-cookie-bite text-primary mb-3"
           style="font-size: 60px;"></i>

        <h4 class="fw-bold">
            No recipes found matching current filters
        </h4>

        <p class="text-muted">
            Try clearing some filtering options to expand your results.
        </p>

    </div>

@endforelse


{{-- Pagination --}}
@if($recipes->hasPages())

    <div class="col-12 mt-5">

        <nav aria-label="Recipe pagination">

            <ul class="pagination justify-content-center mb-0">

                {{-- Previous Page --}}
                @if($recipes->onFirstPage())

                    <li class="page-item disabled">
                        <span class="page-link">
                            Previous
                        </span>
                    </li>

                @else

                    <li class="page-item">
                        <a class="page-link"
                           href="{{ $recipes->previousPageUrl() }}">
                            Previous
                        </a>
                    </li>

                @endif


                {{-- Page Numbers --}}
                @foreach($recipes->getUrlRange(1, $recipes->lastPage()) as $page => $url)

                    @if($page == $recipes->currentPage())

                        <li class="page-item active">
                            <span class="page-link">
                                {{ $page }}
                            </span>
                        </li>

                    @else

                        <li class="page-item">
                            <a class="page-link"
                               href="{{ $url }}">
                                {{ $page }}
                            </a>
                        </li>

                    @endif

                @endforeach


                {{-- Next Page --}}
                @if($recipes->hasMorePages())

                    <li class="page-item">
                        <a class="page-link"
                           href="{{ $recipes->nextPageUrl() }}">
                            Next
                        </a>
                    </li>

                @else

                    <li class="page-item disabled">
                        <span class="page-link">
                            Next
                        </span>
                    </li>

                @endif

            </ul>

        </nav>

    </div>

@endif