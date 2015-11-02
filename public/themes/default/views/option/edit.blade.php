@extends('layout.yukontwo')


@section('left')

<h3>{{$title}}</h3>

{{ Former::hidden('id')->value($formdata['_id']) }}

{{ Former::text('value', $formdata['label'] ) }}

{{ Form::submit('Save',array('class'=>'btn btn-primary'))}}&nbsp;&nbsp;
{{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

<script type="text/javascript">

$(document).ready(function() {

    $('#title').keyup(function(){
        var title = $('#title').val();
        var slug = string_to_slug(title);
        $('#permalink').val(slug);
    });

});

</script>

@stop


