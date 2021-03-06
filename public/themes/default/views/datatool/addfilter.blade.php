<?php
    $dperiod = date('Y-m-d', time());
?>
{{Former::open_for_files_vertical(URL::to($submit_url),'GET',array('class'=>''))->id('filter-form')}}
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {{ Former::select('device','Device')
                    ->selected(Input::get('device'))
                    ->options(Prefs::getDevice()->deviceToSelection('key','identifier'))
                }}
            {{ Former::select('courier','Courier')
                    ->selected(Input::get('courier'))
                    ->options(Prefs::getCourier()->courierToSelection('id','fullname'))
                }}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {{ Former::select('merchant','Merchant')
                    ->selected(Input::get('merchant'))
                    ->options(Prefs::getMerchant()->merchantToSelection('id','merchantname' ))
                }}

            {{ Former::select('logistic','Logistic')
                    ->selected(Input::get('logistic'))
                    ->options(Prefs::getLogistic()->logisticToSelection('logistic_code','name'))
                }}

        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {{ Former::select('time-base','Time Base')
                    ->selected(Input::get('time-base'))
                    ->options(Config::get('jex.filter_time_base'))
                }}
            {{ Former::select('pending-count','Pending Count')
                    ->selected(Input::get('pending-count'))
                    ->options(Config::get('jex.pending_count_choice'))
                }}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {{ Former::text('date-from', 'From Date')
                    ->value(Input::get('date-from',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-from');
            }}
            {{ Former::text('zone','Zone')
                    ->value(Input::get('zone',''))
                    ->class('form-control input-sm')
                    ->id('zone');
                }}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {{ Former::text('date-to', 'To Date')
                    ->value(Input::get('date-to',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-to');
            }}
            {{ Former::text('city','City')
                    ->value(Input::get('city',''))
                    ->class('form-control input-sm')
                    ->id('city');
                }}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {{ Former::text('manifest-date', 'Publish Date')
                    ->value(Input::get('manifest-date',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('manifest-date');
            }}
            {{ Form::submit('Generate',array('class'=>'btn btn-sm btn-primary'))}}
            <a class="btn btn-sm" href="{{ URL::to($submit_url) }}">Reset</a>
            {{--
                Former::text('date-to', 'To')
                    ->value(Input::get('date-to',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-to');
            --}}
        </div>

    </div>

    <div class="row">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            {{ Former::text('status','Delivery Status')
                    ->value(Input::get('status', implode(',', Config::get('jex.data_tool_default_status') ) ))
                    ->class('form-control tag_status') }}
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            {{ Former::text('pickup-status','Pick Up Status')
                    ->value(Input::get('pickup-status'), array() )
                    ->class('form-control tag_pickup_status') }}
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            {{ Former::text('courier-status','Courier Status')
                    ->value(Input::get('courier-status'), array() )
                    ->class('form-control tag_courier_status') }}
        </div>
    </div>

{{--
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <h5>Account</h5>
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            {{ Former::select('acc-code-from', '')
                    ->options(Prefs::getCoa()->CoaToSelection('ACNT_CODE', 'ACNT_CODE', false ), Input::get('acc-code-from')  )
                    ->class('form-control form-white input-sm')
                    ->id('acc-code-from');
            }}

        </div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            {{ Former::select('acc-code-to', '')
                    ->options(Prefs::getCoa()->CoaToSelection('ACNT_CODE', 'ACNT_CODE', false ), Input::get('acc-code-to') )
                    ->class('form-control form-white input-sm')
                    ->id('acc-code-to');
            }}
        </div>
    </div>
    --}}


</form>

<span class="syncing" style="display:none;">Processing...</span>


<style type="text/css">

.modal.large {
    width: 80%; /* respsonsive width */
    margin-left:-40%; /* width/2) */
}

.modal.large .modal-body{
    max-height: 800px;
    height: 500px;
    overflow: auto;
}

button#label_refresh{
    margin-top: 27px;
}

button#label_default{
    margin-top: 28px;
}

</style>

<script type="text/javascript">
    $(document).ready(function(){

        $('.p-datepicker').datepicker({
            format: 'yyyy-mm-dd',
            forceParse:false
        });

        $('#company-code').on('change',function(){
            oTable.draw();
        });

        $('#assign-product').on('click',function(e){
            $('#assign-modal').modal();
            e.preventDefault();
        });

        $('#do-generate').on('click',function(){
            oTable.draw();
            e.preventDefault();
        });

        $('#acc-company').select2().on('change',function(){
            $.post('{{ URL::to('ajax/afe') }}',
                {
                    code: $('#acc-company').val()
                },
                function(data) {
                    if(data.result == 'OK'){
                        var output = '';
                        $.each(data.selection, function(key, value) {
                            output += '<option value="' + key + '" >' + value + '</option>';
                        });
                        $('#acc-afe').select2('destroy');
                        $('#acc-afe').html(output);
                        $('#acc-afe').select2();


                    }
                },'json');
        });

    });
</script>