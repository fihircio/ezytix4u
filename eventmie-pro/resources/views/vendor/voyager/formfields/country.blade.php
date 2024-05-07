<select class="form-control" 
    name="{{ $row->field }}"
    @if($row->required == 1) required @endif
>
    @if($row->required)
        <option value="">{{__('voyager::generic.none')}}</option>
    @endif

    @foreach($countries as $country)
        <option value="{{ $country->id }}" 
            @if(isset($dataTypeContent->{$row->field}))
                @if($dataTypeContent->{$row->field} == $country->id)
                {{ 'selected="selected"' }}
                @endif
            @endif
        >
            {{ $country->country_name }}
        </option>
    @endforeach
</select>