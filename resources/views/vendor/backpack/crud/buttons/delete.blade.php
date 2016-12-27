@if (
		(
			$crud->hasAccess('delete') &&
			/* Security for SuperAdmin */
			!str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'UserController')
		)
		or
		(
			/* Security for SuperAdmin */
			$crud->hasAccess('delete') &&
			str_contains(\Illuminate\Support\Facades\Route::currentRouteAction(), 'UserController') && $entry->id != 1
		)
   )
	<a href="{{ url($crud->route.'/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-button-type="delete"><i class="fa fa-trash"></i>
		{{ trans('backpack::crud.delete') }}
	</a>
@endif