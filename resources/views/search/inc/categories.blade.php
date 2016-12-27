<?php
$cats = $cats->groupBy('parent_id');
$sub_cats = $cats;
$cats = $cats->get(0);
$sub_cats = $sub_cats->forget(0);
?>
<div class="container">
	<div class="category-links">
		<ul>
			@if (isset($sub_cats) and !empty($sub_cats) and isset($cat) and !empty($cat))
				@if ($sub_cats->has($cat->tid))
					@foreach ($sub_cats->get($cat->tid) as $sub_cat)
						<li>
							<a href="{{ lurl(trans('routes.v-search-subCat', ['countryCode' => $country->get('icode'), 'catSlug' => $cat->slug, 'subCatSlug' => $sub_cat->slug])) }}">
								{{ $sub_cat->name }}
							</a>
						</li>
					@endforeach
				@endif
			@else
				@if (isset($cats) and !empty($cats))
					@foreach ($cats as $categorie)
						<li>
							<a href="{{ lurl(trans('routes.v-search-cat', ['countryCode' => $country->get('icode'), 'catSlug' => $categorie->slug])) }}">
								{{ $categorie->name }}
							</a>
						</li>
					@endforeach
				@endif
			@endif
		</ul>
	</div>
</div>