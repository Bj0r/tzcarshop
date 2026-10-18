@extends('layouts.app')
@section('content')
<x-page-header pagetitle="Dashboard" class="bg-dark"/>
<div class="wrapper wrapper-content">
<div class=" animated fadeInRightBig">
<x-widget>
<x-slot name="w1">
<h1 class="m-xs">Run</h1>
<h3 class="font-bold no-margins">
Notification

</h3>
<small>We detected an error.</small>
</x-slot>
<x-slot name="w2">
<h1 class="m-xs">520</h1>
<h3 class="font-bold no-margins">

Likes

</h3>
<small>Emojis.</small>
</x-slot>
<x-slot name="w3">
<h1 class="m-xs">Warning</h1>
<h3 class="font-bold no-margins">

Do

</h3>
<small>Something right.</small>
</x-slot>
</x-widget>
<x-box>
<x-slot name="boxtitle">
<h5>Report for 2024</h5>
<small>Company Financial Status</small>
</x-slot>
<x-slot name="boxcontent">
<h5>Report for 2024</h5>
<small>Company Financial Status</small>
</x-slot>
<x-slot name="boxfooter">
<button type="button" class="btn btn-success">
Save
</button>
</x-slot>
</x-box>
</div>
</div>
@endsection