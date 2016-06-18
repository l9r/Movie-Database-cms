@foreach (isset($items) ? $items : $headerMenu['items'] as $item)
    @if ($item['type'] === 'link')

        @if (isset($item['partial']))
            <li class="dropdown yamm-fw">
                @include('Partials.Menus.Link', array('item' => $item))
                @include('Partials.Menus.Dropdowns.'.$item['partial'])
            </li>
         @elseif (isset($item['children']) && $item['children'])                  
            <li class="dropdown simple-dropdown">
                @include('Partials.Menus.Link', array('item' => $item))
                <ul class="dropdown-menu">
                    @foreach ($item['children'] as $child)
                        <li> @include('Partials.Menus.Link', array('item' => $child))</li>
                    @endforeach
                </ul>           
            </li>
        @else
            <li><a {{ $item['visibility'] == 'admin' ? 'data-bind="visible: app.display_name()"' : '' }} href="{{ $item['action'] }}">{{ $item['label'] }}</a></li>
        @endif  
    
    @elseif ($item['type'] === 'route')

        @if (isset($item['partial']))
            <li class="dropdown yamm-fw">
                @include('Partials.Menus.Link', array('item' => $item))
                @include('Partials.Menus.Dropdowns.'.$item['partial'])
            </li>
        @elseif (isset($item['children']) && $item['children'])                  
            <li class="dropdown simple-dropdown">
               @include('Partials.Menus.Link', array('item' => $item))
                <ul class="dropdown-menu">
                   @foreach ($item['children'] as $child)
                        <li> @include('Partials.Menus.Link', array('item' => $child))</li>
                    @endforeach
                </ul>
                
            </li>
        @else
            <li> @include('Partials.Menus.Link', array('item' => $item)) </li>
        @endif

    @elseif ($item['type'] === 'page')

        @if (isset($item['partial']))
            <li class="dropdown yamm-fw">
               @include('Partials.Menus.Link', array('item' => $item))
                @include('Partials.Menus.Dropdowns.'.$item['partial'])
            </li>
        @elseif (isset($item['children']) && $item['children'])                  
            <li class="dropdown simple-dropdown">
                @include('Partials.Menus.Link', array('item' => $item))
                <ul class="dropdown-menu">
                    @foreach ($item['children'] as $child)
                        <li> @include('Partials.Menus.Link', array('item' => $child))</li>
                    @endforeach
                </ul>
                
            </li>
        @else
            <li> @include('Partials.Menus.Link', array('item' => $item)) </li>
        @endif

    @endif
@endforeach    