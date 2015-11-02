@extends('layout.yukontwo')

@section('left')
        {{ Former::hidden('_id')->value($formdata['_id']) }}

        <h5>Device Information</h5>

        {{ Former::text('identifier','Device Identifier') }}
        {{ Former::text('devname','Device Name') }}
        {{ Former::text('descriptor','Description') }}
        {{ Former::text('mobile','Number') }}

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {{ Former::text('color','Color')->class('form-control') }}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {{ Former::select('is_on','Active')->options(array('1'=>'Yes','0'=>'No'))->class('form-control input-sm') }}
            </div>
        </div>

        {{ Form::submit('Save',array('class'=>'btn btn-primary'))}}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

@section('right')
        <h5>Default Node</h5>
        {{ Former::select('node_id','Node ID')->options(Prefs::getNode()->nodeToSelection('node_code','name',false) )->class('form-control input-sm') }}
        <h5>Device Coverage</h5>

        {{ Former::text('city','City Coverage')->class('form-control tag_city') }}

        {{ Former::text('district','Area Coverage')->class('form-control tag_district') }}



@stop

@section('modals')

@stop

@section('aux')
{{ HTML::style('css/summernote.css') }}
{{ HTML::style('css/summernote-bs3.css') }}

{{ HTML::script('js/summernote.min.js') }}

<script type="text/javascript">


$(document).ready(function() {


    //$('.pick-a-color').pickAColor();

    $('#name').keyup(function(){
        var title = $('#name').val();
        var slug = string_to_slug(title);
        $('#permalink').val(slug);
    });

    $('.editor').summernote({
        height:500
    });

    $('#location').on('change',function(){
        var location = $('#location').val();
        console.log(location);

        $.post('{{ URL::to('asset/rack' ) }}',
            {
                loc : location
            },
            function(data){
                var opt = updateselector(data.html);
                $('#rack').html(opt);
            },'json'
        );

    })

    $('.auto_merchant').autocomplete({
        source: base + 'ajax/merchant',
        select: function(event, ui){
            $('#merchant-id').val(ui.item.id);
        }
    });

    $('.auto_node_id').autocomplete({
        source: '{{ URL::to('ajax/nodeid' ) }}',
        select: function(event, ui){
            //$('#merchant-id').val(ui.item.id);
        }
    });

    function updateselector(data){
        var opt = '';
        for(var k in data){
            opt += '<option value="' + k + '">' + data[k] +'</option>';
        }
        return opt;
    }


});

</script>

@stop