@props(['category'])
<a {{ $attributes->merge(['class' => 'category-card category-'.$category->slug]) }} href="{{ route('explore.index',['category'=>$category->slug]) }}" style="--category-accent: {{ $category->accent ?: '#EFEAFE' }}">
    <div class="category-icon"><x-icon :name="$category->slug" /></div>
    <div class="category-copy">
        <b>{{ $category->name }}</b>
        <small>{{ $category->description }}</small>
    </div>
    <span class="category-link">Jelajahi <x-icon name="arrow-right" /></span>
</a>
