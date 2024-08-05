<option>--- Select State ---</option>
@if(!empty($bodega))
  @foreach($bodega as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif