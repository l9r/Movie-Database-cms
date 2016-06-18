<?php $href = ''; ?>

@if ($item['type'] == 'link')
	<?php $href = $item['action'] ?>
@elseif ($item['type'] == 'page')
	<?php $href = url($item['action']) ?>
@elseif ($item['type'] == 'route')
	<?php $href = route($item['action']) ?>
@endif


<a {{ $item['visibility'] == 'admin' ? 'data-bind="visible: app.perm()"' : '' }} href="{{ $href }}"
	
	@if (isset($item['partial']) || (isset($item['children']) && $item['children']))
		class="dropdown-toggle" data-toggle="dropdown">
		{{ $item['label'] }}
		<b class="caret"></b>
	@else
		>
		{{ $item['label'] }}
	@endif

</a>