<ul id="main-menu" class="">
@foreach ($menus as $menu)
<?php $isActive = is_current_model($menu['pattern']) ?>
{{--<li @if($isActive) class="opened" @endif>--}}
<li class="opened">
  <a href="{{ !empty($menu['url']) ? url($menu['url']) : '#'}}">
    <i class="{{$menu['icon']}}"></i>
    <span>{{$menu['name']}}</span>
  </a>
  @if(!empty($menu['submenu']))
  {{--<ul @if($isActive) class="visible" @endif>--}}
  <ul class="visible">
    @foreach($menu['submenu'] as $submenu)
    <li>
      <a href="{{ url($submenu['url']) }}">
        <span>{{$submenu['name']}}</span>
      </a>
    </li>
    @endforeach
  </ul>
  @endif
</li>
@endforeach
</ul>